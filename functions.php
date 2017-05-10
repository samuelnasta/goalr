<?php
if(isset($_SESSION['ip']) && ($_SERVER['REMOTE_ADDR'] != $_SESSION['ip'])):
	session_destroy();
	session_regenerate_id();
	echo "<p>Ui, ele está querendo me invadir! >o<<br />Mas como assim? Não vai nem pagar um jantar antes?</p>";
	die();
endif;

function login(){
	$_SESSION['time'] = time();
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}


header ('Content-type: text/html; charset=iso-8859-1');
date_default_timezone_set('America/Sao_Paulo');

//try { $db = new PDO('mysql:dbname=goal_goalr;host=localhost', 'goal', 'Hvn}a%QT!94m'); }
try { $db = new PDO('mysql:dbname=goalr;host=localhost', 'root', 'root'); }
catch(PDOException $e) { echo $e->getMessage(); }

$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 



function time_diff($created_at,$updated_at,$done){
	$last_time = $done == 1 ? $updated_at : $created_at;
	if(isset($last_time) && is_numeric($last_time)):
		$seconds_posted = time() - $last_time;
		if ($seconds_posted < 60):
			$time_ago = 'agora mesmo';
		elseif($seconds_posted < 3600):
			$time_ago = round($seconds_posted / 60) . ' minutos atrás';
		elseif($seconds_posted < 7200):
			$time_ago = round($seconds_posted / 3600) . ' hora atrás';
		elseif($seconds_posted < 86400):
			$time_ago = round($seconds_posted / 3600) . ' horas atrás';
		elseif($seconds_posted < 172800):
			$time_ago = round($seconds_posted / 86400) . ' dia atrás';
		elseif($seconds_posted > 172800):
			$time_ago = round($seconds_posted / 86400) . ' dias atrás';
		endif;

		$done_or_posted = $done == 1 ? 'Finalizado ' : 'Postado ';
		return $done_or_posted . $time_ago;
	endif;
}


function add($id, $data){
	$db = $GLOBALS['db']; $table = $GLOBALS['table']; $data = utf8_decode($data);
	$field = substr($table, 0, -1);

	if (isset($_POST['from-cat']) && $_POST['from-cat']): // If is goal
		$is_private = ($_POST['private'] == 'on') ? 1 : 0;
		$deadline = implode("-",array_reverse(explode("/",$_POST['deadline'])));
		$sql = "INSERT INTO $table (created_at, from_user, from_cat, $field, deadline, private)
				VALUES (NOW(), $id, {$_POST['from-cat']}, '$data', '$deadline', $is_private)";

	elseif (isset($_FILES['motif-image']) && $_FILES['motif-image']): // If is motif
		$sql = "INSERT INTO $table (created_at, from_user, from_goal, $field)
				VALUES (NOW(), $id, {$_POST['from-goal']}, '$data')";

	elseif (isset($_POST['from-goal']) && $_POST['from-goal']): // If is task
		$sql = "INSERT INTO $table (created_at, from_user, from_goal, $field)
				VALUES (NOW(), $id, {$_POST['from-goal']}, '$data')";
	endif;

	$db->prepare($sql)->execute();
	global $motif_id;
	$motif_id = $db->lastInsertId();
}


function convert_links($text) {
	$text = ' ' . $text;
	$text = preg_replace('/((\w+:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
						 '<a href="$1" rel="nofollow" target="_blank">$1</a>', $text);
	$text = preg_replace('/([\s*([{])([a-z0-9-]+\.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
						 '$1<a href="http://$2" rel="nofollow" target="_blank">$2</a>', $text);
	$text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})/i',
						 '<a href="mailto:$1" rel="nofollow">$1</a>', $text);
	return substr($text,1);
}

function delete($id){
	$db = $GLOBALS['db']; $table = $GLOBALS['table'];
	$sql = "DELETE FROM $table
			WHERE id = $id";
	$db->exec($sql);

	if($table == 'motifs'):
		$path = "{$_SERVER['DOCUMENT_ROOT']}/uploads/{$id}.jpg";
		if(file_exists($path)):
			unlink($path);
		endif;
	endif;
}

function finish($id, $flag){
	$db = $GLOBALS['db']; $table = $GLOBALS['table'];
	$sql = "UPDATE $table
			SET done = $flag
			WHERE id = $id";
	$db->exec($sql);
}

function remove_accents($string, $is_underscore = 0) {
	$string = strtolower(preg_replace('/\s[\s]+/',' ',$string)); // Strip off multiples spaces
	$string = strtr($string, 'åáàãâéêèíïóôõöøºúüûùç', 'aaaaaeeeiioooooouuuuc'); // Convert accents
	$string = preg_replace('/[^A-Za-z0-9.-_ ]/', '', $string); // Remove any other special char
	if($is_underscore) $string = str_replace(' ','-',$string);
	return $string;
}

function show_reminder(){
	if(isset($_SESSION['id']) && $_SESSION['id']):
		$db = $GLOBALS['db'];
		$sql = "SELECT task
				FROM tasks
				WHERE from_user = {$_SESSION['id']} AND done = 0
				ORDER BY RAND()
				LIMIT 1";
		$rs = $db->query($sql)->fetch();
		if($rs):
			echo "<p>{$rs['task']}</p>";
		else:
			echo "<p>Dica do dia!</p>";
		endif;
	endif;
}

function update($update, $id){
	$db = $GLOBALS['db']; $table = $GLOBALS['table'];
	$field = substr($table,0,-1);
	$update = utf8_decode($update);
	$sql = "UPDATE $table
			SET $field = '$update'
			WHERE id = $id";
	$db->exec($sql);
}