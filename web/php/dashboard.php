<?php
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {

	// Saving the new configuration after submitting it
	// Viele Einstellungen können nur in der initalen Phase geändert werden
	if ($config['Stage'] == 0) {
		//prepare data
		$values = [
			$_POST["stage"],
			$_POST["wahlTyp"],
			empty($_POST["dauer"]["montag"]["vormittag"]) ? "false" : "true",
			empty($_POST["dauer"]["montag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["montag"]["vormittagHinweis"],
			empty($_POST["dauer"]["montag"]["nachmittag"]) ? "false" : "true",
			empty($_POST["dauer"]["montag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["montag"]["nachmittagHinweis"],
			empty($_POST["dauer"]["dienstag"]["vormittag"]) ? "false" : "true",
			empty($_POST["dauer"]["dienstag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["dienstag"]["vormittagHinweis"],
			empty($_POST["dauer"]["dienstag"]["nachmittag"]) ? "false" : "true",
			empty($_POST["dauer"]["dienstag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["dienstag"]["nachmittagHinweis"],
			empty($_POST["dauer"]["mittwoch"]["vormittag"]) ? "false" : "true",
			empty($_POST["dauer"]["mittwoch"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["mittwoch"]["vormittagHinweis"],
			empty($_POST["dauer"]["mittwoch"]["nachmittag"]) ? "false" : "true",
			empty($_POST["dauer"]["mittwoch"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["mittwoch"]["nachmittagHinweis"],
			empty($_POST["dauer"]["donnerstag"]["vormittag"]) ? "false" : "true",
			empty($_POST["dauer"]["donnerstag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["donnerstag"]["vormittagHinweis"],
			empty($_POST["dauer"]["donnerstag"]["nachmittag"]) ? "false" : "true",
			empty($_POST["dauer"]["donnerstag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["donnerstag"]["nachmittagHinweis"],
			empty($_POST["dauer"]["freitag"]["vormittag"]) ? "false" : "true",
			empty($_POST["dauer"]["freitag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["freitag"]["vormittagHinweis"],
			empty($_POST["dauer"]["freitag"]["nachmittag"]) ? "false" : "true",
			empty($_POST["dauer"]["freitag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["freitag"]["nachmittagHinweis"]
		];

	  if (!empty($_POST["tag"]) && !empty($_POST["von"]) && !empty($_POST["bis"]) && count($_POST["tag"]) == count($_POST["von"]) && count($_POST["von"]) == count($_POST["bis"])) {
      $data = [];
      for ($i = 0; $i < count($_POST["tag"]); $i++) {
        if (empty($_POST["tag"][$i]) || empty($_POST["von"][$i]) || empty($_POST["bis"][$i])) {
          continue;
        }

        $doppelt = false;
        foreach ($data as $termin) {
          if ($termin["tag"] == $_POST["tag"][$i] && $termin["von"] == $_POST["von"][$i] && $termin["bis"] == $_POST["bis"][$i]) {
            $doppelt = true;
            break;
          }
        }
        if ($doppelt) {
          continue;
        }
        array_push($data, [
          "tag" => $_POST["tag"][$i],
          "vonUhr" => $_POST["von"][$i],
          "bisUhr" => $_POST["bis"][$i]
        ]);
      }
      if (dbWrite("../data/agTermine.csv", $data) === false) {
        alert("Die Daten der AG-Termine konnten nicht gespeichert werden: '" . json_encode($data) . "'");
      }
		}
		else {
			alert("AG-Termine sind ungültig...");
		}
	}
	else {
		// prepare data
		$values = [
			$_POST["stage"],
			$config['wahlTyp'],
			$config['MontagVormittag'],
			empty($_POST["dauer"]["montag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["montag"]["vormittagHinweis"],
			$config['MontagNachmittag'],
			empty($_POST["dauer"]["montag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["montag"]["nachmittagHinweis"],
			$config['DienstagVormittag'],
			empty($_POST["dauer"]["dienstag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["dienstag"]["vormittagHinweis"],
			$config['DienstagNachmittag'],
			empty($_POST["dauer"]["dienstag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["dienstag"]["nachmittagHinweis"],
			$config['MittwochVormittag'],
			empty($_POST["dauer"]["mittwoch"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["mittwoch"]["vormittagHinweis"],
			$config['MittwochNachmittag'],
			empty($_POST["dauer"]["mittwoch"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["mittwoch"]["nachmittagHinweis"],
			$config['DonnerstagVormittag'],
			empty($_POST["dauer"]["donnerstag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["donnerstag"]["vormittagHinweis"],
			$config['DonnerstagNachmittag'],
			empty($_POST["dauer"]["donnerstag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["donnerstag"]["nachmittagHinweis"],
			$config['FreitagVormittag'],
			empty($_POST["dauer"]["freitag"]["vormittagHinweis"]) ? "" : $_POST["dauer"]["freitag"]["vormittagHinweis"],
			$config['FreitagNachmittag'],
			empty($_POST["dauer"]["freitag"]["nachmittagHinweis"]) ? "" : $_POST["dauer"]["freitag"]["nachmittagHinweis"]
		];
	}

	// write the data and check for success
	dbSetRow("../data/config.csv", "Stage", $config["Stage"], $values);
	if (dbRead("../data/config.csv") == $config) {
		alert("Speichern der Konfiguration fehlgeschlagen: Die Änderung der Einstellung in der Datei ../data/config.csv von '" . json_encode($config) . "' zu '" . json_encode($values) . "' ist fehlgeschlagen");
	}
	else {
		alert("Änderungen der Konfiguration gespeichert");
	}
}
else {
	die("Unzureichende Berechtigung");
}
?>
