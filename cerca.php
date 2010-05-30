<?php
include_once('login.php');
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
			<?php $prodotto = $_GET["prodotto"]?>
			Risultato della ricerca per "<b><?php echo $prodotto;?></b>":
		</div>

		<div>
		<?php
		$user = 'user';
		$password = 'password';
		$link = mysql_connect('localhost', $user, $password);
		if (!$link)
			die ('Could not connect: ' . mysql_error() );

		echo '<br />';
		$query = "(select * from negozio.prodotti\n
				   where nome LIKE '%$prodotto%')\n
				   union
				  (select * from negozio.prodotti\n
				   where tag LIKE '%$prodotto%')\n
				   order by nome";
						
		$result = mysql_query($query, $link);
		if (!$result)
			die ('Invalid query: ' . mysql_error() );
			
		//create a table to show results
		//header
		echo "<table border=\"1\"><TR><TH>";
		echo 'nome prodotto' . "</TH><TH>" . 'parole chiave' . "</TH><TH>";
		echo 'pezzi disponibili' . "</TH><TH>" . 'prezzo';
		echo "</TH></TR>";
			
		//data
		while ($row = mysql_fetch_assoc($result)) {
			echo "<TR><TD>";				
			echo $row['nome'];
			echo "</TD><TD>";
			echo $row['tag'];
			echo "</TD><TD>";
			echo $row['disponibili'];
			echo "</TD><TD>";
			echo $row['prezzo'];
			echo "</TD><TD>";

			echo "<form name=\"Visualizza\" action=\"scheda.php\" method=\"get\">
				    <input type=\"hidden\" name=\"id\" value=\"" .$row['id']. "\">
				    <input type=\"submit\" value=\"Visualizza\">
				  </form>";
			echo "</TD></TR>";
		}
		echo "</table>";
		mysql_free_result($result);
		mysql_close ($link);
	    ?>
		</div>
	</body>
</html>
