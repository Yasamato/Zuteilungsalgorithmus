<?php
//authentication
if(isLogin() && $_SESSION['benutzer']['typ'] == "teachers"){
	// Websitevariables
	$name = $_POST["inputProjektname"];
	$beschreibung = $_POST["inputBeschreibung"];
	$betreuer = $_POST["inputBetreuer"];
	$minKlasse =  $_POST["inputMinKlasse"];
	$maxKlasse =  $_POST["inputMaxKlasse"];
	$minPlaetze =  $_POST["inputMinPlaetze"];
	$maxPlaetze =  $_POST["inputMaxPlaetze"];
	$raumWunsch = $_POST["inputRaumwunsch"];
	$sonstiges = $_POST["inputSonstiges"];
	$besondereVorraussetzungen = $_POST["inputBesVoraus"];
	$material = $_POST["inputMaterial"];
	$mondayForenoon = $_POST["weekdayMondayForenoon"];
	$checkboxFoodMonday = $_POST["checkboxFoodMonday"];
	$mondayAfternoon = $_POST["weekdayMondayAfternoon"];
	$tuesdayForenoon = $_POST["weekdayTuesdayForenoon"];
	$checkboxFoodTuesday = $_POST["checkboxFoodTuesday"];
	$tuesdayAfternoon = $_POST["weekdayTuesdayAfternoon"];
	$wednesdayForenoon = $_POST["weekdayWednesdayForenoon"];
	$checkboxFoodWednesday = $_POST["checkboxFoodMonday"];
	$wednesdayAfternoon = $_POST["weekdayWednesdayAfternoon"];
	$thursdayForenoon = $_POST["weekdayThursdayForenoon"];
	$checkboxFoodThursday = $_POST["checkboxFoodThursday"];
	$thursdayAfternoon = $_POST["weekdayThursdayAfternoon"];
	$fridayForenoon = $_POST["weekdayFridayForenoon"];
	$fridayAfternoon = $_POST["weekdayFridayAfternoon"];


	// count the lines to get the index
	$linecount = count(file('csv/projekte.csv'));

	// create the data array to write into the csv
	$list = [
		$linecount,
		$name,
		$beschreibung,
		$betreuer,
		$minKlasse,
		$maxKlasse,
		$minPlaetze,
		$maxPlaetze,
		$sonstiges,
		$besondereVorraussetzungen,
		$raumWunsch, 
		$material,
		$mondayForenoon,
		$checkboxFoodMonday,
		$mondayAfternoon,
		$tuesdayForenoon,
		$checkboxFoodTuesday,
		$tuesdayAfternoon,
		$wednesdayForenoon,
		$checkboxFoodWednesday,
		$wednesdayAfternoon,
		$thursdayForenoon,
		$checkboxFoodThursday,
		$thursdayAfternoon,
		$fridayForenoon,
		$fridayAfternoon
	];
	// open the file
	$fp = fopen('csv/projekte.csv', 'a');

	// write the data and check for success
	if (!fputcsv($fp, $list,"#")) {
		die("Projekt konnte nicht gespeichert werden");
	}
	// close the file
	fclose($fp);
}
?>
