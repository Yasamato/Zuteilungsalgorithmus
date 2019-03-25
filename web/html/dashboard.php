<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Zugriff verweigert");
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

$showErrorModal = false;
$errorIncluded = false;
?>

<!-- Fehldermeldungs-Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Warnungen und Fehlermeldungen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
      <?php
      // Fehlende Klassseneinträge
      foreach ($nichtEingetrageneKlassen as $klasse) {
        $showErrorModal = true;
        $errorIncluded = true;
        ?>
        <div class="alert alert-danger" role="alert">
          Die <strong>Klasse <?php echo $klasse; ?></strong> konnte nicht gefunden werden. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a>.
        </div><?php
      }

      // Mehr Schüler als eingetragen
      foreach ($klassenliste as $klasse) {
        if (count($klassen[$klasse["klasse"]]) - 1 > $klasse["anzahl"]) {
          $showErrorModal = true;
          $errorIncluded = true;
          ?>
        <div class="alert alert-danger" role="alert">
          Die <strong>Klasse <?php echo $klasse["klasse"]; ?></strong> hat mehr Schüler als eingetragen. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a>.
        </div><?php
        }
      }

      // Wahlfortschritt nach Schülern
      if ($config["Stage"] > 2 && $gesamtanzahl != count($wahlen)) {
        $showErrorModal = true;
        if ($config["Stage"] > 3) {
          $errorIncluded = true;
        }
        ?>
        <div class="alert alert-<?php echo $config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger"; ?>" role="alert">
          Es ha<?php echo $gesamtanzahl > 1 ? "ben" : "t" ?> nur <?php echo count($wahlen) . " von " . $gesamtanzahl; ?> Schülern gewählt.
          <?php if ($config["Stage"] < 4) { ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        <?php } ?>
        </div>
        <?php
      }
      // Wahlfortschritt nach Klassen
      if ($config["Stage"] > 2 && $klassenFertig != count($klassenliste)) {
        $showErrorModal = true;
        if ($config["Stage"] > 3) {
          $errorIncluded = true;
        }
        ?>
        <div class="alert alert-<?php echo $config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger"; ?>" role="alert">
          Es ha<?php echo $klassenFertig > 1 ? "ben" : "t" ?> nur <?php echo $klassenFertig . " von " . count($klassenliste); ?> Klassen vollständig gewählt.
          <?php if ($config["Stage"] < 4) { ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        <?php } ?>
        </div>
        <?php
      }

      // Ausreichend Plätze für Schüler
      if ($pMin > $gesamtanzahl) {
        $showErrorModal = true;
        ?>
        <div class="alert alert-warning" role="alert">
          Die von allen Projekten summierte Mindestteilnehmeranzahl ist größer der Gesamtschülerzahl. Falls nicht Projekte nicht stattfinden sollen, passen Sie bitte die Mindestteilnehmeranzahl an.
        </div><?php
      }
      if ($pMax < $gesamtanzahl) {
        $showErrorModal = true;
        $errorIncluded = true;
        ?>
        <div class="alert alert-danger" role="alert">
          Die von allen Projekten summierte Maximalteilnehmeranzahl ist kleiner der Gesamtschülerzahl. Bitte erweitern sie die Maximalzahl bestehender Projekte oder fügen sie weitere Projekte hinzu.
        </div><?php
      }

      // Platz pro Stufe
      for ($i = 5; $i <= 12; $i++) {
        if ($stufen[$i]["min"] > $stufen[$i]["students"]) {
          ?>
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Die von allen Projekten summierte Mindestteilnehmeranzahl für die <strong>Klassenstufe <?php echo $i; ?></strong> ist größer der Schüleranzahl der Stufe. Dies kann zu Problemen führen.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div><?php
        }
        if ($stufen[$i]["max"] < $stufen[$i]["students"]) {
          $errorIncluded = true;
          ?>
          <div class="alert alert-danger" role="alert">
            Die von allen Projekten summierte Maximalteilnehmeranzahl für die <strong>Klassenstufe <?php echo $i; ?></strong> ist kleiner der Schüleranzahl der Stufe. Bitte erweitern sie die Maximalzahl bestehender Projekte oder fügen sie weitere Projekte hinzu.
          </div><?php
        }
      }
      ?>
      </div>
    </div>
  </div>
</div>

<!-- Fehldermeldungen -->
<div class="container">
  <?php
  if ($showErrorModal && $errorIncluded) {
    ?>
    <div class="alert alert-danger" role="alert">
      Es sind Fehler aufgetreten. <a href="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.
    </div>
    <?php
  }
  elseif ($showErrorModal) {
    ?>
    <div class="alert alert-warning" role="alert">
      Es sind Warnmeldungen und Hinweise aufgetreten. <a href="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.
    </div>
    <?php
  }
  if ($config["Stage"] == 4) {
  ?>
  <div class="alert alert-<?php echo $showErrorModal ? "danger" : "success"; ?>" role="alert">
    <?php
    if ($showErrorModal) {
      ?>
    Aufgrund der obigen Fehler kann momentan keine Auswertung durchgeführt werden. Bitte korrigieren sie evtl. fehlende oder inkorrekte Angaben.
      <?php
    }
    else {
    ?>
    <h4 class="alert-heading">Bereit zur Auswertung</h4>
    <p>
      Die Wahlphase wurde erflogreich abgeschlossen und somit kann die Auswertung durch den Zuteilungsalgorithmus vom Admin initialisiert werden.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="runZuteilungsalgorithmus">
      <button type="submit" class="btn btn-primary">
        Starten
      </a>
    </form>
    <?php
    }
    ?>
  </div>
  <?php
  }
  ?>
</div>


<!-- Einstellungs-Modal -->
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title" id="configModalLabel">Projektwahlkonfiguration</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="configForm" method="post">
        <div class="modal-body">
          <h5>Allgemeine Einstellungen</h5>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Aktuelle Phase:</label>
            <div class="col-sm-10">
              <select class="form-control" name="stage" id="stageSelect" aria-describedby="stageHelper">
                <?php
              	$stages = [
              		'<option value="0">#1 Nicht veröffentlicht</option>',
              		'<option value="1">#2 Projekte können eingereicht werden</option>',
              		'<option value="2">#3 Projekt-Einreichung geschlossen</option>',
              		'<option value="3">#4 Wahl-Interface geöffnet</option>',
              		'<option value="4">#5 Wahlen abgeschlossen</option>'
              	];
              	echo $stages[$config["Stage"]];
              	for ($i = 0; $i < 5; $i++) {
              		if ($i == $config["Stage"]) {
              			continue;
              		}
              		echo $stages[$i];
              	}
                ?>
              </select>
            </div>
          </div>
          <small id="stageHelper" class="form-text text-muted">
            <ul>
              <li>"Nicht veröffentlicht": Keiner hat Zugriff außer der Admin</li>
              <li>"Projekte können eingereicht werden": Änderungen an den Einstellungen zu den Projekten können nicht mehr getätigt werden, jedoch die Hinweise vom Admin verändert werden sowie über das Lehrer-Interface Projekte eingesehen, bearbeitet und eingereicht werden</li>
              <li>"Projekt-Einreichung geschlossen": Es können keine weiteren Projekte mehr von den Lehrern eingereicht werden. Editierungen bereits bestehender Projekte ist weiterhin möglich durch den Admin sowie die Lehrer</li>
              <li>"Wahl-Interface geöffnet": Die Schüler können sich nun mit ihren Login-Daten anmelden und aus ihrem Projekt-Pool ihre Wahl wählen. Die Lehrerschaft hat nun nur noch Zugriff auf die Liste mit den Projekten, kann diese noch bearbeiten, jedoch nicht mehr einreichen (nur der Admin)</li>
              <li>"Wahl abgeschlossen": Der Schüler-Zugriff wird geschlossen, Lehrer können die Liste ansehen. Änderungen können nur noch händisch von einem Admin getätigt werden. Die Auswertung wird durch einen Admin durchgeführt</li>
            </ul>
          </small>
          <h5>Klassendatensätze</h5>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#studentsInKlassen">
            Einträge bearbeiten
          </button>
          <small class="form-text text-muted">
            Tragen Sie alle Klassen bitte in dieses Formular ein und speichern Sie dieses, damit Ihnen eine Übersicht zur Verfügung steht welche Klassen noch nicht vollständig gewählt haben.
          </small>

          <hr class="my-4">
          <h5>Projekt-Einstellungen</h5>
          <small class="form-text text-muted">
            Stellen sie die Dauer der Projektwoche ein sowie ggf. Hinweise für die Lehrer zur Projekterstellung.
            Diese Anmerkungen werden als zusätzliche Information beim Einreichen von Projekten beim entsprechenden Feld angezeigt.
            Durch das Auswählen der Checkboxen wird festgelegt, ob Projekte an dem jeweiligem Vor-/Nachmittag statt finden.
          </small>
          <br>
          <table class="table table-dark table-striped">
            <thead class="thead-dark">
              <tr>
                <th>
                  Wochentag
                </th>
                <th>
                  <label class="form-check-label">Montag</label>
                </th>
                <th>
                  <label class="form-check-label">Dienstag</label>
                </th>
                <th>
                  <label class="form-check-label">Mittwoch</label>
                </th>
                <th>
                  <label class="form-check-label">Donnerstag</label>
                </th>
                <th>
                  <label class="form-check-label">Freitag</label>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>Vormittags</th>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[montag][vormittag]" <?php echo $config["MontagVormittag"] == "true" ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[montag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MontagVormittagHinweis"]) ? $config["MontagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[dienstag][vormittag]" <?php echo $config["DienstagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[dienstag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DienstagVormittagHinweis"]) ? $config["DienstagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[mittwoch][vormittag]" <?php echo $config["MittwochVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[mittwoch][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MittwochVormittagHinweis"]) ? $config["MittwochVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[donnerstag][vormittag]" <?php echo $config["DonnerstagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[donnerstag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DonnerstagVormittagHinweis"]) ? $config["DonnerstagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[freitag][vormittag]" <?php echo $config["FreitagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[freitag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["FreitagVormittagHinweis"]) ? $config["FreitagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
              </tr>

              <tr>
                <th>Nachmittags</th>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[montag][nachmittag]" <?php echo $config["MontagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[montag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MontagNachmittagHinweis"]) ? $config["MontagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[dienstag][nachmittag]" <?php echo $config["DienstagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[dienstag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DienstagNachmittagHinweis"]) ? $config["DienstagNachmittagHinweis"] : "");

                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[mittwoch][nachmittag]" <?php echo $config["MittwochNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[mittwoch][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MittwochNachmittagHinweis"]) ? $config["MittwochNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[donnerstag][nachmittag]" <?php echo $config["DonnerstagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[donnerstag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DonnerstagNachmittagHinweis"]) ? $config["DonnerstagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[freitag][nachmittag]" <?php echo $config["FreitagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[freitag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["FreitagNachmittagHinweis"]) ? $config["FreitagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" name="action" value="updateConfiguration" class="btn btn-primary">Speichere Änderungen</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Projekte-Modal -->
<div class="modal fade" id="projekteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projekte</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Name</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Platz</th>
            </tr>
          </thead>
          <tbody><?php
          if (empty($projekte)) {
            echo "
            <tr>
              <td>
                Bisher wurden keine Projekte eingereicht
              </td>
            </tr>";
          }
          foreach ($projekte as $key => $projekt) {
            echo '
            <tr>
              <td><a href="javascript:;" class="btn btn-success" onclick="showProjektInfoModal(projekte[' . $key . ']);">Info</a> ' . $projekt["name"] . '</td>
              <td>' . $projekt["betreuer"] . '</td>
              <td>' . $projekt["minKlasse"] . '-' . $projekt["maxKlasse"] . '</td>
              <td>' . $projekt["minPlatz"] . '-' . $projekt["maxPlatz"] . '</td>
            </tr>';
          }
          ?>

          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Schüler-Modal -->
<div class="modal fade" id="schuelerModal" tabindex="-1" role="dialog" aria-labelledby="schuelerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüler</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button onclick="javascript: $('#zwangszuteilungModal').modal('show');" type="button" class="btn btn-success">Zwangszuteilungen</button>
        <br>
        <small class="text-muted">Das Ergebnis der Auswertung ist erst verfügbar sobald die Auswertung durch den Admin durchgeführt wurde. Die Auswertung kann erst im Admin-Panel durchgeführt werden, sobald die Wahlen geschlossen sind.</small>

        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Klasse</th>
              <th class="sticky-top">Vorname</th>
              <th class="sticky-top">Nachname</th>
              <th class="sticky-top">Wahl</th>
              <th class="sticky-top">Ergebnis</th>
            </tr>
          </thead>
          <tbody><?php
          if (empty($wahlen)) {
            echo "
            <tr>
              <td>
                Bisher wurde keine Wahl getätigt
              </td>
            </tr>";
          }
          foreach ($klassen as $klasse) {
            foreach ($klasse as $key => $student) {
              if ($key == 0) {
                continue;
              }
              echo '
            <tr>
              <td>' . $student["stufe"] . '</td>
              <td>' . $student["klasse"] . '</td>
              <td>' . $student["vorname"] . '</td>
              <td>' . $student["nachname"] . '</td>
              <td>';
                if (!empty($student["wahl"])) {
                  echo '
                <ol>';
                  foreach ($student["wahl"] as $wahl) {
                    $p = null;
                    foreach ($projekte as $key => $projekt) {
                      if ($projekt["id"] == $wahl) {
                        $p = $key;
                        break;
                      }
                    }
                    echo '
                  <li>
                    <a href="javascript:;" onclick="showProjektInfoModal(projekte[' . $p . ']);">
                      ' . getProjektInfo($wahl)["name"] . '
                    </a>
                  </li>';
                  }
                  echo '
                </ol>';
                }
                else {
                  echo "Zugeteilt";
                }

                echo '
              </td>
              <td>';

              if (empty($student["ergebnis"])) {
                echo "N/A";
              }
              else {
                $p = null;
                foreach ($projekte as $key => $projekt) {
                  if ($projekt["id"] == $student["ergebnis"]) {
                    $p = $key;
                    break;
                  }
                }
                echo '
                <a href="javascript:;" onclick="showProjektInfoModal(projekte[' . $p . ']);">
                  ' . getProjektInfo($student["ergebnis"])["name"] . '
                </a>';
              }
              echo '
              </td>
            </tr>';
            }
          }
          ?>

          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Zwangszuteilungs-Modal -->
<div class="modal fade" id="zwangszuteilungModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Zwangszuteilungen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="post" id="zwangszuteilungForm">
        <div class="modal-body">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" name="action" value="updateZwangszuteilung" class="btn btn-primary">Änderung speichern</button>
          <br>
          <small class="text-muted">
            Um Schüler Zwangszuzuteilen tragen Sie bitte die U-ID (Login-Name des Schülers) korrekt ein und wählen sie das entsprechende Projekt aus.
            Falls sie bereits ein Projekt ausgewählt haben, färbt sich der Knopf zur Projektauswahl grün und die Beschriftung ändert sich zu "Ändern".
            Es kann jedoch weiterhin jederzeit das ausgewwählte Projekt geändert werden.
            Unten sehen sie einen beispielhaften Eintrag.
            Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Schüler hinzufügen &#10010;".
            Um einen Eintrag zu entfernen betätigen sie das rote Kreuz rechts vom Eintrag.
            Bitte beachten Sie, dass unvolständige Einträge beim Speichern gelöscht werden.
          </small>

          <table class="table table-dark">
            <tbody>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" value="mustmax" readonly>
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="5" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="5a" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="Max" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="Mustermann" readonly>
                </td>
                <td>
                  <button type="button" class="btn btn-success" disabled>Ändern</button>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" disabled>
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <small class="text-muted">
            Tragen Sie die echten Werte bitte in der nachfolgenden Tabelle ein.
          </small>
          <table class="table table-dark table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th class="sticky-top">U-ID</th>
                <th class="sticky-top">Stufe</th>
                <th class="sticky-top">Klasse</th>
                <th class="sticky-top">Vorname</th>
                <th class="sticky-top">Nachname</th>
                <th class="sticky-top">Projekt</th>
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody><?php
            foreach ($zwangszuteilung as $student) {
              ?>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" value="<?php echo $student['uid']; ?>" name="uid[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="<?php echo $student['stufe']; ?>" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="<?php echo $student['klasse']; ?>" name="klasse[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="<?php echo $student['vorname']; ?>" name="vorname[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="<?php echo $student['nachname']; ?>" name="nachname[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="hidden" class="form-control" value="<?php echo $student['projekt']; ?>" name="projekt[]">
                  <button type="button" class="btn btn-success" onclick="javascript: changeZwangszuteilungProjekt(this);">Ändern</button>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
            <?php
            }
            ?>

            </tbody>
          </table>
          <script>
            function zwangszuteilungAppend() {
              let inputs = $("#zwangszuteilungModal tbody")[1].querySelectorAll("input");
              for (var input of inputs) {
                if (!input.value.replace(/\s+/, '').length) {
                  console.log(input);
                  console.log("found empty");
                  return;
                }
              }
              addStudentsInZwangszuteilungInput();
            }

            function addStudentsInZwangszuteilungInput() {
              //var node = document.querySelector('#studentsInKlassen tbody');
              $($("#zwangszuteilungModal tbody")[1]).append($(`
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" name="uid[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" name="stufe[]" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" name="klasse[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" name="vorname[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" name="nachname[]" oninput="javascript: zwangszuteilungAppend();">
                </td>
                <td>
                  <input type="hidden" class="form-control" name="projekt[]">
                  <button type="button" class="btn btn-warning" onclick="javascript: changeZwangszuteilungProjekt(this);">Projekt wählen</button>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>`));
            }

            function setZwangszuteilungProjekt(projekt) {
              projekt = projekt.parentNode.parentNode.children[0].children[0].value;
              $("#zwangszuteilungProjektModal").modal("hide");
              var button = $("#zwangszuteilungModal .current-open").children()[5];
              button.children[0].value = projekt;
              button.children[1].innerHTML = "Ändern";
              button.children[1].classList.remove("btn-warning");
              button.children[1].classList.add("btn-success");
              $("#zwangszuteilungModal tr.current-open").removeClass("current-open");
              $("#zwangszuteilungProjektModal tbody").html("");
              zwangszuteilungAppend();
            }

            function changeZwangszuteilungProjekt(student, currentProjekt) {
              student.parentNode.parentNode.classList.add("current-open");
              for (var i = 0; i < projekte.length; i++) {
                $("#zwangszuteilungProjektModal tbody").append(`
                <tr` + (projekte[i]["id"] == student.parentNode.children[0].value ? " class='bg-success'" : "") + `>
                  <td>
                    <input type="hidden" value="` + projekte[i]["id"] + `">
                    <a href="javascript: showProjektInfoModal(projekte[` + i + `]);">` + projekte[i]["name"] + `</a>
                  </td>
                  <td>` + projekte[i]["betreuer"] + `</td>
                  <td>
                    <button type="button" onclick="javascript: setZwangszuteilungProjekt(this);" class="btn btn-`
                    + (projekte[i]["id"] == student.parentNode.children[0].value ? `primary">
                      OK` : `success">
                      Setzen`) + `
                    </button>
                  </td>
                </tr>`);
              }
              $("#zwangszuteilungProjektModal").modal("show");
            }

            addStudentsInZwangszuteilungInput();
          </script>
          <button onclick="javascript: addStudentsInZwangszuteilungInput();" type="button" class="btn btn-success">Schüler hinzufügen &#10010;</button>
        </div>

        <div class="modal-footer">
          <button type="submit" name="action" value="updateZwangszuteilung" class="btn btn-primary">Änderung speichern</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Projekt-Zwangszuteilungs-Modal -->
<div class="modal fade" id="zwangszuteilungProjektModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projekte der Zwangszuteilung</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        <table class="table table-striped table-hover table-dark">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Projektname</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Klassenauflistung-Modal -->
<div class="modal fade" id="studentsInKlassen" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüleranzahl in den Klassen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="studentsInKlassenForm" method="post">
        <div class="modal-body">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" name="action" value="updateStudentsInKlassen" class="btn btn-primary">Änderung speichern</button>
          <small>
            Hier können Sie die ganzen verschiedenen Klassen eintragen mit ihrer Schüleranzahl.
            Dadurch kann eine Überprüfung der Vollständigkeit durchgeführt werden.
            Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Klasse hinzufügen &#10010;".
            Um einen Eintrag zu entfernen betätigen sie das rote Kreuz rechts vom Eintrag.
            Bitte beachten Sie, dass unvolständige Einträge beim Speichern gelöscht werden.
            Im Nachfolgenden sehen Sie einen beispielhaften Eintrag einer 5. Klasse mit 28 Schülern.
          </small>

          <table class="table table-dark">
            <tbody>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="Stufe" value="5" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="5a" readonly>
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Schüleranzahl" value="28" readonly>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" disabled>
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <small class="text-muted">
            Tragen Sie die echten Werte bitte in der nachfolgenden Tabelle ein.
          </small>
          <table class="table table-striped">
            <thead class="thead-dark">
              <tr>
                <th class="sticky-top">Stufe</th>
                <th class="sticky-top">Klasse</th>
                <th class="sticky-top">Schüleranzahl</th>
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $studentsInKlassen = dbRead("../data/klassen.csv");
            	uasort($studentsInKlassen, function ($a, $b) {
            		if ($a["stufe"] == $b["stufe"]) {
            			return $a["klasse"] < $b["klasse"] ? -1 : 1;
            		}
            		return intval($a["stufe"]) < intval($b["stufe"]) ? -1 : 1;
            	});
              foreach ($studentsInKlassen as $klasse) {
                ?>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="Stufe" value="<?php echo $klasse['stufe']; ?>" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe[]" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="<?php echo $klasse['klasse']; ?>" name="klasse[]" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Schüleranzahl" value="<?php echo $klasse['anzahl']; ?>" name="anzahl[]" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
              <?php
              }
              ?>
            </tbody>
          </table>

          <script>
            function studentsInKlassenAppend() {
              let inputs = $("#studentsInKlassen tbody")[1].querySelectorAll("input");
              for (var input of inputs) {
                if (!input.value.replace(/\s+/, '').length) {
                  console.log(input);
                  console.log("found empty");
                  return;
                }
              }
              addStudentsInKlassenInput();
            }

            function addStudentsInKlassenInput() {
              //var node = document.querySelector('#studentsInKlassen tbody');
              $($("#studentsInKlassen tbody")[1]).append($(`
              <tr>
                <td>
                  <input type="text" class="form-control" placeholder="Bsp: 5" aria-label="Stufe" name="stufe[]" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <input type="text" class="form-control" placeholder="Bsp: 5a" aria-label="Klasse" name="klasse[]" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Schüleranzahl" name="anzahl[]" oninput="javascript: studentsInKlassenAppend();">
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>`));
            }

            function removeLine(element) {
              // button -> td -> tr -> tbody
              var row = element.parentNode.parentNode;
              row.parentNode.removeChild(row);
            }
            addStudentsInKlassenInput();
          </script>
          <button onclick="javascript: addStudentsInKlassenInput();" type="button" class="btn btn-success">Klasse hinzufügen &#10010;</button>
        </div>

        <div class="modal-footer">
          <button type="submit" name="action" value="updateStudentsInKlassen" class="btn btn-primary">Änderung speichern</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- eigentlicher Seiteninhalt -->
<div class="container-fluid">
  <div class="row">
    <!-- Spalte 1 -->
    <div class="col-12 col-lg-4">
      <div class="row flex">

        <div class="col-xs-12 col-sm-6 col-lg-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title">Dashboard Projektwahl</h5>
      				<p class="card-text">Übersicht über die Projektwahl-Datenbank</p>

          		<div class="btn-group btn-group-toggle" data-toggle="buttons">
                <button type="button" class="btn btn-danger" onclick="logout()">
                  Abmelden
                </button>
            		<!-- Button trigger modal -->
            		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#configModal">
            			Konfiguration
            		</button>
              </div>
      			</div>
      		</div>
        </div>

        <div class="col-xs-12 col-sm-6 col-lg-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo count($projekte); ?></h5>
      				<p class="card-text">Projekt<?php echo count($projekte) == 1 ? " wurde" : "e wurden"; ?> eingereicht</p>
              <!-- Button trigger modal -->
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Drucken</button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#projekteModal">
                  Auflisten
                </button>
              </div>
              <button type="button" class="btn btn-success" onclick="window.location.href = '?site=create';">
                Neues Projekt erstellen
              </button>
      			</div>
      		</div>
        </div>

      </div>
    </div>

    <!-- Spalte 2 -->
    <div class="col-12 col-lg-8">
      <div class="row flex">

        <div class="col-xl-4 col-sm-6 col-xs-12">
          <div class="card w-100 text-white bg-dark p-3<?php if ($pMax < $gesamtanzahl) {echo " border border-danger"; } elseif ($pMin > $gesamtanzahl) {echo " border border-warning"; } ?>">
            <div class="card-body">
              <h5 class="card-title">
                <span<?php if ($pMin > $gesamtanzahl) {echo " class='text-warning'"; } echo ">" . $pMin; ?></span> - <span<?php if ($pMax < $gesamtanzahl) {echo " class='text-danger'"; } echo ">" . $pMax; ?></span>
              </h5>
              <p class="card-text">Plätze sind laut Projektangaben insgesamt verfügbar</p></p>
            </div>
          </div>
        </div>

        <?php
          for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
          ?>
        <div class="col-xl-4 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3<?php if ($stufen[$i]["max"] < $stufen[$i]["students"]) {echo " border border-danger"; } elseif ($stufen[$i]["min"] > $stufen[$i]["students"]) {echo " border border-warning"; } ?>">
      			<div class="card-body">
              <h5 class="card-title">
                <span<?php if ($stufen[$i]["min"] > $stufen[$i]["students"]) {echo " class='text-warning'"; } echo ">" . $stufen[$i]["min"]; ?></span> - <span<?php if ($stufen[$i]["max"] < $stufen[$i]["students"]) {echo " class='text-danger'"; } echo ">" . $stufen[$i]["max"]; ?></span>
              </h5>
      				<p class="card-text">Plätze sind laut Projektangaben verfügbar für Klassenstufe <?php echo $i; ?></p>
      			</div>
      		</div>
        </div>
          <?php
          }
        ?>

    	</div>
    </div>
  </div>


  <!-- Klassenauflistung -->
  <div class="row flex">


    <div class="col-12 col-sm-6 col-md-4 offset-md-2 col-lg-3 offset-lg-3">
      <div class="card shadow bg-dark w-100 p-3 border <?php
      if (count($klassenliste) == 0) {
        echo " border-danger text-danger";
      }
      elseif ($klassenFertig == count($klassenliste)) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>" style="border: 3px solid !important;">
        <div class="card-body"><?php
        if (count($klassenliste) == 0) {
          ?>
          <h5 class="card-title">Keine</h5>
          <p class="card-text">Klasse wurde bisher im System eingetragen.</p>
          <?php
        }
        else {
          ?>
          <h5 class="card-title"><?php echo $klassenFertig; ?> von <?php echo count($klassenliste); ?></h5>
          <p class="card-text">Klassen haben bereits vollständig gewählt.</p>
          <?php
        }
        ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card shadow bg-dark p-3 w-100 border <?php
      if ($gesamtanzahl == 0) {
        echo "border-danger text-danger";
      }
      elseif ($gesamtanzahl == count($wahlen)) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>" style="border: 3px solid !important;">
        <div class="card-body">
          <h5 class="card-title"><?php echo count($wahlen); ?> von <?php echo $gesamtanzahl; ?>
          </h5>
          <p class="card-text">Schüler haben schon gewählt</p>
          <!-- Button trigger modal -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#schuelerModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row flex">

    <?php
    foreach ($klassen as $key => $klasse) {
      $anzahl = 0;
      $found = false;
      foreach ($klassenliste as $liste) {
        if (strtolower($liste["klasse"]) == strtolower($key)) {
          $anzahl = $liste["anzahl"];
          $found = true;
          break;
        }
      }
    ?>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
      <div class="card shadow bg-dark p-3 w-100 border <?php
      if (!$found || $anzahl < count($klasse) - 1) {
        echo "border-danger text-danger";
      }
      elseif ($anzahl == count($klasse) - 1) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>">
        <div class="card-body">
          <?php
          if (!$found) {
            echo " <span>Diese Klasse wurde nicht in den Datensätzen gefunden!!!</span>";
          }
          elseif ($anzahl < count($klasse) - 1) {
            echo " <span>Diese Klasse hat scheinbar mehr Schüler als eingetragen!!!</span>";
          }
          ?>
          <h5 class="card-title"><?php echo $key ?></h5>
          <p class="card-text"><?php echo count($klasse) - 1 > 0 ? count($klasse) - 1 . "/" . $anzahl . " Personen ha" . (count($klasse) - 1 > 1 ? "ben" : "t") . " bereits gewählt" : "Keine Person hat gewählt"; ?></p>
          <button onclick="javascript: window.open('printPDF.php?print=students&klasse=<?php echo $key; ?>');" type="button" class="btn btn-primary">Auflisten</button>
        </div>
      </div>
    </div><?php
    }
    ?>

  </div>

</div>
