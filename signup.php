<?php
session_start();
include_once('functions.php');

// Verify if username is available
if(isset($_POST['verify']) && $_POST['verify']):
	$verify = remove_accents(utf8_decode($_POST['verify']),1);
	echo 'Verificando disponibilidade do endereço: ' . $verify;
	$rs = $db->query("SELECT COUNT(1) as is_valid FROM users WHERE user = '$verify'")->fetch();
	if($rs['is_valid'] == 0):
		echo '<div class="ok">Tudo certo! Usuário disponível!</div>';
	else:
		echo '<div class="error">Ops! Infelizmente alguém chegou antes e pegou esse endereço. Tente outro!</div>';
	endif;
	$db = NULL;
	die();
endif;





if(isset($_POST['email']) && $_POST['email']):

	// Record user info
	$now = date("Y-m-d H:i:s");
	$user = remove_accents($_POST['user'],1);
	$salt = hash('sha256', $_POST['email'] . $now);
	$pwd = hash('sha256', $_POST['pwd'] . $salt);
	$db->prepare("INSERT INTO users(user, created_at, email, pwd, salt)
				  VALUES ('$user', '$now', '{$_POST['email']}', '$pwd', '$salt')")
				  ->execute();

	// Remember user id
	$from_user = $db->lastInsertId();
	if(!$db->lastInsertId()):
		$rs = $db->query("SELECT id FROM users WHERE user = '{$_POST['user']}'")->fetch();
		$from_user = $rs['id'];
	endif;

	// Record goal
	$db->prepare("INSERT INTO goals(from_user,goal)
				  VALUES ($from_user, '{$_POST['goal']}')")
				  ->execute();

	// Remember goal id
	$from_goal = $db->lastInsertId();
	if(!$db->lastInsertId()):
		$rs = $db->query("SELECT id FROM goals WHERE goal = '{$_POST['goal']}' AND user = $from_user")->fetch();
		$from_goal = $rs['id'];
	endif;
	
	// Record task
	$db->prepare("INSERT INTO tasks(from_user,from_goal,task)
				  VALUES ($from_user, $from_goal, '{$_POST['task']}')")
				  ->execute();


	// Record motivation
	$db->prepare("INSERT INTO motifs(from_user,from_goal,motif)
				  VALUES ($from_user, $from_goal, '{$_POST['motif']}')")
				  ->execute();


	// Remember motif id
	$motif_id = $db->lastInsertId();
	if(!$db->lastInsertId()):
		$rs = $db->query("SELECT id FROM motifs WHERE motif = '{$_POST['motif']}' AND user = $from_user")->fetch();
		$motif_id = $rs['id'];
	endif;


	if(isset($_POST['motif_image']) && $_POST['motif_image']):
		include('image_upload_script.php');
		$target_file = "uploads/$fileName";
		$resized_file = "uploads/$fileName";
		ak_img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	endif;
?>

	<h1>Cadastro realizado com sucesso!</h1>
	<a href="/">Voltar</a>

<?
	die();
endif;

?>

<form action="signup" enctype="multipart/form-data" method="post">
	<label for="user">Vou compartilhar com meus amigos através do</label><br/>www.goalr.us/<input id="user" maxlength="32" name="user" type="text" />
	<div id="user_exists"></div>
	<label for="email">Email</label><input id="email" name="email" type="email" />
	<label for="pwd">Senha</label><input id="pwd" maxlength="32" name="pwd" type="password" />	
	<label for="goal">Meta</label><input id="goal" name="goal" type="text" />
	<label for="task">Primeira tarefa</label><input id="task" name="task" type="text" />
	<label for="motif">Primeira motivação</label><textarea id="motif" name="motif" /></textarea>
	<input id="motif_image" name="motif_image" type="file" />
	
	<input type="submit" />
</form>

<script type="text/javascript">
$(document).ready(function() {

	$('#user').focus();
	$('#user').change(function(){
		$.ajax({
			type: 'POST',
			data: { 'verify' : $('#user').val() },
			url: 'signup',
			success: function(data){ $('#user_exists').html(data); }
		});
	});
});
</script>


<?php if (isset($db) && $db) $db = NULL; ?>