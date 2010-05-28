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
		$user = 'user';
		$password = 'password';
		$link = mysql_connect('localhost', $user, $password);
		
		//cancellazione entry scadute		
		$query = "LOCK TABLES negozio.prenotazioni WRITE;";
		mysql_query($query, $link);
		$query = "DELETE FROM negozio.prenotazioni 
				  WHERE scadenza < CURDATE();";
		//per l'inserimento in scadenza: DATE_ADD(CURDATE(), INTERVAL 1 HOUR)
		//lock in scrittura esclusivo
		//modifica del db (decrementa 'disponibili' di 1)
		//transazione con la banca
		?>
		</div>
	</body>
</html>
