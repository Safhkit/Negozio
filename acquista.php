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
		
		//cancellazione entry scadute		
		//lock su prenotazioni perché un altro utente potrebbe leggere le stesse entry da
		//cancellare e ci potrebbero essere corse sulla cancellazione, su prodotti
		//perché si modifica la disponibilità e non si vuole che qualche utente
		//la legga in stato inconsistente
		$query = "LOCK TABLES negozio.prenotazioni WRITE, negozio.prodotti WRITE;";
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
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
			
		$query = "SELECT disponibili FROM negozio.prodotti
							WHERE id =". $id;
		//TODO: mettere in una funzione del tipo fai_query($query, $link) e ritorna $result
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error());
		
		$row = mysql_fetch_assoc($result);
		if ($num <= $row["disponibili"]) {
			//TODO: proseguire da qui
			//inserimento in prenotazioni
			//per l'inserimento in scadenza: DATE_ADD(NOW(), INTERVAL 1 HOUR)
			//modifica in prodotti
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

		//transazione con la banca
		?>
		</div>
	</body>
</html>
