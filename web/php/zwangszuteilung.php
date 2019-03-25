<?php
if (isLogin() && $_SESSION["benutzer"]["typ"] == "admin") {
  if (!empty($_POST["uid"]) && !empty($_POST["stufe"])
    && !empty($_POST["klasse"]) && !empty($_POST["vorname"])
    && !empty($_POST["nachname"]) && !empty($_POST["projekt"])) {
    if (count($_POST["uid"]) == count($_POST["stufe"])
      && count($_POST["stufe"]) == count($_POST["klasse"])
      && count($_POST["klasse"]) == count($_POST["vorname"])
      && count($_POST["vorname"]) == count($_POST["nachname"])
      && count($_POST["nachname"]) == count($_POST["projekt"])) {
      $data = [];
      for ($i = 0; $i < count($_POST["uid"]); $i++) {
        if (empty($_POST["uid"][$i])) {
          continue;
        }

        $doppelt = false;
        foreach ($data as $key => $value) {
          if ($value["uid"] == $_POST["uid"][$i]) {
            alert("Der Schüler " . $value["vorname"] . " " . $value["nachname"] . " aus der Klasse " . $value["klasse"] . " wurde mehrfach eingetragen. Der erste Wert für den Schüler wird hierbei übernommen.");
            $doppelt = true;
            break;
          }
        }
        if ($doppelt) {
          continue;
        }
        foreach ($wahlen as $key => $student) {
          if ($student["uid"] == $_POST["uid"][$i]) {
            dbRemove("../data/wahl.csv", "uid", $_POST["uid"][$i]);
            break;
          }
        }
        array_push($data, [
          "uid" => $_POST["uid"][$i],
          "stufe" => $_POST["stufe"][$i],
          "klasse" => $_POST["klasse"][$i],
          "vorname" => $_POST["vorname"][$i],
          "nachname" => $_POST["nachname"][$i],
          "projekt" => $_POST["projekt"][$i]
        ]);
      }
      dbWrite("../data/zwangszuteilung.csv", $data);
    }
    else {
      alert("Ungültige Angaben: Anzahl der Angaben stimmen nicht überein!");
    }
  }
  else {
    alert("Ungültige Angaben: Es fehlend Angaben");
  }
}
?>
