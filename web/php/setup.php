<?php
if (!file_exists("../data/config.csv")) {
  // define the header of the columns in the first row
  dbCreateFile("../data/config.csv", [
    "Stage",
    "MontagVormittag",
    "MontagVormittagHinweis",
    "MontagNachmittag",
    "MontagNachmittagHinweis",
    "DienstagVormittag",
    "DienstagVormittagHinweis",
    "DienstagNachmittag",
    "DienstagNachmittagHinweis",
    "MittwochVormittag",
    "MittwochVormittagHinweis",
    "MittwochNachmittag",
    "MittwochNachmittagHinweis",
    "DonnerstagVormittag",
    "DonnerstagVormittagHinweis",
    "DonnerstagNachmittag",
    "DonnerstagNachmittagHinweis",
    "FreitagVormittag",
    "FreitagVormittagHinweis",
    "FreitagNachmittag",
    "FreitagNachmittagHinweis"
  ]);
  dbAdd("../data/config.csv", [
    0,
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "true",
    "",
    "false",
    ""
  ]);
}
$config = dbRead("../data/config.csv")[0];

if (!file_exists("../data/projekte.csv")) {
  // define the header of the columns in the first row
  dbCreateFile("../data/projekte.csv", [
    "id",
    "name",
    "beschreibung",
    "betreuer",
    "minKlasse",
    "maxKlasse",
    "minPlatz",
    "maxPlatz",
    "vorraussetzungen",
    "sonstiges",
    "raum",
    "material",
    "moVor",
    "moMensa",
    "moNach",
    "diVor",
    "diMensa",
    "diNach",
    "miVor",
    "miMensa",
    "miNach",
    "doVor",
    "doMensa",
    "doNach",
    "frVor",
    "frMensa",
    "frNach"
  ]);
}

if (!file_exists("../data/wahl.csv")) {
  // define the header of the columns in the first row
  dbCreateFile("../data/wahl.csv", [
    "uid",
    "vorname",
    "nachname",
    "stufe",
    "klasse",
    "wahl",
    "projekt"
  ]);
}

if (!file_exists("../data/zwangszuteilung.csv")) {
  // define the header of the columns in the first row
  dbCreateFile("../data/zwangszuteilung.csv", [
    "uid",
    "vorname",
    "nachname",
    "stufe",
    "klasse",
    "projekt"
  ]);
}

if (!file_exists("../data/klassen.csv")) {
  // define the header of the columns in the first row
  dbCreateFile("../data/klassen.csv", [
    "stufe",
    "klasse",
    "anzahl"
  ]);
}


$zwangszuteilung = dbRead("../data/zwangszuteilung.csv");
usort($zwangszuteilung, function ($a, $b) {
  if (strtolower($a["nachname"]) == strtolower($b["nachname"])) {
    return strtolower($a["vorname"]) < strtolower($b["vorname"]) ? -1 : 1;
  }
  return strtolower($a["nachname"]) < strtolower($b["nachname"]) ? -1 : 1;
});

// Einlesen der Tabellen
$wahlen = dbRead("../data/wahl.csv");
foreach ($wahlen as $key => $student) {
  $wahlen[$key]["wahl"] = explode("§", $student["wahl"]);
}
foreach ($zwangszuteilung as $key => $zuteilung) {
  array_push($wahlen, [
    "uid" => $zuteilung["uid"],
    "vorname" => $zuteilung["vorname"],
    "nachname" => $zuteilung["nachname"],
    "stufe" => $zuteilung["stufe"],
    "klasse" => $zuteilung["klasse"],
    "wahl" => array(),
    "projekt" => $zuteilung["projekt"]
  ]);
}
usort($wahlen, function ($a, $b) {
  if (strtolower($a["nachname"]) == strtolower($b["nachname"])) {
    return strtolower($a["vorname"]) < strtolower($b["vorname"]) ? -1 : 1;
  }
  return strtolower($a["nachname"]) < strtolower($b["nachname"]) ? -1 : 1;
});

// Klassenliste mit den Schüleranzahlen
$klassenliste = dbRead("../data/klassen.csv");
uasort($klassenliste, function ($a, $b) {
  if ($a["stufe"] == $b["stufe"]) {
    return $a["klasse"] < $b["klasse"] ? -1 : 1;
  }
  return intval($a["stufe"]) < intval($b["stufe"]) ? -1 : 1;
});

// aufteilung aller Schüler-Wahlen in Klassen
$klassen = [];
foreach ($wahlen as $key => $student) {
  if (empty($student) || empty($student["uid"])) {
    continue;
  }
  if (empty($klassen[$student["klasse"]])) {
    $klassen[$student["klasse"]] = [[
      "stufe" => $student["stufe"],
      "klasse" => $student["klasse"]
      ]];
  }
  array_push($klassen[$student["klasse"]], $student);
}
// bereits vorhandene Datensätze mit den eingetragenen Datensätzen auffüllen
foreach ($klassenliste as $klasse) {
  if (empty($klassen[$klasse["klasse"]])) {
    $klassen[$klasse["klasse"]] = [[
      "stufe" => $klasse["stufe"],
      "klasse" => $klasse["klasse"]
    ]];
  }
}
// sortieren der Klasse nach Stufe und Klasse
uasort($klassen, function ($a, $b) {
  if ($a[0]["stufe"] == $b[0]["stufe"]) {
    return $a[0]["klasse"] < $b[0]["klasse"] ? -1 : 1;
  }
  return intval($a[0]["stufe"]) < intval($b[0]["stufe"]) ? -1 : 1;
});
// sortieren der Schülerlisten nach Nachname und Name
foreach ($klassen as $studentlist) {
  usort($studentlist, function ($a, $b) {
    // dummy-Wert
    if (empty($a["nachname"]) || empty($b["nachname"])) {
      return 0;
    }

    if (strtolower($a["nachname"]) == strtolower($b["nachname"])) {
      return strtolower($a["vorname"]) < strtolower($b["vorname"]) ? -1 : 1;
    }
    return strtolower($a["nachname"]) < strtolower($b["nachname"]) ? -1 : 1;
  });
}

$projekte = [];
if (isLogin()) {
  if ($_SESSION['benutzer']['typ'] == "teachers" || $_SESSION['benutzer']['typ'] == "admin") {
    $projekte = dbRead("../data/projekte.csv");
    foreach ($projekte as $key => $projekt) {
      $teilnehmer = [];
      foreach ($wahlen as $student) {
        if (!empty($student["projekt"]) && $student["projekt"] == $projekt["id"]) {
          array_push($teilnehmer, $student);
        }
      }
      usort($teilnehmer, function ($a, $b) {
        if (strtolower($a["nachname"]) == strtolower($b["nachname"])) {
          return strtolower($a["vorname"]) < strtolower($b["vorname"]) ? -1 : 1;
        }
        return strtolower($a["nachname"]) < strtolower($b["nachname"]) ? -1 : 1;
      });
      $projekte[$key]["teilnehmer"] = $teilnehmer;
    }
  }
  else {
    foreach (dbRead("../data/projekte.csv") as $p) {
      if ($p['minKlasse'] <= $_SESSION['benutzer']['stufe'] && $p['maxKlasse'] >= $_SESSION['benutzer']['stufe']) {
        array_push($projekte, $p);
      }
    }
  }
}
?>
