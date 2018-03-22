<?php
if(!empty(array_filter($_POST))) {
	$data = read("csv/config.csv")[0];
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


	$stage = $data["Stage"];
	$numberOfStudents = $_POST["inputSchuelerAnzahl"];
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
	$firstDay = $_POST["firstDay"];

	$values = [
		$stage,
		$numberOfStudents,
		$monday,
		$tuesday,
		$wednesday,
		$thursday,
		$friday,
		$firstDay
	];

	// open the file
	$fp = fopen('csv/config.csv', 'w');

	// write the data and check for success
	if (!fputcsv($fp, $names, "#") or !fputcsv($fp, $values, "#")) {
		die("Projekt konnte nicht gespeichert werden!");
	}
	// close the file
	fclose($fp);

}
?>
