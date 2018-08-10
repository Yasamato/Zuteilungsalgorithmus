<?php
//authentication
if(isLogin() && ($_SESSION['benutzer']['typ'] == "teachers" || $_SESSION['benutzer']['typ'] == "admin")){
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
	?>
	<div class="modal show" id="createOK" role="dialog" style="color: #212529;">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4>Erfolgreich eingereicht</h4>
        </div>
        <div class="modal-body">
          <p>
						Das Projekt <kbd><?php echo $_POST["pName"]; ?></kbd>
						mit dem Betreuer <kbd><?php echo $_POST["betreuer"]; ?></kbd>
						für <kbd><?php echo $_POST["minPlatz"]; ?></kbd>
						bis <kbd><?php echo $_POST["maxPlatz"]; ?></kbd>
						Schüler der Klassenstufe <kbd><?php echo $_POST["minPlatz"]; ?></kbd>
						bis <kbd><?php echo $_POST["maxPlatz"]; ?></kbd> wurde erstellt.
						Die Daten sind nun auf dem Server gespeichert und können von einem Admin editiert werden.
					</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>OK</button>
        </div>
      </div>

    </div>
  </div>
	<?php
}
else die("ungültige Berechtigung");
?>
