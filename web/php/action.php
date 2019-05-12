<?php
	$waittime = 0;
	if (isset($_POST['action']) || isLogin() && $_SESSION['benutzer']['typ'] == "admin" && (
		!file_exists("../data/cleanup.lock") && file_exists("../data/algorithmus.pid") && !isRunning(file_get_contents("../data/algorithmus.pid")) ||
		file_exists("../data/update.pid") && !isRunning(file_get_contents("../data/update.pid")))) {
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
		// cleanup des Updates
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
					require("php/updateKlassenliste.php");
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
?>
