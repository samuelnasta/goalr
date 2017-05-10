<?php
error_reporting(E_ALL | E_STRICT);
ini_set('session.use_only_cookies', 1);
ini_set('display_errors', 1);

session_start();
ob_start();
include_once('functions.php');

print_r($_COOKIE);

// Login
if(isset($_POST['login-user']) && $_POST['login-user'] && isset($_POST['login-pwd']) && $_POST['login-pwd']):
	$user = strtolower($_POST['login-user']);
	$rs = $db->query("SELECT id, user, salt, pwd
					  FROM users
					  WHERE user = '$user'
					  LIMIT 1")->fetch();

	$pwd = hash('sha256', $_POST['login-pwd'] . $rs['salt']);

	if($pwd == $rs['pwd']):
		echo '<div id="logged">Estamos logados, capitão!</div>';
		$_SESSION['id'] = $rs['id'];
		$_SESSION['user'] = $rs['user'];
		$_SESSION['salt'] = $rs['salt'];
		login();
	else:
		echo '<div class="error"><h3>Sem sucesso!</h3>
			  <p>Muita calma nessa hora!<br />
			  Veja se não digitou nada errado (ou se o caps lock está ligado).</p>
			  </div>';
	endif;
endif;

// Logout
if(isset($_GET['logout'])):
    $_SESSION = array();
    setcookie('goalr','');
    session_destroy();
endif;

ob_end_flush();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"xml:lang="pt-BR" lang="pt-BR">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Crie metas, estabeleça tarefas, adicione motivadores e receba incentivo dos amigos. Goal R Us</title>
	<link rel="stylesheet" href="goalr.css" type="text/css" />
</head>

<body>

<?php if(isset($_SESSION['id']) && ($_SESSION['id'])): ?>
	<div id="reminder">
		<a href="index.php?logout" id="logout">Logout</a>
		<?php show_reminder(); ?>
	</div>
<?php else: ?>

	<a href="javascript:;" id="link-signup">Sign up</a>
	<h3><a id="button-login" href="javascript:;">Login</a></h3>
	<div id="login">
		<form action="index.php" method="post">
			<label for="login-user">Usuário</label><input id="login-user" maxlength="32" name="login-user" type="text" />
			<label for="login-pwd">Senha</label><input id="login-pwd" maxlength="32" name="login-pwd" type="password" />
			<input type="submit" />
		</form>
	</div>

<?php endif; ?>



<div id="header">
	<h1><a href="/index.php">Goal R Us</a></h1>
	<h2>Crie metas, estabeleça tarefas, adicione motivadores e receba incentivo dos amigos.</h2>
</div>


<div id="panel">
	<a href="javascript:;" id="link-goals">Metas</a>
	<a href="javascript:;" id="link-tasks">Tarefas</a>
	<a href="javascript:;" id="link-motifs">Motivações</a>
	<a href="javascript:;" id="link-cards">Cartões</a>
	<a href="javascript:;" id="link-settings">Configurações</a>

	<a href="inspire" id="inspire">Inspire me</a>
	
	<div id="content"></div>
</div>



<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>-->
<script src="jquery.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {

	username = '<?php if(isset($_SESSION['user']) && $_SESSION['user']) echo $_SESSION['user']; ?>';
	show_page('goals'); show_page('tasks'); show_page('motifs'); show_page('cards'); show_page('settings'); show_page('signup');
	$('#button-login').click(function(){ $('#login').slideToggle('slow'); $('#login-user').focus(); });
	$('#logged').delay(5000).fadeOut('slow', function() { $('#logged').remove(); });

});
</script>
</body>
</html>
<?php if (isset($db) && $db) $db = NULL; ?>