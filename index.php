<?php
	session_start();
	if (isset($_SESSION['user'])) {
		header("Location: start.php");
	}
	else if (isset($_POST['username'][3])) {
		$_SESSION['user'] = $_POST['username'];
		header("Location: start.php");
	}
	else {
		echo "Inserire un username di almeno 4 caratteri";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Negozio Virtuale</title>
	</head>
	<body>
		<h1>Login</h1>
		<form name="loginform" action="" method="post" />
			Username:
			<input type="text" name="username" maxlength="20" />
			<input type="submit" value="login" />
		</form>
	</body>
</html>
