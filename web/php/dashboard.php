<?php
if(isLogin() && $_SESSION['benutzer']['typ'] == "admin") {

	// Saving the new configuration after submitting it
	if(!file_exists("data/config.csv")){
		// define the names of the columns in the first row
		$names = [
			"Stage",
			"Schüleranzahl",
			"Montag",
			"Dienstag",
			"Mittwoch",
			"Donnerstag",
			"Freitag",
			"SchuleAmErstenVormittag"
		];
		createFile("data/config.csv", $names);
	}

	//Projekt-Einstellungen können nur in der ersten Phase geändert werden
	if($config['Stage'] == 0){
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
	else{
		//prepare data
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
	setRow("data/config.csv", 0, $values);
	if(read("data/config.csv") == $config){
		die("Konfiguration wurde nicht gespeichert.");
	}
	else{
		//update config
		$config = read("data/config.csv");
	}
?>
	<script>
		config = {<?php
	foreach(read("data/config.csv")[0] as $key => $v){
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
else{
	die("Unzureichende Berechtigung");
}
?>
