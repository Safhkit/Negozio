<?php
	session_start();
	if (isset($_SESSION['user'])) {
		echo "User: ".$_SESSION['user']."<br />";
		echo '<a href="logout.php">logout</a>';
	}
	else {
		header("Location: index.php");
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Cerca</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>

<body>
	<h1>Cerca</h1>
	<form name="ricerca" action="cerca.php" method="get">
		Cerca prodotto:
		<input type="text" name="prodotto" />
		<input type="submit" value="Avvia" />
	</form>
</body>
</html>
