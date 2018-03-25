<?php
if(isLogin()) {// hard gecoded für präsi
	if(!empty($_POST['wahl']) && count($_POST['wahl']) == 4 && $_SESSION['benutzer']['typ'] != "teachers"){
		$projekte = read("csv/projekte.csv");
		for($i = 0; $i < count($_POST['wahl']); $i++){
			if($projekte[$i]['min Klasse'] <= $_SESSION['benutzer']['stufe'] && $projekte[$i]['max Klasse'] >= $_SESSION['benutzer']['stufe']){
				if(!file_exists("data/schueler.csv")){
					createFile("data/schueler.csv", [
						"uid",
						"vorname",
						"nachname",
						"stufe",
						"klasse",
						"wahl"
					]);
				}
				add("data/schueler.csv", [
					$_SESSION['benutzer']['uid'],
					$_SESSION['benutzer']['vorname'],
					$_SESSION['benutzer']['nachname'],
					$_SESSION['benutzer']['stufe'],
					$_SESSION['benutzer']['klasse'],
					implode($_POST['wahl'], "§")
				]);
			}
		}
		print("Erfolgreich eingetragen");
	}
	else{
		die("Ungültige Wahl");
	}
}
else{
	die("Anmeldung abgelaufen!");
}
?>
