<?php
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {

	// Saving the new configuration after submitting it
	//Projekt-Einstellungen können nur in der ersten Phase geändert werden
	if ($config['Stage'] == 0) {
		//prepare data
		$values = [
			$_POST["stage"],
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
	}
	else {
		// prepare data
		$values = [
			$_POST["stage"],
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
