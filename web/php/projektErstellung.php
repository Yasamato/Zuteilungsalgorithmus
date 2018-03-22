<?php
//authentication
if(isLogin() && $_SESSION['benutzer']['typ'] == "teachers"){
	if(empty($_POST["pName"]) ||
	empty($_POST["beschreibung"]) ||
	empty($_POST["betreuer"]) ||
	empty($_POST["minKlasse"]) ||
	empty($_POST["maxKlasse"]) ||
	empty($_POST["minPlatz"]) ||
	empty($_POST["maxPlatz"]) ||
	empty($_POST["weekdayMondayForenoon"]) ||
	empty($_POST["checkboxFoodMonday"]) ||
	empty($_POST["weekdayMondayAfternoon"]) ||
	empty($_POST["weekdayTuesdayForenoon"]) ||
	empty($_POST["checkboxFoodTuesday"]) ||
	empty($_POST["weekdayTuesdayAfternoon"]) ||
	empty($_POST["weekdayWednesdayForenoon"]) ||
	empty($_POST["checkboxFoodMonday"]) ||
	empty($_POST["weekdayWednesdayAfternoon"]) ||
	empty($_POST["weekdayThursdayForenoon"]) ||
	empty($_POST["checkboxFoodThursday"]) ||
	empty($_POST["weekdayThursdayAfternoon"]) ||
	empty($_POST["weekdayFridayForenoon"]) ||
	empty($_POST["weekdayFridayAfternoon"])){
		die("Fehlende Angaben");
	}
	if(!file_exists("data/projekte.csv")){
		createFile("data/projekte.csv", [
			"id",
			"name",
			"beschreibung",
			"betreuer",
			"minKlasse",
			"maxKlasse",
			"minPlatz",
			"maxPlatz",
			"vorraussetzungen",
			"sonstiges",
			"raum",
			"material",
			"weekdayMondayForenoon",
			"checkboxFoodMonday",
			"weekdayMondayAfternoon",
			"weekdayTuesdayForenoon",
			"checkboxFoodTuesday",
			"weekdayTuesdayAfternoon",
			"weekdayWednesdayForenoon",
			"checkboxFoodMonday",
			"weekdayWednesdayAfternoon",
			"weekdayThursdayForenoon",
			"checkboxFoodThursday",
			"weekdayThursdayAfternoon",
			"weekdayFridayForenoon",
			"weekdayFridayAfternoon"
		]);
	}
	add("data/projekte.csv", [
			count(read('data/projekte.csv')),
			$_POST["pName"],
			$_POST["beschreibung"],
			$_POST["betreuer"],
			$_POST["minKlasse"],
			$_POST["maxKlasse"],
			$_POST["minPlatz"],
			$_POST["maxPlatz"],
			$_POST["sonstiges"],
			$_POST["vorraussetzungen"],
			$_POST["raum"],
			$_POST["material"],
			$_POST["weekdayMondayForenoon"],
			$_POST["checkboxFoodMonday"],
			$_POST["weekdayMondayAfternoon"],
			$_POST["weekdayTuesdayForenoon"],
			$_POST["checkboxFoodTuesday"],
			$_POST["weekdayTuesdayAfternoon"],
			$_POST["weekdayWednesdayForenoon"],
			$_POST["checkboxFoodMonday"],
			$_POST["weekdayWednesdayAfternoon"],
			$_POST["weekdayThursdayForenoon"],
			$_POST["checkboxFoodThursday"],
			$_POST["weekdayThursdayAfternoon"],
			$_POST["weekdayFridayForenoon"],
			$_POST["weekdayFridayAfternoon"]
	]);
}
?>
