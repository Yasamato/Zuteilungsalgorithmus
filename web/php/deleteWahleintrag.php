<?php
//authentication
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (!empty($_POST["uid"])) {
    $noDeleted = 0;
    foreach ($_POST["uid"] as $uid) {
      $student = [];
      foreach ($wahlen as $wahl) {
        if ($wahl["uid"] == $uid) {
          $student = $wahl;
          break;
        }
      }
      $zwangszugeteilt = false;
      foreach ($zwangszuteilung as $zuteilung) {
        if ($zuteilung["uid"] == $uid) {
          $zwangszugeteilt = true;
          break;
        }
      }
      if (empty($student)) {
        alert("Der Schüler mit der ID " . $uid . " konnte nicht gefunden werden und wurde dementsprechend nicht gelöscht werden");
      }
      else {
        if (dbRemove("../data/" . ($zwangszugeteilt ? "zwangszuteilung.csv" : "wahl.csv"), "uid", $uid)) {
          if (count($_POST["uid"]) < 4) {
            alert("Der Schüler '" . $student["vorname"] . " " . $student["nachname"] . "' mit der ID " . $uid . " wurde erfolgreich gelöscht");
          }
        }
        else {
          $noDeleted += 1;
          alert("Löschen des Schülereintrags mit der ID " . $uid . " ist fehlgeschlagen.");
        }
      }
    }
    if (count($_POST["uid"]) > 3) {
      if ($noDeleted > 0) {
        alert((count($_POST["uid"]) - $noDeleted) . " von " . count($_POST["uid"]) . " Schülern wurden erfolgreich gelöscht");
      }
      else {
        alert(count($_POST["uid"]) . " Schüler wurden erfolgreich gelöscht");
      }
    }
  }
  else {
    die("Ungültige Anfrage");
  }
}
?>
