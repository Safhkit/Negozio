<?php
include_once('login.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Negozio virtuale</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.18" />
</head>

<body>

<?php
$user = 'user';
$password = 'password';
$link = mysql_connect('localhost', $user, $password);
if (!$link)
	die ('Could not connect: ' . mysql_error() );

$id = $_GET['id'];

//Necessario lock in scrittura perché potrebbe essere effettuato contemporanemente un pagamento
$query = "LOCK TABLES negozio.prenotazioni WRITE;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

$query = "DELETE FROM negozio.prenotazioni
		WHERE user_id = ".$_SESSION['user']." and prod_id=".$id.";";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

$query = "UNLOCK TABLES;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());
?>
	
</body>
</html>
