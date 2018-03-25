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

	//prepare data
	if (array_key_exists("inlineCheckbox1", $_POST)) {
		$monday = "true";
	} else {
		$monday = "false";
	}
	if (array_key_exists("inlineCheckbox2", $_POST)) {
		$tuesday = "true";
	} else {
		$tuesday = "false";
	}
	if (array_key_exists("inlineCheckbox3", $_POST)) {
		$wednesday = "true";
	} else {
		$wednesday = "false";
	}
	if (array_key_exists("inlineCheckbox4", $_POST)) {
		$thursday = "true";
	} else {
		$thursday = "false";
	}
	if (array_key_exists("inlineCheckbox5", $_POST)) {
		$friday = "true";
	} else {
		$friday = "false";
	}
	$values = [
		$_POST["stage"],
		$_POST["inputSchuelerAnzahl"],
		$monday,
		$tuesday,
		$wednesday,
		$thursday,
		$friday,
		$_POST["firstDay"]
	];

	// write the data and check for success
	setRow("data/config.csv", 0, $values);
	if(read("data/config.csv") == $config){
		die("Konfiguration wurde nicht gespeichert.");
	}
	else{
		//update config
		$config = read("data/config.csv");
	}
}
else{
	die("Unzureichende Berechtigung");
}
?>
