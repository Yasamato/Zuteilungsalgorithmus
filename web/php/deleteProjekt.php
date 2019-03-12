<?php
//authentication
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (!empty($_POST['action']) && $_POST['action'] == "deleteProjekt" && !empty($_POST["projekt"])) {
    $projekt = getProjektInfo($_POST["projekt"]);
    if (empty($projekt)) {
      error_log("Das Projekt mit der ID " . $_POST["projekt"] . " konnte nicht gefunden werden und dementsprechend nicht gelöscht werden", 0, "../data/error.log");
      alert("Zu löschendes Projekt nicht gefunden");
    }
    else {
      if (dbRemove("../data/projekte.csv", "id", $_POST["projekt"])) {
        alert("Das Projekt '" . $projekt["name"] . "' mit der ID " . $_POST["projekt"] . " wurde erfolgreich gelöscht");
        $entrysModified = [];
        foreach ($wahlen as $key => $wahl) {
          if (in_array($_POST["projekt"], $wahl["wahl"])) {
            array_push($entrysModified, $wahl);
            unset($wahl["wahl"][array_search($_POST["projekt"], $wahl["wahl"])]);
            dbSet("../data/wahl.csv", "id", $wahl["id"], "wahl", implode("§", $wahl["wahl"]));
          }
        }

        if (count($entrysModified) > 0) {
          $stringToPrint = "Aufgrund des Löschvorgangs, wurden bei " . count($entrysModified) . " Schülern die Wahlen bearbeitet. Folgende Schüler müssen ihre Wahl erneut abgeben:\r\n";
          foreach ($entrysModified as $entry) {
            $stringToPrint .= "\r\n" . $entry["nachname"] . " " . $entry["vorname"] . " aus Klasse " . $entry["klasse"];
          }
          alert($stringToPrint);
        }
      }
      else {
        error_log("Löschen des Projekts mit der ID " . $_POST["projekt"] . " fehlgeschlagen.", 0, "../data/error.log");
        alert("Es ist ein unerwartetes Problem aufgetreten beim Löschen des Projekts mit der ID " . $_POST["projekt"]);
      }
    }
  }
}
?>
