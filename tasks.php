<?php
session_start();
ob_start();
include_once('functions.php');
?>


<div id="blur"></div>


<?php
$table = 'tasks';
// Insert task
if(isset($_POST['add']) && $_POST['add']) add($_SESSION['id'], $_POST['add']);

// Delete task
if(isset($_POST['del']) && $_POST['del']) delete($_POST['del']);

// Set this task as done
if(isset($_POST['done']) && $_POST['done']) finish($_POST['id'],$_POST['done']);

// Update task
if(isset($_POST['update']) && $_POST['update']) update($_POST['update'],$_POST['id']);


// Show task list
$sql = "SELECT goal, id
		FROM goals
		WHERE from_user = {$_SESSION['id']} AND done = 0
		LIMIT 3";
	$rs = $db->query($sql);
	$goal = ($_POST['goal']) ? $_POST['goal'] : $_COOKIE['goalr'];

	echo '<ul class="tabs" id="task_tabs">';

	if (!isset($goal)) $is_active = ' class="active"';
	foreach ($rs as $row) {
		$goal_id = ($goal_id == '') ? $row['id'] : $goal_id;
		$is_active = ($goal == $row['id']) ? ' class="active"' : $is_active;
		if($is_active != '') setcookie('goalr',$row['id'],time()+3600,'/');

		echo <<<EOL
			<li><a href="javascript:show_tasks({$row['id']});" $is_active>{$row['goal']}</a></h3></li>
EOL;
		$is_active = '';
	}
	echo '</ul>';

$goal_id = ($goal) ? $goal : $goal_id;
ob_end_flush();


// Show tasks
$sql = "SELECT id, UNIX_TIMESTAMP(created_at) as created_at, UNIX_TIMESTAMP(updated_at) as updated_at, from_goal, task, done
		FROM tasks
		WHERE from_user = {$_SESSION['id']} AND from_goal = {$goal_id}
		ORDER BY done, created_at ASC";
	$rs = $db->query($sql);
	
	foreach ($rs as $row) {
		$strike = ''; $checkbox = '';
		if ($row['done'] == 1): $strike = 'class="strike"'; $checkbox = 'checked="true"'; endif;
		$time_diff = time_diff($row['created_at'],$row['updated_at'],$row['done']);

		echo <<<EOL
		<li>
			<p>
				<a href="javascript:;" class="edit" title="Editar" value="{$row['id']}">Editar</a>
				<input type="checkbox" name="{$row['id']}" id="task-{$row['id']}" $checkbox title="Marcar como realizado" />
				<label $strike>{$row['task']}</label><span>- $time_diff</span>
				<a href="javascript:;" class="delete" confirm="{$row['task']}" from-goal="{$row['from_goal']}" id="del-task-{$row['id']}" title="Apagar" value="{$row['id']}">Apagar</a>
			</p>
		</li>
EOL;
	}
?>

	<input id="new-tasks" name="new-tasks" type="text" />
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

<a href="javascript:;" id="add-tasks" type="submit">Adicionar</a>

<script type="text/javascript">
$(document).ready(function() {

	page = 'tasks';
	edit();
	toggle_done();
	$('#content li:even').css('background-color', 'rgba(255,255,255,0.2');
	$('.delete').click(function(event){ del(this); });
	$('#add-' + page).click(function(){ add(); });
	$('#new-' + page).bind('keypress', function(event) { if(event.keyCode == 13) { add(); } });

});
</script>

<?php if (isset($db) && $db) $db = NULL; ?>