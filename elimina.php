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
$query = "LOCK TABLES negozio.prenotazioni WRITE, negozio.prodotti WRITE;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

//ripristino delle quantità dell'oggetto eliminato, se la prenotazione è stata cancellata
//da quando l'utente ha premuto "ELIMINA" a ora, semplicemente non vengono aggiunti pezzi
//(la routine di cancellazione in acquista.php lo ha già fatto)
$query = 'UPDATE negozio.prodotti, (
			SELECT pezzi from negozio.prenotazioni
			WHERE prod_id = '.$id.' and user_id = "'.$_SESSION["user"].'") as P
			SET disponibili = disponibili + P.pezzi
			WHERE id = '.$id.';';
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

//la prenotazione potrebbe non esserci più se nel frattempo è andata
//in esecuzione la routine di pulizia. La query semplicemente non ha effetto.
$query = "DELETE FROM negozio.prenotazioni
		WHERE user_id = '".$_SESSION['user']."' and prod_id=".$id.";";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

$query = "UNLOCK TABLES;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());
	
//utile solo se il javascript è disabilitato
echo "Prenotazione eliminata con successo.";
?>

<script type="text/javascript">
location.replace("carrello.php");
</script>
	
</body>
</html>
