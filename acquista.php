<?php
include_once('login.php');
?>
<?php
function formConferma($id, $num, $l)
{
	$query = "SELECT * FROM negozio.prodotti
						WHERE id =" .$id. ";";

	$result = mysql_query($query, $l);
	if (!$result)
		die ('Invalid query: ' . mysql_error());
	$row = mysql_fetch_assoc($result);
		
	echo "Riepilogo: <br />";
	echo "Prodotto: ".$row['nome']."<br />";
	echo "Quantità: ".$num."<br />";
	echo "Prezzo totale: ".$row['prezzo']." x " .$num. " = " .$row['prezzo'] * $num ."<br />";
	echo "<form name=\"Conferma\" action=\"pagamento.php\" method=\"get\" >";
	echo "<input type=\"hidden\" name=\"id\" value=\"".$id."\" />";
	echo "<input type=\"hidden\" name=\"num\" value=\"".$num."\" />";
	echo "<input type=\"submit\" value=\"Conferma Pagamento\" />";
	echo "</form>";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Negozio Virtuale</title>
	</head>
	<body>
		<div>
		<?php
		$id = $_GET['id'];
		$num = $_GET['num'];
		
		$user = 'user';
		$password = 'password';
		$link = mysql_connect('localhost', $user, $password);
				
		//lock su prenotazioni perché un altro utente potrebbe leggere le stesse entry da
		//cancellare e ci potrebbero essere corse sulla cancellazione, su prodotti
		//perché si modifica la disponibilità e non si vuole che qualche utente
		//la legga in stato inconsistente
		$query = "LOCK TABLES negozio.prenotazioni WRITE, negozio.prodotti WRITE;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		//cancellazione entry scadute e ripristino delle disponibilità in prodotti
		$now = time();
		$query = "update negozio.prodotti, (
				select prod_id, sum(pezzi) as somma
				from negozio.prenotazioni
				where scadenza < FROM_UNIXTIME(".$now.")
				group by prod_id ) as T
				set negozio.prodotti.disponibili = negozio.prodotti.disponibili + T.somma
				where negozio.prodotti.id = T.prod_id;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
			
		$query = "DELETE FROM negozio.prenotazioni 
				WHERE scadenza < FROM_UNIXTIME(".$now.");";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
			
		//controllo per la quantità richiesta dall'utente
		$query = "SELECT disponibili FROM negozio.prodotti
				WHERE id =". $id;
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		$row = mysql_fetch_assoc($result);
		
		//la disponiblità vista dall'utente potrebbe essere cambiata
		if ($num <= $row["disponibili"]) {
			//se la prenotazione esiste già, si incrementa pezzi
			//altrimenti si inserisce la nuova prenotazione.
			$query = "SELECT * FROM negozio.prenotazioni
					where user_id = '" .$_SESSION['user']. "' and prod_id = ".$id.";";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());
			
			$row = mysql_fetch_assoc($result);
			if (!$row) {
				//prenotazione non esisteva
				$query = "INSERT INTO negozio.prenotazioni
					values(".$id.", '".$_SESSION['user']."', ".$num.", DATE_ADD(NOW(), INTERVAL 1 HOUR));";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
			}
			else {
				//aggiungere alla prenotazione e rinnovare la scadenza
				$query = "UPDATE negozio.prenotazioni
						SET pezzi = pezzi +".$num.", scadenza = DATE_ADD(NOW(), INTERVAL 1 HOUR)
						WHERE prod_id = ".$id." and user_id = '".$_SESSION['user']."';";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
			}
			
			//decremento dei pezzi prenotati da prodotti
			$query = "update negozio.prodotti
					set negozio.prodotti.disponibili = negozio.prodotti.disponibili - ".$num."
					where negozio.prodotti.id = ".$id.";";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());
			
			formConferma($id, $num, $link);
		}
		else {
			$query = "UNLOCK TABLES;";
			$result = mysql_query($query, $link);
			if (!$result)
				die ('Invalid query: ' . mysql_error());
			die("Quantità richiesta non disponibile");
		}
		
		$query = "UNLOCK TABLES;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		mysql_free_result($result);
		mysql_close ($link);
		?>
		</div>
	</body>
</html>
