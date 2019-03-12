<?php
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (empty($_POST["stufe"]) || empty($_POST["klasse"]) || empty($_POST["anzahl"]) || count($_POST["stufe"]) != count($_POST["klasse"]) || count($_POST["stufe"]) != count($_POST["anzahl"])) {
    error_log("Datensatz fehlerhaft!! Änderung der Klassenlisten ist fehlgeschlagen. Die Anzahl an Datensätzen stimmen in den Spalten nicht überein", 0, "../data/error.log");
    die("Datensatz fehlerhaft!! Änderung der Klassenlisten ist fehlgeschlagen. Die Anzahl an Datensätzen stimmen in den Spalten nicht überein");
  }

  // bereite die Daten auf
  $values = [];
  for ($i = 0; $i < count($_POST["stufe"]); $i++) {
    // skippen von leeren Einträgen
    if (empty($_POST["stufe"][$i]) || empty($_POST["klasse"][$i]) || empty($_POST["anzahl"][$i])) {
      continue;
    }
    // Validierung
    if ($_POST["stufe"][$i] < 5 || $_POST["stufe"][$i] > 12 || $_POST["anzahl"][$i] <= 0) {
      error_log("Datensatz fehlerhaft!! Änderung der Klassenlisten ist fehlgeschlagen. Die Werte haben einen unrealistischen Betrag", 0, "../data/error.log");
      die("Datensatz fehlerhaft!! Änderung der Klassenlisten ist fehlgeschlagen. Die Werte haben einen unrealistischen Betrag");
    }

    array_push($values, array(
      "stufe" => $_POST["stufe"][$i],
      "klasse" => $_POST["klasse"][$i],
      "anzahl" => $_POST["anzahl"][$i]
    ));
  }

  usort($values, function ($a, $b) {
    if ($a["stufe"] == $b["stufe"]) {
      return $a["klasse"] < $b["klasse"] ? -1 : 1;
    }
    return intval($a["stufe"]) < intval($b["stufe"]) ? -1 : 1;
  });
  if ($klassenliste == $values) {
    alert("Es wurden keine Änderungen vorgenommen da die Datensätze identisch sind");
  }
  else {
  	// write the data and check for success
  	dbWrite("../data/klassen.csv", $values);
  	if ($klassenliste == dbRead("../data/klassen.csv")) {
  		error_log("Die Änderung der Einstellung in der Datei ../data/klassen.csv von '" . json_encode($klassenliste) . "' zu '" . json_encode($values) . "' ist fehlgeschlagen", 0, "../data/error.log");
  		die("Die Änderung der Einstellung in der Datei ../data/klassen.csv von '" . json_encode($klassenliste) . "' zu '" . json_encode($values) . "' ist fehlgeschlagen");
  	}
  	else {
      // aktualisere die Einträge
    	$klassenliste = dbRead("../data/klassen.csv");
    	usort($klassenliste, function ($a, $b) {
    		if ($a["stufe"] == $b["stufe"]) {
    			return $a["klasse"] < $b["klasse"] ? -1 : 1;
    		}
    		return intval($a["stufe"]) < intval($b["stufe"]) ? -1 : 1;
    	});
      alert("Die Klassenlisten wurden erfolgreich aktualisiert");
  	}
  }
}
else {
	die("Unzureichende Berechtigung");
}
?>
