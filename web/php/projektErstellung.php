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
	empty($_POST["weekdayMondayAfternoon"]) ||
	empty($_POST["weekdayTuesdayForenoon"]) ||
	empty($_POST["weekdayTuesdayAfternoon"]) ||
	empty($_POST["weekdayWednesdayForenoon"]) ||
	empty($_POST["weekdayWednesdayAfternoon"]) ||
	empty($_POST["weekdayThursdayForenoon"]) ||
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
			"moVor",
			"moMensa",
			"moNach",
			"diVor",
			"diMensa",
			"diNach",
			"miVor",
			"miMensa",
			"miNach",
			"doVor",
			"doMensa",
			"doNach",
			"frVor",
			"frNach"
		]);
	}

	function checkBox($v){
		return (isset($v) && $v ? "Ja" : "Nein");
	}

	add("data/projekte.csv", [
			count(read('data/projekte.csv')),
			$_POST["pName"],
			str_replace("\n", "<br>", $_POST["beschreibung"]),
			$_POST["betreuer"],
			$_POST["minKlasse"],
			$_POST["maxKlasse"],
			$_POST["minPlatz"],
			$_POST["maxPlatz"],
			$_POST["sonstiges"],
			$_POST["vorraussetzungen"],
			$_POST["raum"],
			$_POST["material"],
			str_replace("\n", "<br>", $_POST["weekdayMondayForenoon"]),
			checkBox($_POST["checkboxFoodMonday"]),
			str_replace("\n", "<br>", $_POST["weekdayMondayAfternoon"]),
			str_replace("\n", "<br>", $_POST["weekdayTuesdayForenoon"]),
			checkBox($_POST["checkboxFoodTuesday"]),
			str_replace("\n", "<br>", $_POST["weekdayTuesdayAfternoon"]),
			str_replace("\n", "<br>", $_POST["weekdayWednesdayForenoon"]),
			checkBox($_POST["checkboxFoodMonday"]),
			str_replace("\n", "<br>", $_POST["weekdayWednesdayAfternoon"]),
			str_replace("\n", "<br>", $_POST["weekdayThursdayForenoon"]),
			checkBox($_POST["checkboxFoodThursday"]),
			str_replace("\n", "<br>", $_POST["weekdayThursdayAfternoon"]),
			str_replace("\n", "<br>", $_POST["weekdayFridayForenoon"]),
			str_replace("\n", "<br>", $_POST["weekdayFridayAfternoon"])
	]);
}
?>
