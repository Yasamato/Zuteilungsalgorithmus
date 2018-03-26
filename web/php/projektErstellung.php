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
	$config["Montag"] == "true" && (empty($_POST["moVor"]) || empty($_POST["moNach"])) ||
	$config["Dienstag"] == "true" && (empty($_POST["diVor"]) || empty($_POST["diNach"])) ||
	$config["Mittwoch"] == "true" && (empty($_POST["miVor"]) || empty($_POST["miNach"])) ||
	$config["Donnerstag"] == "true" && (empty($_POST["doVor"]) || empty($_POST["doNach"])) ||
	$config["Freitag"] == "true" && (empty($_POST["frVor"]) || empty($_POST["frNach"]))){
		die("Fehlende Angaben");
	}
	foreach($_POST as $post){
		if(strpos($post, "__#__") !== false || strpos($post, "__;__") !== false){
			die("Ungültige Zeichenkette: __#__ oder __;__");
		}
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
		return isset($_POST[$v]) && $_POST[$v] ? "Ja" : "Nein";
	}

	function getSafeString($v){
		return isset($_POST[$v]) ? str_replace("\n", "<br>", $_POST[$v]) : "";
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
			getSafeString("moVor"),
			checkBox("moMensa"),
			getSafeString("moNach"),
			getSafeString("diVor"),
			checkBox("diMensa"),
			getSafeString("diNach"),
			getSafeString("miVor"),
			checkBox("miMensa"),
			getSafeString("miNach"),
			getSafeString("doVor"),
			checkBox("doMensa"),
			getSafeString("doNach"),
			getSafeString("frVor"),
			getSafeString("frNach")
	]);
}
?>
