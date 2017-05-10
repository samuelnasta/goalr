<?php
session_start();
ob_start();
include_once('functions.php');
?>


<div id="blur"></div>


<?php
$table = 'motifs';
// Insert motif
if(isset($_POST['add']) && $_POST['add']) add($_SESSION['id'], $_POST['add']);

// Delete motif
if(isset($_POST['del']) && $_POST['del']) delete($_POST['del']);

// Update task
if(isset($_POST['update']) && $_POST['update']) update($_POST['update'],$_POST['id']);

// Show motifs list
$sql = "SELECT goal, id
		FROM goals
		WHERE from_user = {$_SESSION['id']} AND done = 0
		LIMIT 3";
	$rs = $db->query($sql);
	$goal = ($_POST['goal']) ? $_POST['goal'] : $_COOKIE['goalr'];

	echo '<ul class="tabs" id="motifs_tabs">';

	if (!isset($goal)) $is_active = ' class="active"';
	foreach ($rs as $row) {
		$goal_id = ($goal_id == '') ? $row['id'] : $goal_id;
		$is_active = ($goal == $row['id']) ? ' class="active"' : $is_active;
		if($is_active != '') setcookie('goalr',$row['id'],time()+3600,'/');

		echo <<<EOL
			<li><a href="javascript:show_motifs({$row['id']});" $is_active>{$row['goal']}</a></h3></li>
EOL;
		$is_active = '';
	}
	echo '</ul>';

$goal_id = ($goal) ? $goal : $goal_id;
ob_end_flush();


// Show motifs
$sql = "SELECT id, motif
		FROM motifs
		WHERE from_user = {$_SESSION['id']} AND from_goal = {$goal_id}
		ORDER BY motif ASC
		LIMIT 10";
	$rs = $db->query($sql);

	foreach ($rs as $row):
		$image = '';
		$path = "{$_SERVER['DOCUMENT_ROOT']}/uploads/{$row['id']}.jpg";
		if(file_exists($path)):
			$image = <<<EOL
			<a href="/uploads/{$row['id']}.jpg" class="image" id="image-{$row['id']}" title="Ver imagem" target="_blank" value="{$row['id']}">Ver imagem</a>
EOL;
		endif;



$str = convert_links($row['motif']);
		echo <<<EOL
		<li>
			<p>
				<a href="javascript:;" class="edit" title="Editar" value="{$row['id']}">Editar</a>
				<label for="motif-{$row['id']}">{$str}</label>
				$image
				<a href="javascript:;" class="delete" confirm="{$row['motif']}" id="del-motif-{$row['id']}" title="Apagar" value="{$row['id']}">Apagar</a>
			</p>
		</li>
EOL;
	endforeach;

	if(isset($_FILES['motif-image']) && $_FILES['motif-image']):
	echo "Tentativa de Upload de imagem";
		include('image_upload_script.php');
		$target_file = "uploads/$fileName";
		$resized_file = "uploads/$fileName";
		ak_img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	endif;
?>

<form action="<?php echo $_SESSION['user']; ?>/motifs" enctype="multipart/form-data" method="post">
	<textarea id="new-motifs" name="add" type="text"></textarea>
	<select id="from-goal" name="from-goal">
	<?php
	$sql = "SELECT id, goal
			FROM goals
			WHERE from_user = {$_SESSION['id']} AND done = 0";
	
		foreach ($db->query($sql) as $row) {
			$is_selected = ($goal_id == $row['id']) ? 'selected="selected"' : '';
			echo <<<EOL
				<option value="{$row['id']}" $is_selected>{$row['goal']}</option>
EOL;
		}
	?>
	
	</select>
	<input id="add-motifs" type="submit" value="Adicionar" />
	<input id="motif-image" name="motif-image" type="file" />
</form>

<script type="text/javascript">
$(document).ready(function() {

	page = 'motifs';
	edit();
	$('#content li:even').css('background-color', 'rgba(255,255,255,0.2');
	$('.delete').click(function(event){ del(this); });
	//$('#add-' + page).click(function(){ add(); });
	$('#new-' + page).bind('keypress', function(event) { if(event.keyCode == 13) { add(); } });
});
</script>

<?php if (isset($db) && $db) $db = NULL; ?>