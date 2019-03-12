<?php
if (isLogin()) {// hard gecoded für präsi
	if (!empty($_POST['wahl']) && count($_POST['wahl']) == 4 && $_SESSION['benutzer']['typ'] != "teachers") {
		$projekte = dbRead("../data/projekte.csv");
		$valid = true;
		foreach ($_POST['wahl'] as $p) {
			$projekt = getProjektInfo($p);
			if (empty($projekt) || $projekt['minKlasse'] > $_SESSION['benutzer']['stufe'] && $projekt['maxKlasse'] < $_SESSION['benutzer']['stufe']) {
				$valid = false;
			}
		}

		if ($valid) {

			$wahl = [
				$_SESSION['benutzer']['uid'],
				$_SESSION['benutzer']['vorname'],
				$_SESSION['benutzer']['nachname'],
				$_SESSION['benutzer']['stufe'],
				$_SESSION['benutzer']['klasse'],
				implode("§", $_POST['wahl']), // warum haben wir hier §? weil ir es können :)
				""
			];
			// Überschreiben des vorigen Eintrags
			if (count(dbSearch("../data/wahl.csv", "uid", $_SESSION['benutzer']['uid'])) > 0) {
				dbSetRow("../data/wahl.csv", "uid", $_SESSION['benutzer']['uid'], $wahl);
				alert("Eintrag erfolgreich aktualisiert");
			}
			else {
				dbAdd("../data/wahl.csv", $wahl);
				alert("Erfolgreich eingetragen");
			}
		}
		else {
			// Petze :)
			error_log("Invalide Stufenlimitierung! Der Schüler " .  $_SESSION['benutzer']['vorname'] . ", " . $_SESSION['benutzer']['nachname'] . " aus der " . $_SESSION['benutzer']['stufe'] . " " . $_SESSION['benutzer']['klasse'] . " versuchte eine ungültige Wahl einzureichen.", 0, "../data/error.log");
			alert("Ungültige Wahl");
		}
	}
	else{
		alert("Ungültige Wahl");
	}
}
else{
	die("Anmeldung abgelaufen!");
}
?>
