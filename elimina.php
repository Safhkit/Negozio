<?php
include_once('login.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Negozio virtuale</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
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
//TODO: probabilmente lock su prenotazioni non serve: ci si prende il numero di oggetti da rimettere disp,
//può cambiare se la prenotazione scade o se l'utente (lo stesso) lo aumenta, e ci si cancella
//la entry (unica se esiste) della prenotazione. Per la lettura di pezzi, nel primo caso (che si può
//verificare comunque, anche avendo il lock, leggi commento sotto), la query di aggiornamento non modifica niente, 
//nel secondo, correttamente viene incrementato disponibili di tutti i pezzi che erano prenotati.
//Possibile caso da considerare: l'utente è loggato da due postazioni (!) da una parte fa aggiungi e dall'altra fa
//elimina: per non eliminare anche i nuovi pezzi prenotati sarebbe necessario il lock, ma comunque solo in lettura.
//problema, se non si locka in scrittura, si rischia di eliminare una prenotazione appena creata
$query = "LOCK TABLES negozio.prenotazioni WRITE, negozio.prodotti WRITE;";
//$query = "LOCK TABLES negozio.prodotti WRITE;";
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

//ripristino delle quantità dell'oggetto eliminato, se la prenotazione è stata cancellata
//da quando l'utente ha premuto "ELIMINA" a ora, semplicemente non vengono aggiunti pezzi
//(la routine di cancellazione in scheda.php lo ha già fatto)
$query = 'UPDATE negozio.prodotti, (
			SELECT pezzi from negozio.prenotazioni
			WHERE prod_id = '.$id.' and user_id = "'.$_SESSION["user"].'") as P
			SET disponibili = disponibili + P.pezzi
			WHERE id = '.$id.';';
$result = mysql_query($query, $link);
if (!$result)
	die ('Invalid query: ' . mysql_error());

//la prenotazione potrebbe non esserci più se nel è andata in esecuzione la routine di pulizia
//prima che l'utente premesse ELIMINA. La query semplicemente non ha effetto.
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
