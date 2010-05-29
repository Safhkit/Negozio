<?php
session_start();
if (isset($_SESSION['user'])) {
	echo "User: ".$_SESSION['user']."<br />";
	echo '<a href="logout.php">logout</a> <br />';
	echo '<a href="index.php">HOME</a><br />';
}
else {
	header("Location: index.php");
}
?>
