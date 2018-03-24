<!DOCTYPE html>
<html lang="de">
	<head>
		<title>LMG8 Campus | Wahl</title>
		<meta charset="utf-8">
		<meta name="author" content="Leo Jung">
		<meta name="description" content="Wahlseite der LMG8-Schule von Maxdorf">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
		<!-- css-frameworks -->
		<link rel="stylesheet" href="bootstrap-4.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/main.css">
		<!-- JS-Libs -->
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
		<script src="bootstrap-4.0.0/js/bootstrap.min.js"></script>
<?php
	session_start();
	require("php/db.php");
	$config = read("data/config.csv")[0];
	function isLogin(){
		return isset($_SESSION['benutzer']);
	}

	function logout(){
		session_destroy();
		session_start();
	}

	// on form-submit
	if(isset($_GET['logout'])){
		logout();
	}
	if (isset($_POST['action'])) {
		switch ($_POST['action']) {
			case "login":
				require("php/login.php");
				break;
			case "logout":
				logout();
				break;
			case "addProject":
				require("php/projektErstellung.php");
				break;
			case "wahl":
				require("php/wahl.php");
				break;
			case "updateConfiguration":
				require("php/dashboard.php");
				break;
			default:
				die("Unbekannter Befehl!");
				break;
		}
	}

	// DEBUG
	/*if(isset($loginResult)) {
		//print_r(array_keys($loginResult)[30]);
		//print_r(array_values($loginResult)[30][0]);
		//print_r($loginResult);
		print_r($_SESSION['benutzer']);
	}*/

	//--------------------------------------------------------
	//html-teil
	if (isLogin()) {
		if($_SESSION['benutzer']['typ'] == "teachers") {
			//if(read("csv/config.csv")[0]["Stage"] == 2){
?>
		<link rel="stylesheet" href="css/projektErstellung.css">
	</head>
	<body>
<?php
				include "html/projektErstellung.html";
			/*}
			else{
?>
	</head>
	<body>
<?php
				logout();
				include "html/einreichenGeschlossen.html";
			}*/
		}
		elseif($_SESSION['benutzer']['typ'] == "admin"){
?>
		<link rel="stylesheet" href="css/dashboard.css">
	</head>
	<body>
<?php
			include "html/dashboard.php";
		}
		else {
			if(read("csv/config.csv")[0]["Stage"] == 4){
?>
		<link rel="stylesheet" href="css/wahl.css">
	</head>
	<body>
<?php
				include "html/wahl.html";
			}
			else{
?>
?>
	</head>
	<body>
<?php
				logout();
				include "html/wahlGeschlossen.html";
			}
		}
	}
	else {
?>
		<link rel="stylesheet" href="css/login.css">
	</head>
	<body>
	<div class="container text-center login-box d-flex justify-content-center">
		<form class="form-signin" method="post">
<?php
		if(isset($loginResult)) {
?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Verweigert</strong> Falsche Benutzerdaten!
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
<?php } ?>
			<img class="mb-4" src="wahlbox.svg" alt="Wahlbox" width="144" height="144">
			<h1 class="h3 mb-3 font-weight-normal">Anmeldung</h1>
			<label for="inputBenutzername" class="sr-only">Benutzername</label>
			<input type="text" name="user" id="inputBenutzername" class="form-control" placeholder="Benutzername" required autofocus>
			<label for="inputPasswort" class="sr-only">Passwort</label>
			<input type="password" name="pw" id="inputPasswort" class="form-control" placeholder="Passwort" required>
			<button name="action" value="login" class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
		</form>
	</div>
<?php
	}
?>
	</body>
</html>
