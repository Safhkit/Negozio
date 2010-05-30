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
	echo 'Oppure visualizza il <a href="carrello.php">carrello</a>';
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
		
		if ($num == 0)
			die('Impossibile selezionare 0 pezzi');
		
		$user = 'user';
		$password = 'password';
		$link = mysql_connect('localhost', $user, $password);
		
		//prodotti deve essere aggiornato e disponibili deve rimanere consistente, lock in scrittura
		//su prenotazioni si fa lettura/scrittura, necessario lock in scrittura
		$query = 'LOCK TABLES negozio.prodotti WRITE, negozio.prenotazioni WRITE';
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
				//prenotazione non esisteva (il lock in scrittura assicura che nel frattempo non venga inserita una)
				$query = "INSERT INTO negozio.prenotazioni
					values(".$id.", '".$_SESSION['user']."', ".$num.", DATE_ADD(NOW(), INTERVAL 1 HOUR));";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
			}
			else {
				//aggiungere alla prenotazione e rinnovare la scadenza (lock in scrittura garantisce che non venga mod. entry)
				$query = "UPDATE negozio.prenotazioni
						SET pezzi = pezzi +".$num.", scadenza = DATE_ADD(NOW(), INTERVAL 1 HOUR)
						WHERE prod_id = ".$id." and user_id = '".$_SESSION['user']."';";
				$result = mysql_query($query, $link);
				if (!$result)
					die ('Invalid query: ' . mysql_error());
			}
			
			//decremento dei pezzi prenotati da prodotti
			//il lock garantisce che non sia stato modificato il valore letto all'inizio (non si può avere disp<0)
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
			
		mysql_close ($link);
		?>
		</div>
	</body>
</html>
