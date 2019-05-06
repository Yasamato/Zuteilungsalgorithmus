<!DOCTYPE html>
<html lang="de">
	<head>
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

		<?php echo file_get_contents("html/head.html"); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>

		<!-- bootstrap-frameworks -->
		<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/main.css?hash=<?php echo sha1_file("css/main.css"); ?>">

		<!-- JS-Libs -->
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/popper-1.14.7.min.js"></script>
		<script src="bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>

		<script src="js/main.js?hash=<?php echo sha1_file("js/main.js"); ?>"></script>
<?php
	if (!file_exists("../LICENSE") || !file_exists("../VERSION")) {
		die("</head><body style='color: #000'>Bei diesem Produkt handelt es sich möglicherweise um eine illegale Kopie. Bitte beziehen Sie dieses Produkt nur von der offiziellen Github-Seite unter <a href='https://github.com/Agent77326/Zuteilungsalgorithmus'>https://github.com/Agent77326/Zuteilungsalgorithmus</a></body></html>");
	}
	session_start();
  (include "../data/config.php") OR die("</head><body style='color: #000'>Der Webserver wurde noch nicht konfiguriert, kontaktiere einen Admin damit dieser setup.sh ausführt.</body></html>");
	require "php/db.php";
	require "php/utils.php";

	/*
	// Integration von Fabi's Verteilung
	$tmp = file_get_contents("../data/nurSchueler.csv");
	$data = [];
	$header = [
		"uid",
		"vorname",
		"nachname",
		"stufe",
		"klasse",
		"wahl",
		"projekt"
	];
	foreach (explode("\n", $tmp) as $line => $row) {
		if (empty($row)) {
			continue;
		}
		$row = explode(",", $row);
		$stufe = "";
		for ($i = 0; $i < strlen($row[0]); $i++) {
			$char = $row[0][$i];
			if (!is_numeric($char)) {
				break;
			}
			$stufe .= $char;
		}
		array_push($data, [
			$row[1], // uid
			$row[2], // vorname
			"Nachname", // nachname
			$stufe, // stufe
			$row[0], // klasse
			implode("§", [
				$row[3],
				$row[4],
				$row[5],
				$row[6],
				$row[7] // nur bei 5 Wahlen
			]), // wahl
			"" // ergebnis
		]);
	}
	dbWrite("../data/wahl.csv", $data, $header);
	*/

	// on form-submit
	if (isset($_GET['logout'])) {
		logout();
	}
	require 'php/setup.php';

	/*
	if (($fh = fopen("../data/p.csv", "w")) === false) {
		die("Mangelnde Zugriffsberechtigung auf den Ordner FinishedAlgorithm");
	}
	fwrite($fh, "id,minPlatz,maxPlatz,minKlasse,maxKlasse\n");
	foreach ($projekte as $projekt) {
    fwrite($fh, $projekt["id"] . "," . $projekt["minPlatz"] . "," . $projekt["maxPlatz"] . "," . $projekt["minKlasse"] . "," . $projekt["maxKlasse"]);
    if ($projekt["id"] != $projekte[count($projekte) - 1]["id"]) {
      fwrite($fh, "\n");
    }
	}
	fclose($fh);*/


	$waittime = 0;
	if (isset($_POST['action']) || isLogin() && $_SESSION['benutzer']['typ'] == "admin" && (
		!file_exists("../data/cleanup.lock") && file_exists("../data/algorithmus.pid") && !isRunning(file_get_contents("../data/algorithmus.pid")) ||
		!file_exists("../data/update.lock") && file_exists("../data/update.pid") && !isRunning(file_get_contents("../data/update.pid")))) {
		// cleanup des Zuteilungsalgorithmus
		if (isLogin() && $_SESSION['benutzer']['typ'] == "admin" && !file_exists("../data/cleanup.lock") && file_exists("../data/algorithmus.pid") && !isRunning(file_get_contents("../data/algorithmus.pid"))) {
			$fh = fopen("../data/cleanup.lock", "w");
			fclose($fh);
		  unlink("../data/algorithmus.pid");
		  alert("Der Zuteilungsalgorithmus wurde beendet.");
		  if (file_exists("../FinishedAlgorithm/prozentzahl")) {
		    unlink("../FinishedAlgorithm/prozentzahl");
		  }
		  if (file_exists("../FinishedAlgorithm/verteilungNachSchuelern.csv") && file_exists("../FinishedAlgorithm/verteilungNachProjekten.csv")) {
		    dbSet("../data/config.csv", "Stage", $config["Stage"], "Stage", "5");
				$head = true;
				foreach (explode("\n", file_get_contents("../FinishedAlgorithm/verteilungNachSchuelern.csv")) as $row) {
					if ($head) {
						$head = false;
						continue;
					}
					$data = explode(",", $row);
					dbSet("../data/wahl.csv", "uid", $data[1], "projekt", ($data[3] == "null" ? "" : $data[3]));
				}
		    alert("Die Daten wurden erfolgreich ausgewertet.");
		  }
		  else {
		    alert("Es konnten keine Ergebnisdateien gefunden werden. Überprüfen sie die Dateiberechtigungen im Verzeichnis 'FinishedAlgorithm', da es sich hierbei wahrscheinlich um einen Berechtigungsfehler handelt.");
		  }
			$waittime = 2;
			unlink("../data/cleanup.lock");
		}
		elseif (isLogin() && $_SESSION['benutzer']['typ'] == "admin" && file_exists("../data/update.pid") && !isRunning(file_get_contents("../data/update.pid"))) {
		  unlink("../data/update.pid");
		  if ($newest == $version) {
		  	alert("Das Update wurde erfolgreich durchgeführt.");
		  }
		  else {
		  	alert("Das Update auf Version " . $newest . " von Version " . $version . " ist fehlgeschlagen. Überprüfen Sie bitte die Berechtigungen.");
		  }
			$waittime = 2;
		}
		else {
			// eigentlicher action-handler
			switch ($_POST['action']) {
				case "login":
					require("php/login.php"); // dummy-login
					// require("php/login_live.php");
					break;
				case "logout":
					logout();
					break;
				case "addProject":
					require("php/projektErstellung.php");
					$waittime = 5;
					break;
				case "editProject":
					require("php/editProjekt.php");
					$waittime = 5;
					break;
				case "deleteProjekt":
					require("php/deleteProjekt.php");
					$waittime = 5;
					break;
				case "wahl":
					require("php/wahl.php");
					$waittime = 1;
					break;
				case "editWahleintrag":
					require("php/editWahleintrag.php");
					$waittime = 1;
					break;
				case "deleteWahleintrag":
					require("php/deleteWahleintrag.php");
					$waittime = 1;
					break;
				case "deleteProjektzuteilung":
					require("php/deleteProjektzuteilung.php");
					$waittime = 1;
					break;
				case "updateConfiguration":
					require("php/dashboard.php");
					$waittime = 1;
					break;
				case "updateKlassenliste":
					require("php/klassenliste.php");
					$waittime = 1;
					break;
				case "updateZwangszuteilung":
					require("php/updateZwangszuteilung.php");
					$waittime = 1;
					break;
				case "updateKeineWahl":
					require("php/updateKeineWahl.php");
					$waittime = 1;
					break;
				case "runZuteilungsalgorithmus":
					require("php/run.php");
					$waittime = 1;
					break;
				case "update":
					require("php/update.php");
					$waittime = 2;
					break;
				default:
					die("Unbekannter Befehl!");
					break;
			}
		}
		// verhindern vom erneuten Senden von Formular-Daten beim Refreshen durch den Browser
		?>
		<meta http-equiv="refresh" content="<?php echo $waittime; ?>; url=?">
	</head>
	<body>
		<div class="container">
			<div class="card bg-dark">
				<div class="card-body text-center">
					<p>
						Sie werden in <span id="timer"><?php echo $waittime; ?></span>s <a href="?">hierhin</a> automatisch weitergeleitet.
					</p>
				</div>
			</div>
		</div>
		<script>
			window.setInterval(function () {
				$("#timer").html(parseInt($("#timer").html()) - 1);
			}, 1000);
		</script>
	</body>
</html>
		<?php
		die("");
	}

	// DEBUG
	/*if(isset($loginResult)) {
		//print_r(array_keys($loginResult)[30]);
		//print_r(array_values($loginResult)[30][0]);
		//print_r($loginResult);
		print_r($_SESSION['benutzer']);
	}*/
?>
<script>
	var user = "<?php echo empty($_SESSION['benutzer']['typ']) ? "logged out" : $_SESSION['benutzer']['typ']; ?>";
</script>
<?php
	//--------------------------------------------------------
	//html-teil
	if (isLogin()) {
		if ($_SESSION['benutzer']['typ'] == "admin") {
			if (!empty($_GET['site']) && ($_GET['site'] == "create" || $_GET["site"] == "edit")) {
?>
	<link rel="stylesheet" href="css/projektErstellung.css?hash=<?php echo sha1_file("css/projektErstellung.css"); ?>">
</head>
<body>
<?php
				include "html/projektErstellung.php";
			}
			else {
?>
		<link rel="stylesheet" href="css/dashboard.css?hash=<?php echo sha1_file("css/dashboard.css"); ?>">
	</head>
	<body>
<?php
				include "html/dashboard.php";
			}
		}
		elseif ($_SESSION['benutzer']['typ'] == "teachers") {
			if ($config["Stage"] > 0) {
				if (!empty($_GET['site']) && ($_GET['site'] == "create" && $config["Stage"] == 1 || $_GET['site'] == "edit")) {
?>
		<link rel="stylesheet" href="css/projektErstellung.css?hash=<?php echo sha1_file("css/projektErstellung.css"); ?>">
	</head>
	<body>
<?php
					include "html/projektErstellung.php";
				}
				else {
					if (!empty($_GET['site']) && $_GET['site'] == "create") {
						alert("Es können keine Projekte mehr eingereicht werden. Wenden sie sich hierfür bei Hilfe an den Administrator");
					}
?>
	</head>
	<body>
<?php
					include "html/lehrerDashboard.php";
				}
			}
			else {
?>
	</head>
	<body>
<?php
				logout();
				include "html/einreichenGeschlossen.html";
			}
		}
		else {
			$zwangszugeteilt = false;
			foreach ($zwangszuteilung as $key => $zuteilung) {
				if ($_SESSION["benutzer"]["uid"] == $zuteilung["uid"]) {
					$zwangszugeteilt = true;
					break;
				}
			}
			if ($config["Stage"] == 3 && !$zwangszugeteilt) {
?>
		<link rel="stylesheet" href="css/wahl.css?hash=<?php echo sha1_file("css/wahl.css"); ?>">
	</head>
	<body>
<?php
				include "html/wahl.php";
			}
			else {
?>
	</head>
	<body>
<?php
				if ($zwangszugeteilt) {
					include "html/zwangszuteilung.php";
				}
				else {
					include "html/wahlGeschlossen.html";
				}
				logout();
			}
		}
	}
	else {
?>
		<link rel="stylesheet" href="css/login.css?hash=<?php echo sha1_file("css/login.css"); ?>">
	</head>
	<body>
<?php
			include "html/login.php";
	}
?>
		<form id="logout" method="post" action="/">
			<input type="hidden" name="action" value="logout">
		</form>
		<div class="tmp-modal"></div>

		<footer class="footer">
			<div class="container text-center">
				<small class="text-muted">
					&copy; 2018-2019 Leo Jung, Fabian von der Warth, Jan Pfenning, Tim Schneider, Lukas Fausten, Tobias Palzer, Leon Selig, Jonas Dalchow
				</small>
			</div>
		</footer>
	</body>
</html>
