<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (isset($_SESSION['user'])) {
	echo "User: ".$_SESSION['user'].'&nbsp;';
	echo '&nbsp;<a href="logout.php">logout</a>&nbsp;';
	echo '&nbsp;<a href="index.php">HOME</a>&nbsp;';
	echo '&nbsp;<a href="carrello.php">carrello</a> <hr />&nbsp;';
}
else {
	header("Location: index.php");
}
?>
