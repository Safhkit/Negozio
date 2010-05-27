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
		//lock in lettura sul DB (togliere disp dalla pag prec?)
		//invio richiesta di acquisto al server (che dovrà riverificare info)
		$id = $_GET['id'];
		$user = 'user';
		$password = 'password';
		$link = mysql_connect('localhost', $user, $password);
		if (!$link)
			die ('Could not connect: ' . mysql_error() );
				
		echo "<br />";
				
		$query = "(select * from negozio.prodotti\n
				   where id = '$id')";
				
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error() );
				
		//'id' is unique
		$row = mysql_fetch_assoc($result);
		//create a page with product's data
		echo "<H1>" . $row['nome'] . "</H1>";
		echo "<hr>";
		echo "<h4 style=\"float:left;margin-left:5%;\">Descrizione:</h4>
			  <h4 style=\"float:right;margin-right:40%;\">Immagine:</h4>
			  <br>
			  <div>
			  <div  style=\"height:200px;border-width:2px;border-color:black;border-style:solid;float:left;margin-left:5%;width:35%;\" id=\"desc\">";
		echo  $row['descrizione'] . "</div>";
		echo  "<div style=\"width:35%;height:200px;border-width:2px;border-color:black;border-style:solid;float:right;margin-right:12.3%;\" id=\"img\"> . <IMG src=\"" . $row['immagine'] . "\" height=200px width=200px border=0>";
		echo  "</div><br>";
		echo "<div style=\"float:left;margin-left:5%;width:35%;\" id=\"prezzo\">
			  <h4>Prezzo:</h4><div style=\"float:left;\">" . $row['prezzo'] . "€ </div></div>";
		echo "<div style=\"float:left;margin-left:12.6%;width:35%;\" id=\"disp\">
			  <h4>Disponibili:</h4><div style=\"float:left;\">" . $row['disponibili'] . " pezzi</div></div>
			  </div><br>";
		echo "<form style=\"float:left;margin-top:5%;margin-left:5%;width:35%;\" name=\"Acquita\" action=\"acquista.php\" method=\"get\">
			  <input type=\"hidden\" name=\"id\" value=\"" .$row['id']. "\">
			  <input type=\"submit\" value=\"Acquista\">
			  </form>";
			
		mysql_free_result($result);
		mysql_close ($link);
		?>
	    </div>

	</body>
</html>