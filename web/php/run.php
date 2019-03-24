<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] == "admin") {
  die("Fehlende Berechtigung");
}

// generate the statistics how many places are available in each class
// initialize the data array
$stufen = [
  5 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  6 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  7 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  8 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  9 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  10 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  11 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ],
  12 => [
    "min" => 0,
    "max" => 0,
    "students" => 0
  ]
];

// read each project and add the max members to the affected classes
$pMin = 0;
$pMax = 0;
foreach (dbRead("../data/projekte.csv") as $p) {
  for ($i = 5; $i <= 12; $i++) {
		if ($p["minKlasse"] <= $i && $p["maxKlasse"] >= $i) {
			$stufen[$i]["min"] += $p["minPlatz"];
			$stufen[$i]["max"] += $p["maxPlatz"];
      $pMin += $p["minPlatz"];
      $pMax += $p["maxPlatz"];
		}
	}
}

// Gesamtanzahl der Schüler
$gesamtanzahl = 0;
foreach ($klassenliste as $klasse) {
  $gesamtanzahl += $klasse["anzahl"];

  // für die einzelnen Stufen
  for ($i = 5; $i <= 12; $i++) {
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
for ($i = 5; $i <= 12; $i++) {
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
  || $pMax < $gesamtanzahl
  || $pMin > $gesamtanzahl) {
    alert("Die Bedinungen zur Ausführung des Zuteilungsalgorithmus sind nicht erfüllt.");
}
else {
  // only linux.... drop win support
  function isRunning($pid){
      try {
          $result = shell_exec(sprintf("ps %d", $pid));
          return count(preg_split("/\n/", $result)) > 2;
      } catch(Exception $e) {
        var_dump($e);
      }
      return false;
  }

  $cmd = "sh ../FinishedAlgorithm/startAlgorithmus.sh";
  //exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
}
?>
