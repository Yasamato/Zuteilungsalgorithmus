<?php
	function isLogin() {
		return isset($_SESSION['benutzer']);
	}

	function logout() {
		session_destroy();
		session_start();
	}

	function alert($msg) {
		echo "<script>alert('" . $msg . "');</script>";
	}

	function newlineBack($txt) {
	  $txt = str_replace("<br>", "\n", $txt);
	  $txt = str_replace("<br/>", "\n", $txt);
	  return str_replace("<br />", "\n", $txt);
	}

	function getProjektInfo($id) {
	  foreach (dbRead("../data/projekte.csv") as $projekt) {
	    if ($projekt["id"] == $id) {
	      return $projekt;
	    }
	  }
	}

	function checkBox($v) {
		return isset($_POST[$v]) && $_POST[$v] ? "true" : "false";
	}

	// htmlentities() ?
	function newlineRemove($txt) {
		return str_replace("\n", "<br>", $txt);
	}

	if (!file_exists("../data/config.csv")) {
		// define the header of the columns in the first row
		dbCreateFile("../data/config.csv", [
			"Stage",
			"Schüleranzahl",
			"MontagVormittag",
			"MontagVormittagHinweis",
			"MontagNachmittag",
			"MontagNachmittagHinweis",
			"DienstagVormittag",
			"DienstagVormittagHinweis",
			"DienstagNachmittag",
			"DienstagNachmittagHinweis",
			"MittwochVormittag",
			"MittwochVormittagHinweis",
			"MittwochNachmittag",
			"MittwochNachmittagHinweis",
			"DonnerstagVormittag",
			"DonnerstagVormittagHinweis",
			"DonnerstagNachmittag",
			"DonnerstagNachmittagHinweis",
			"FreitagVormittag",
			"FreitagVormittagHinweis",
			"FreitagNachmittag",
			"FreitagNachmittagHinweis"
		]);
		dbAdd("../data/config.csv", [
			0,
			800,
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"true",
			"",
			"false",
			""
		]);
	}
	$config = dbRead("../data/config.csv")[0];

	if (!file_exists("../data/projekte.csv")) {
		// define the header of the columns in the first row
		dbCreateFile("../data/projekte.csv", [
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
			"frMensa",
			"frNach"
		]);
	}

	if (!file_exists("../data/wahl.csv")) {
		// define the header of the columns in the first row
		dbCreateFile("../data/wahl.csv", [
			"uid",
			"vorname",
			"nachname",
			"stufe",
			"klasse",
			"wahl"
		]);
	}
?>
