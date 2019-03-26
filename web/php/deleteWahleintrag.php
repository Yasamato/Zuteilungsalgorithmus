<?php
//authentication
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (!empty($_POST["studentID"])) {
    $student = [];
    foreach ($wahlen as $wahl) {
      if ($wahl["uid"] == $_POST["studentID"]) {
        $student = $wahl;
        break;
      }
    }
    $zwangszugeteilt = false;
    foreach ($zwangszuteilung as $zuteilung) {
      if ($zuteilung["uid"] == $_POST["studentID"]) {
        $zwangszugeteilt = true;
        break;
      }
    }
    if (empty($student)) {
      error_log("Der Schüler mit der ID " . $_POST["studentID"] . " konnte nicht gefunden werden und dementsprechend nicht gelöscht werden", 0, "../data/error.log");
      alert("Zu löschenden Schüler nicht gefunden");
    }
    else {
      if (dbRemove("../data/" . ($zwangszugeteilt ? "zwangszuteilung.csv" : "wahl.csv"), "uid", $_POST["studentID"])) {
        alert("Der Schüler '" . $student["vorname"] . " " . $student["nachname"] . "' mit der ID " . $_POST["studentID"] . " wurde erfolgreich gelöscht");
      }
      else {
        error_log("Löschen des Schülereintrags mit der ID " . $_POST["studentID"] . " ist fehlgeschlagen.", 0, "../data/error.log");
        alert("Löschen des Schülereintrags mit der ID " . $_POST["studentID"] . " ist fehlgeschlagen.");
      }
    }
  }
  else {
    die("Ungültige Anfrage");
  }
}
?>
