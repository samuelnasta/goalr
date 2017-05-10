<?php
session_start();
include_once('functions.php');
?>


<div id="blur"></div>


<?php
$table = 'goals';
// Insert goal
if(isset($_POST['add']) && $_POST['add']) add($_SESSION['id'], $_POST['add']);

// Delete goal
if(isset($_POST['del']) && $_POST['del']) delete($_POST['del']);

// Set this goal as done
if(isset($_POST['done']) && $_POST['done']) finish($_POST['id'],$_POST['done']);

// Update goal
if(isset($_POST['update']) && $_POST['update']) update($_POST['update'],$_POST['id']);


// Show goals
$sql = "SELECT id, UNIX_TIMESTAMP(created_at) as created_at, UNIX_TIMESTAMP(updated_at) as updated_at, goal, done
		FROM goals
		WHERE from_user = {$_SESSION['id']}
		ORDER BY done, created_at ASC";

	foreach ($db->query($sql) as $row) {
		$strike = ''; $checkbox = '';
		if ($row['done'] == 1): $strike = 'class="strike"'; $checkbox = 'checked="true"'; endif;
		$goal_label = ($row['done'] == 1) ? $row['goal'] : '<a href="javascript:show_tasks(' . $row['id'] . ');">' . $row['goal'] . '</a>';
		$time_diff = time_diff($row['created_at'],$row['updated_at'],$row['done']);
	
		echo <<<EOL
		<li>
			<p>
				<a href="javascript:;" class="edit" title="Editar" value="{$row['id']}">Editar</a>
				<input type="checkbox" name="{$row['id']}" id="goal-{$row['id']}" $checkbox title="Marcar como realizado" />
				<label $strike>$goal_label</label><span>- $time_diff</span>
				<a href="javascript:;" class="delete" confirm="{$row['goal']}" id="del-goal-{$row['id']}" title="Apagar" value="{$row['id']}">Apagar</a>
			</p>
		</li>
EOL;
	}
?>



<?php $rs = $db->query("SELECT COUNT(1) as total_goals FROM goals WHERE from_user = {$_SESSION['id']} AND done = 0")->fetch();
	if($rs['total_goals'] < 3): ?>


	<div id="add">
		<input id="new-goals" name="new-goals" type="text" />
		<select id="from-cat" name="from-cat">
			<option disabled="disabled">- Selecione uma categoria -</option>
			<?php
			$sql = "SELECT id, category_pt as category
					FROM categories
					ORDER BY category_pt";
			
				foreach ($db->query($sql) as $row) {
				$is_selected = ($row['id'] == 1) ? 'selected="selected"' : '';
					echo <<<EOL
						<option value="{$row['id']}" $is_selected>{$row['category']}</option>
EOL;
				}
			?>
		</select>
		<input type="checkbox" id="private" name="private" title="Essa meta é privada, só eu poderei ver suas tarefas e motivações. Consequentemente, ninguém poderia enviar cartões." />
		<label for="private"><img alt="Meta privada" src="/images/private.png" height="16" title="Essa meta é privada, só eu poderei ver suas tarefas e motivações. Consequentemente, ninguém poderia enviar cartões." width="16" /></label>


<input type="text" name="deadline" id="deadline" size="10" maxlength="10"/>


		<a href="javascript:;" id="add-goals" type="submit">Adicionar</a>
	</div>

<?php else: ?>

	<h4>Limite de metas atingido.</h4>
	<p>Pra não sobrecarregar, que tal focar mais em concluir as anteriores? ;)</h2>

<?php endif; ?>


<script type="text/javascript">
$(document).ready(function() {

	page = 'goals';
	edit();
	toggle_done();
	$('#content li:even').css('background-color', 'rgba(255,255,255,0.2');
	$('.delete').click(function(event){ del(this); });
	$('#add-' + page).click(function(){ add(); });
	$('#add').bind('keypress', function(event) { if(event.keyCode == 13) { add(); } });


$('#deadline').focus(function(){
	$(this).calendario({
		minDate:'<?php echo date("d/m/Y"); ?>',
		target:'#deadline'
	});
});

});
</script>

<?php if (isset($db) && $db) $db = NULL; ?>