<!DOCTYPE html>
<html lang="de">
	<head>
		<title>LMG8 Campus | Wahl</title>
		<meta charset="utf-8">

		<meta name="github" content="https://github.com/Agent77326/Zuteilungsalgorithmus/">
		<meta name="author" content="Leo Jung">
		<meta name="author" content="Jan Pfenning">
		<meta name="author" content="Lukas Fausten">
		<meta name="author" content="Tim Schneider">
		<meta name="author" content="Tobias Palzer">
		<meta name="author" content="Fabian von der Warth">
		<meta name="author" content="Jonas Dalchow">
		<meta name="author" content="Leon Selig">
		<!-- Credits:


		-->

		<meta name="description" content="Wahlseite der LMG8-Schule von Maxdorf">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">

		<!-- css-frameworks -->
		<!--<link rel="stylesheet" href="bootstrap-4.0.0/css/bootstrap.min.css">-->
		<!-- replace local with remote if needed...-->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

		<link rel="stylesheet" href="css/main.css">

		<!-- JS-Libs -->
		<!-- es gilt als good practice die js ganz unten als letztes einzubinden im body.... -->
		<!--<script src="js/jquery-3.3.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
		<script src="bootstrap-4.0.0/js/bootstrap.min.js"></script>-->

		<!-- replace local with remote if needed...-->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>


		<script src="js/main.js"></script>
<?php
	session_start();
  (include "../data/config.php") OR die("</head><body style='color: #000'>Der Webserver wurde noch nicht konfiguriert, kontaktiere einen Admin damit dieser setup.sh ausführt.</body></html>");
	require "php/db.php";
	require "php/utils.php";
?>

	<script>
		var config = {<?php
			end($config);
			$last = key($config);
			foreach ($config as $key => $v) {
				echo "'" . $key . "': '" . $v . "'";
				if ($key != $last) {
					echo ",\n";
				}
			}
		?>};
		// convert the string into a bool
		config["MontagVormittag"] = (config["MontagVormittag"] == 'true');
		config["MontagNachmittag"] = (config["MontagNachmittag"] == 'true');
		config["DienstagVormittag"] = (config["DienstagVormittag"] == 'true');
		config["DienstagNachmittag"] = (config["DienstagNachmittag"] == 'true');
		config["MittwochVormittag"] = (config["MittwochVormittag"] == 'true');
		config["MittwochNachmittag"] = (config["MittwochNachmittag"] == 'true');
		config["DonnerstagVormittag"] = (config["DonnerstagVormittag"] == 'true');
		config["DonnerstagNachmittag"] = (config["DonnerstagNachmittag"] == 'true');
		config["FreitagVormittag"] = (config["FreitagVormittag"] == 'true');
		config["FreitagNachmittag"] = (config["FreitagNachmittag"] == 'true');
	</script>

<?php

	// on form-submit
	if (isset($_GET['logout'])) {
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
			case "editProject":
				require("php/editProject.php");
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
	$klassen = [];
	foreach (dbRead("../data/wahl.csv") as $key => $student) {
		if (empty($klassen[$student["klasse"]])) {
			$klassen[$student["klasse"]] = [$student];
		}
		else {
			array_push($klassen[$student["klasse"]], $student);
		}
	}
	foreach ($klassen as $klasse => $studentlist) {
		array_multisort(array_column($studentlist, "nachname"), SORT_ASC, $studentlist);
	}
	$wahlen = [];
	foreach ($klassen as $klasse) {
		foreach ($klasse as $student) {
			array_push($wahlen, $student);
		}
	}
	
	$projekte = [];
	if (isLogin()) {
		if ($_SESSION['benutzer']['typ'] == "teachers" || $_SESSION['benutzer']['typ'] == "admin") {
			$projekte = dbRead("../data/projekte.csv");
		}
		else {
			foreach (dbRead("../data/projekte.csv") as $p) {
				if ($p['minKlasse'] <= $_SESSION['benutzer']['stufe'] && $p['maxKlasse'] >= $_SESSION['benutzer']['stufe']) {
					array_push($projekte, $p);
				}
			}
		}
	}
?>
		<script>
			var projekte = [<?php
			foreach ($projekte as $p) {
		    echo "{";
		    foreach ($p as $key => $v) {
		      echo "'" . $key . "': `" . $v . "`,";
		    }
		    echo "}";
				if ($p != $projekte[sizeof($projekte) - 1]) {
					echo ",\n";
				}
			}
		?>
			];
		</script>
<?php
	//--------------------------------------------------------
	//html-teil
	if (isLogin()) {
		if ($_SESSION['benutzer']['typ'] == "admin") {
			if (!empty($_GET['site']) && $_GET['site'] == "create") {
?>
	<script>var site = "projektErstellung";</script>
	<link rel="stylesheet" href="css/projektErstellung.css">
</head>
<body>
<?php
				include "html/projektErstellung.php";
			}
			elseif (!empty($_GET['site']) && $_GET['site'] == "edit") {
?>
	<script>var site = "projektEdit";</script>
	<link rel="stylesheet" href="css/projektErstellung.css">
</head>
<body>
<?php
				include "html/projektEdit.php";
			}
			else {
?>
		<script>var site = "dashboard";</script>
		<link rel="stylesheet" href="css/dashboard.css">
	</head>
	<body>
<?php
				include "html/dashboard.php";
			}
		}
		elseif ($_SESSION['benutzer']['typ'] == "teachers") {
			if ($config["Stage"] > 0) {
				if (!empty($_GET['site']) && $_GET['site'] == "create" && $config["Stage"] == 1) {
?>
		<script>var site = "projektErstellung";</script>
		<link rel="stylesheet" href="css/projektErstellung.css">
	</head>
	<body>
<?php
					include "html/projektErstellung.php";
				}
				elseif (!empty($_GET['site']) && $_GET['site'] == "edit" && $config["Stage"] < 3) {
?>
		<script>var site = "projektEdit";</script>
		<link rel="stylesheet" href="css/projektErstellung.css">
	</head>
	<body>
<?php
					include "html/projektEdit.php";
				}
				else {
?>
		<script>var site = "projektListe";</script>
	</head>
	<body>
<?php
					include "html/projektListe.php";
				}
			}
			else {
?>
		<script>var site = "closed";</script>
	</head>
	<body>
<?php
				logout();
				include "html/einreichenGeschlossen.html";
			}
		}
		else {
			if ($config["Stage"] == 3) {
?>
		<script>var site = "wahl";</script>
		<link rel="stylesheet" href="css/wahl.css">
	</head>
	<body>
<?php
				include "html/wahl.php";
			}
			else {
?>
		<script>var site = "closed";</script>
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
		<script>var site = "login";</script>
		<link rel="stylesheet" href="css/login.css">
	</head>
	<body>
	<div class="container text-center login-box d-flex justify-content-center">
		<form class="form-signin" method="post">
<?php
		if (isset($loginResult)) {
?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Verweigert</strong> Falsche Benutzerdaten!
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
<?php } ?>
			<img class="mb-4" src="pictures/wahlbox.svg" alt="Wahlbox" width="144" height="144">
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
		<form id="logout" method="post" action="/">
			<input type="hidden" name="action" value="logout">
		</form>
		<div class="tmp-modal"></div>

		<footer class="footer">
			<div class="container text-center">
				<small class="text-muted">
					(c) 2018-2019 Leo Jung, Fabian von der Warth, Jan Pfenning, Tim Schneider, Lukas Fausten, Tobias Palzer, Leon Selig, Jonas Dalchow
				</small>
			</div>
		</footer>
	</body>
</html>
