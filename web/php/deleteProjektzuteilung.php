<?php
//authentication
if (isLogin() && $_SESSION['benutzer']['typ'] == "admin") {
  if (!empty($_POST["uid"])) {
    $student = [];
    foreach ($wahlen as $wahl) {
      if ($wahl["uid"] == $_POST["uid"]) {
        $student = $wahl;
        break;
      }
    }
    if (empty($student)) {
      alert("Der Schüler mit der ID " . $_POST["uid"] . " konnte nicht gefunden werden und wurde dementsprechend nicht gelöscht werden");
    }
    else {
      if (dbSet("../data/wahl.csv", "uid", $_POST["uid"], "projekt", "")) {
        alert("Die Projektzuteilung des Schülers '" . $student["vorname"] . " " . $student["nachname"] . "' mit der ID " . $_POST["uid"] . " wurde erfolgreich gelöscht");
      }
      else {
        alert("Löschen der Projektzuteilung des Schülereintrags mit der ID " . $_POST["uid"] . " ist fehlgeschlagen.");
      }
    }
  }
  else {
    die("Ungültige Anfrage");
  }
}
?>
