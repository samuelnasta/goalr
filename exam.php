<html>
<head>
       <title>Desafio 2</title>
</head>

<?php

// Preparing to go to a new branch : "Exam"


if (isset($_GET['numero'])):
	$num = $_GET['numero'];
	while ($num != 1):
		$num = $num & 1 ? ($num * 3) + 1 : $num/2;
		echo $num . '<br />';
	endwhile;

else:
?>
<body>
<form action="" method="get">
<input type="text" name="numero"/>
<input type="submit" value="enviar">
</form>
</body>
</html>
<?php endif; ?>