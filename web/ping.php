{
<?php
	if (!file_exists("../LICENSE") || !file_exists("../VERSION")) {
		die("</head><body style='color: #000'>Bei diesem Produkt handelt es sich möglicherweise um eine illegale Kopie. Bitte beziehen Sie dieses Produkt nur von der offiziellen Github-Seite unter <a href='https://github.com/Agent77326/Zuteilungsalgorithmus'>https://github.com/Agent77326/Zuteilungsalgorithmus</a></body></html>");
	}
	session_start();
  (include "../data/config.php") OR die("<body style='color: #000'>Der Webserver wurde noch nicht konfiguriert, kontaktiere einen Admin damit dieser setup.sh ausführt.</body></html>");
	require "php/db.php";
	require "php/utils.php";
	require "php/setup.php";
  if (isLogin() && $_SESSION["benutzer"]["typ"] == "admin") {
    $fh = fopen("../data/admin.lock", "w");
    fwrite($fh, time() . "," . session_id());
    fclose($fh);
    echo '"version" : "' . $version . '"' . ", \n";
    echo '"newest" : "' . $newest . '"' . ", \n";
    // Algorithmus-isRunning
    if (isRunning(file_get_contents("../data/algorithmus.pid"))) {
      $status = file_exists("../FinishedAlgorithm/prozentzahl") ? file_get_contents("../FinishedAlgorithm/prozentzahl") : 0;
    }
    elseif (file_exists("../FinishedAlgorithm/prozentzahl")) {
      $status = 1;
    }
    else {
      $status = "false";
    }
    echo '"algorithmusRunning" : "' . $status . '"' . ", \n";

    // Update-isRunning
    $status = isRunning(file_get_contents("../data/update.pid")) ? "true" : "false";
    echo '"updateRunning" : "' . $status . '"' . ", \n";

    // databases
    echo '"config" : ' . JSON_encode(array_merge($config, CONFIG)) . ",\n";
    echo '"projekte" : ' . JSON_encode($projekte) . ",\n";
    echo '"wahlen" : ' . JSON_encode($wahlen) . ",\n";
    echo '"zwangszuteilungen" : ' . JSON_encode($zwangszuteilung) . ",\n";
    echo '"keineWahl" : ' . JSON_encode($keineWahl) . ",\n";
    echo '"klassen" : ' . JSON_encode($klassen) . ",\n";
    echo '"klassenliste" : ' . JSON_encode($klassenliste) . "\n";
  }
  elseif (isLogin() && $_SESSION["benutzer"]["typ"] == "teachers") {
    // Update-isRunning
    $status = isRunning(file_get_contents("../data/update.pid")) ? "true" : "false";
    echo '"updateRunning" : "' . $status . '"' . ", \n";
    echo '"config" : ' . JSON_encode($config) . ",\n";
    echo '"projekte" : ' . JSON_encode($projekte) . ",\n";
    echo '"klassen" : ' . JSON_encode($klassen) . ",\n";
    echo '"klassenliste" : ' . JSON_encode($klassenliste) . "\n";
	}
  elseif (isLogin()) {
    // Update-isRunning
    $status = isRunning(file_get_contents("../data/update.pid")) ? "true" : "false";
    echo '"updateRunning" : "' . $status . '"' . ", \n";
		$vorherigeWahl = "false";
		foreach ($wahlen as $wahl) {
			if ($_SESSION["benutzer"]["uid"] == $wahl["uid"]) {
				$vorherigeWahl = $wahl;
				break;
			}
		}
    echo '"vorherigeWahl" : "' . JSON_encode($vorherigeWahl) . '"' . ", \n";
		echo '"projekte" : ' . JSON_encode($projekte) . "\n";
	}
?>
}
