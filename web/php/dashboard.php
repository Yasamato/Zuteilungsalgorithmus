<?php
if(isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
	$data = read("data/config.csv")[0];
	$names = [
		"Stage",
		"Schüleranzahl",
		"Montag",
		"Dienstag",
		"Mittwoch",
		"Donnerstag",
		"Freitag",
		"Schule_am_ersten_Vormittag"
	];

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
	/*
	$tuesday = ($_POST["inlineCheckbox2"] == "true");
	$wednesday = ($_POST["inlineCheckbox3"] == "true");
	$thursday = ($_POST["inlineCheckbox4"] == "true");
	$friday = ($_POST["inlineCheckbox5"] == "true");
	*/

	$values = [
		$config['Stage'],
		$_POST["inputSchuelerAnzahl"],
		$monday,
		$tuesday,
		$wednesday,
		$thursday,
		$friday,
		$_POST["firstDay"]
	];
	setRow("data/config.csv", 0, $values);
	if(read("data/config.csv") == $config){
		die("Konfiguration wurde nicht gespeichert.");
	}
	else{
		$config = read("data/config.csv");
	}
}
else{
	die("Unzureichende Berechtigung");
}
?>
