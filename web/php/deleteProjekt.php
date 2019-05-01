<?php
//authentication
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (!empty($_POST['action']) && $_POST['action'] == "deleteProjekt" && !empty($_POST["projekt"])) {
    $projekt = getProjektInfo($projekte, $_POST["projekt"]);
    if (empty($projekt)) {
      alert("Zu löschendes Projekt nicht gefunden");
    }
    else {
      if (dbRemove("../data/projekte.csv", "id", $_POST["projekt"])) {
        alert("Das Projekt '" . $projekt["name"] . "' wurde erfolgreich gelöscht");
        $entrysModified = [];
        foreach ($wahlen as $key => $wahl) {
          if (in_array($_POST["projekt"], $wahl["wahl"])) {
            array_push($entrysModified, $wahl);
            dbRemove("../data/wahl.csv", "uid", $wahl["id"]);
          }
        }
        foreach ($zwangszuteilung as $key => $zuteilung) {
          if ($_POST["projekt"] == $zuteilung["ergebnis"]) {
            array_push($entrysModified, $zuteilung);
            dbRemove("../data/zwangszuteilung", "uid", $zuteilung["uid"]);
          }
        }

        if (count($entrysModified) > 0) {
          $stringToPrint = "Aufgrund des Löschvorgangs, wurden bei " . count($entrysModified) . " Schülern die Wahlen gelöscht. Folgende Schüler müssen ihre Wahl erneut tätigen:\n";
          foreach ($entrysModified as $entry) {
            $stringToPrint .= "\n- " . $entry["nachname"] . " " . $entry["vorname"] . " aus Klasse " . $entry["klasse"];
          }
          alert($stringToPrint);
        }
      }
      else {
        alert("Es ist ein unerwartetes Problem aufgetreten beim Löschen des Projekts '" . $projekt["name"] . "'");
      }
    }
  }
}
?>
