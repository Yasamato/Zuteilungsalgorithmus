<?php
if (isLogin() && $_SESSION['benutzer']['typ'] != "teachers" && $_SESSION['benutzer']['typ'] != "admin") {
	if (!empty($_POST['wahl']) && count($_POST['wahl']) == CONFIG["anzahlWahlen"]) {
		$valid = true;
		foreach ($_POST['wahl'] as $p) {
			$projekt = getProjektInfo($projekte, $p);
			if (empty($projekt) || $projekt['minKlasse'] > $_SESSION['benutzer']['stufe'] && $projekt['maxKlasse'] < $_SESSION['benutzer']['stufe']) {
				$valid = false;
				alert("Invalide Stufen! Ungültige Wahl.");
				break;
			}
		}

		if ($valid) {
			$wahl = [
				$_SESSION['benutzer']['uid'],
				$_SESSION['benutzer']['vorname'],
				$_SESSION['benutzer']['nachname'],
				$_SESSION['benutzer']['stufe'],
				$_SESSION['benutzer']['klasse'],
				implode("§", $_POST['wahl']), // warum haben wir hier §? weil wir es können :)
				""
			];
			// Überschreiben des vorigen Eintrags
			if (count(dbSearch("../data/wahl.csv", "uid", $_SESSION['benutzer']['uid'])) > 0) {
				dbSetRow("../data/wahl.csv", "uid", $_SESSION['benutzer']['uid'], $wahl);
				alert("Die Wahl wurde erfolgreich aktualisiert");
        foreach ($keineWahl as $key => $student) {
          if ($student["uid"] == $_SESSION['benutzer']['uid']) {
            dbRemove("../data/keineWahl.csv", "uid", $_SESSION['benutzer']['uid']);
            break;
          }
        }
			}
			else {
				dbAdd("../data/wahl.csv", $wahl);
				alert("Die Wahl wurde erfolgreich eingetragen");
			}
			logout();
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
