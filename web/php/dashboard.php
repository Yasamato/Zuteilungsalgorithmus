<?php
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {

	// Saving the new configuration after submitting it
	//Projekt-Einstellungen können nur in der ersten Phase geändert werden
	if ($config['Stage'] == 0) {
		//prepare data
		$values = [
			$_POST["stage"],
			$_POST["inputSchuelerAnzahl"],
			isset($_POST['montag']) ? "true" : "false",
			isset($_POST['dienstag']) ? "true" : "false",
			isset($_POST['mittwoch']) ? "true" : "false",
			isset($_POST['donnerstag']) ? "true" : "false",
			isset($_POST['freitag']) ? "true" : "false",
			$_POST["firstDay"]
		];
	}
	else {
		// prepare data
		$values = [
			$_POST["stage"],
			$_POST["inputSchuelerAnzahl"],
			$config['Montag'],
			$config['Dienstag'],
			$config['Mittwoch'],
			$config['Donnerstag'],
			$config['Freitag'],
			$config["SchuleAmErstenVormittag"]
		];
	}

	// write the data and check for success
	dbSetRow("../data/config.csv", "Stage", $config["Stage"], $values);
	if (dbRead("../data/config.csv") == $config) {
		error_log("Die Änderung der Einstellung in der Datei ../data/config.csv von '" . json_encode($config) . "' zu '" . json_encode($values) . "' ist fehlgeschlagen");
		die("Speichern der Konfiguration fehlgeschlagen.");
	}
	else {
		//update config
		$config = dbRead("../data/config.csv")[0];
	}
?>
	<script>
		config = {<?php
	foreach ($config as $key => $v) {
		echo "'" . $key . "': '" . $v . "',";
	}
	?>}
	  // convert the string into a bool
	  config["Montag"] = (config["Montag"] == 'true');
	  config["Dienstag"] = (config["Dienstag"] == 'true');
	  config["Mittwoch"] = (config["Mittwoch"] == 'true');
	  config["Donnerstag"] = (config["Donnerstag"] == 'true');
		config["Freitag"] = (config["Freitag"] == 'true');
	</script>
<?php
}
else {
	die("Unzureichende Berechtigung");
}
?>
