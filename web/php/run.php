<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Fehlende Berechtigung");
}

// generate the statistics how many places are available in each class
// initialize the data array
$stufen = [];
for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
  $stufen[$i] = [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ];
}

// read each project and add the max members to the affected classes
$pMin = 0;
$pMax = 0;
foreach (dbRead("../data/projekte.csv") as $p) {
  for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
		if ($p["minKlasse"] <= $i && $p["maxKlasse"] >= $i) {
			$stufen[$i]["min"] += $p["minPlatz"];
			$stufen[$i]["max"] += $p["maxPlatz"];
		}
	}
  $pMin += $p["minPlatz"];
  $pMax += $p["maxPlatz"];
}

// Gesamtanzahl der Schüler
$gesamtanzahl = 0;
foreach ($klassenliste as $klasse) {
  $gesamtanzahl += $klasse["anzahl"];

  // für die einzelnen Stufen
  for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
    if ($i == $klasse["stufe"]) {
			$stufen[$i]["students"] += $klasse["anzahl"];
			$stufen[$i]["students"] += $klasse["anzahl"];
    }
  }
}

// Zählen der bereits gewählten Schüler
$klassenFertig = 0;
$nichtEingetrageneKlassen = [];
foreach ($klassen as $klasse => $liste) {
  $found = false;
  foreach ($klassenliste as $k) {
    if ($klasse == $k["klasse"]) {
      if (count($liste) - 1 == $k["anzahl"]) {
        $klassenFertig += 1;
      }
      $found = true;
      break;
    }
  }
  if (!$found) {
    array_push($nichtEingetrageneKlassen, $klasse);
  }
}

$error = false;
foreach ($klassenliste as $klasse) {
  if (count($klassen[$klasse["klasse"]]) - 1 > $klasse["anzahl"]) {
    $error = true;
    break;
  }
}
for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
  if ($stufen[$i]["max"] < $stufen[$i]["students"]) {
    $error = true;
    break;
  }
}
if ($error
  || $config["Stage"] != 4
  || $gesamtanzahl != count($wahlen)
  || !empty($nichtEingetrageneKlassen)
  || $klassenFertig != count($klassenliste)
  || $pMax < $gesamtanzahl) {
    alert("Die Bedinungen zur Ausführung des Zuteilungsalgorithmus sind nicht erfüllt.");
}
else {
  if (($fh = fopen("../FinishedAlgorithm/projekte2.csv", "w")) === false) {
    die("Mangelnde Zugriffsberechtigung auf den Ordner FinishedAlgorithm");
  }
  foreach ($projekte as $projekt) {
    fwrite($fh, $projekt["id"] . "," . ($projekt["minPlatz"] - count($projekt["teilnehmer"])) . "," . ($projekt["maxPlatz"] - count($projekt["teilnehmer"])));
    if ($projekt["id"] != $projekte[count($projekte) - 1]["id"]) {
      fwrite($fh, "\n");
    }
  }
  fclose($fh);

  if (($fh = fopen("../FinishedAlgorithm/nurSchueler2.csv", "w")) === false) {
    die("Mangelnde Zugriffsberechtigung auf den Ordner FinishedAlgorithm");
  }
  foreach ($wahlen as $wahl) {
    if (empty($wahl["wahl"])) {
      continue;
    }
    $string = $wahl["uid"] . "," . $wahl["nachname"] . "," . $wahl["vorname"];
    for ($i = 0; $i < count($wahl["wahl"]) ; $i++) {
      $string .= "," . $wahl["wahl"][$i];
    }
    fwrite($fh, $string);
    if ($wahl["uid"] != $wahlen[count($wahlen) - 1]["uid"]) {
      fwrite($fh, "\n");
    }
  }
  fclose($fh);

  $cmd = "java -jar ../FinishedAlgorithm/Algorithmus.jar 2 1000000 '../FinishedAlgorithm/projekte2.csv' ',ImM' '../FinishedAlgorithm/nurSchueler2.csv' ',KNV1234'";
  $outputfile = "../data/algorithmus.log";
  $pidfile = "../data/algorithmus.pid";
  exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
  die("");
}
?>
