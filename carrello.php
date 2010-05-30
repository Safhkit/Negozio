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

	//TODO: verificare problema consistenza
	/*$query = "LOCK TABLES negozio.prenotazioni READ;";
	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error());*/

	//mostra tutte le prenotazioni dell'utente loggato
	//non necessario lock perché tanto quando l'utente usa ELIMINA o PAGA dal carrello,
	//viene sempre riverificata la presenza della entry.
	$query = "SELECT id, nome, pezzi, prezzo, scadenza FROM negozio.prodotti, negozio.prenotazioni
				WHERE ((user_id = '".$_SESSION['user']."') AND (id = prod_id))";

	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error() );

	//TODO: verificare problema consistenza
	/*$query = "UNLOCK TABLES;";
	$result = mysql_query($query, $link);
	if (!$result)
		die ('Invalid query: ' . mysql_error());*/

	//create a table to show results
	//header
	echo "<table border=\"1\"><TR><TH>";
	echo 'nome prodotto' . "</TH><TH>" . 'pezzi prenotati' . "</TH><TH>";
	echo 'prezzo unitario' . "</TH><TH>" . 'prezzo totale' . "</TH><TH>" . 'scadenza';
	echo "</TH></TR>";

	//data
	while ($row = mysql_fetch_assoc($result)) {
		echo "<TR><TD>";
		echo $row['nome'];
		echo "</TD><TD>";
		echo $row['pezzi'];
		echo "</TD><TD>";
		echo $row['prezzo'];
		echo "</TD><TD>";
		echo $row['prezzo']*$row['pezzi'];
		echo "</TD><TD>";
		echo $row['scadenza'];
		echo "</TD><TD>";

		echo "<form name=\"Elimina\" action=\"elimina.php\" method=\"get\">
				<input type=\"hidden\" name=\"id\" value=\"" .$row['id']. "\">
				<input type=\"submit\" value=\"Elimina\">
			</form>";
		echo "</TD><TD>";
		echo "<form name=\"Paga\" action=\"pagamento.php\" method=\"get\">
				<input type=\"hidden\" name=\"id\" value=\"" .$row['id']. "\">
				<input type=\"hidden\" name=\"num\" value=\"".$row['pezzi']."\" />
				<input type=\"submit\" value=\"Paga\">
			</form>";
		echo "</TD></TR>";
	}
	echo "</table>";

	mysql_close ($link);
	?>
	</body>
</html>


