<?php
//TODO: controlli sull'esistenza della sessione
session_start();
session_destroy();
header("Location: index.php");
?>

