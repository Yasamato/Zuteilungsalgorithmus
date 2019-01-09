<?php
if (isLogin()) {// hard gecoded für präsi
	if (!empty($_POST['wahl']) && count($_POST['wahl']) == 4 && $_SESSION['benutzer']['typ'] != "teachers") {
		$projekte = dbRead("../data/projekte.csv");
		$valid = true;
		foreach ($_POST['wahl'] as $p) {
			if ($projekte[$p]['minKlasse'] > $_SESSION['benutzer']['stufe'] && $projekte[$p]['maxKlasse'] < $_SESSION['benutzer']['stufe']) {
				$valid = false;
			}
		}

		if ($valid) {
			if (!file_exists("../data/schueler.csv")) {
				dbCreateFile("../data/schueler.csv", [
					"uid",
					"vorname",
					"nachname",
					"stufe",
					"klasse",
					"wahl"
				]);
			}

			$wahl = [
				$_SESSION['benutzer']['uid'],
				$_SESSION['benutzer']['vorname'],
				$_SESSION['benutzer']['nachname'],
				$_SESSION['benutzer']['stufe'],
				$_SESSION['benutzer']['klasse'],
				implode("§", $_POST['wahl']) // warum haben wir hier §?
			];
			// Überschreiben des vorigen Eintrags
			if (count(dbSearch("../data/schueler.csv", "uid", $_SESSION['benutzer']['uid'])) > 0) {
				dbSetRow("../data/schueler.csv", "uid", $_SESSION['benutzer']['uid'], $wahl);
				print("Eintrag erfolgreich aktualisiert");
			}
			else {
				dbAdd("../data/schueler.csv", $wahl);
				print("Erfolgreich eingetragen");
			}
		}
		else {
			error_log("Invalide Stufenlimitierung! Der Schüler " .  $_SESSION['benutzer']['vorname'] . ", " . $_SESSION['benutzer']['nachname'] . " aus der " . $_SESSION['benutzer']['stufe'] . " " . $_SESSION['benutzer']['klasse'] . " versuchte eine ungültige Wahl einzureichen.");
			die("Ungültige Wahl");
		}
	}
	else{
		die("Ungültige Wahl");
	}
}
else{
	die("Anmeldung abgelaufen!");
}
?>
