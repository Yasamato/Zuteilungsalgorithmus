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
foreach ($projekte as $p) {
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

$buffer = 0.10; // +/- 10% Buffer bei den Projektplätzen
$showErrorModal = false;
$errorIncluded = false;
?>

<!-- Fehldermeldungs-Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Warnmeldungen und Hinweise</h4>
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
          Die <strong>Klasse <?php echo $klasse; ?></strong> konnte nicht gefunden werden. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript:;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a> oder bearbeiten sie den Schülereintrag <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier</a>.
        </div><?php
      }

      // Mehr Schüler als eingetragen
      foreach ($klassenliste as $klasse) {
        if (count($klassen[$klasse["klasse"]]) - 1 > $klasse["anzahl"]) {
          $showErrorModal = true;
          $errorIncluded = true;
          ?>
        <div class="alert alert-danger" role="alert">
          Die <strong>Klasse <?php echo $klasse["klasse"]; ?></strong> hat mehr Schüler als eingetragen. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript:;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a> oder bearbeiten sie den Schülereintrag <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier</a>.
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
          Es ha<?php echo $gesamtanzahl > 1 ? "ben" : "t" ?> nur <?php echo count($wahlen) . " von " . $gesamtanzahl; ?> Schülern gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">auflisten</a>.
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
          Es ha<?php echo ($klassenFertig > 1 ? "ben " : "t ") . $klassenFertig . " von " . count($klassenliste); ?> Klassen vollständig gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">auflisten</a>
          <?php if ($config["Stage"] < 4) { ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        <?php } ?>
        </div>
        <?php
      }

      if ($config["Stage"] < 5) {
        // Ausreichend Plätze für Schüler
        if ($pMin > $gesamtanzahl * (1 - $buffer)) {
          $showErrorModal = true;
          ?>
          <div class="alert alert-warning" role="alert">
            Die von allen Projekten summierte Mindestteilnehmeranzahl ist <?php echo ($pMin > $gesamtanzahl ? "größer als" : "zu groß für"); ?> die Gesamtschülerzahl. Falls nicht Projekte nicht stattfinden sollen, passen Sie bitte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> ggf. die Mindestteilnehmeranzahl an.
          </div><?php
        }
        if ($pMax < $gesamtanzahl * (1 + $buffer)) {
          $showErrorModal = true;
          if ($pMax < $gesamtanzahl) {
            $errorIncluded = true;
          }
          ?>
          <div class="alert alert-<?php echo ($pMax < $gesamtanzahl ? "danger" : "warning"); ?>" role="alert">
            Die von allen Projekten summierte Maximalteilnehmeranzahl <?php echo ($pMax < $gesamtanzahl ? "ist kleiner als die" : "liegt nur wenig über der"); ?> Gesamtschülerzahl und kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
          </div><?php
        }

        // Platz pro Stufe
        for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
          if ($stufen[$i]["min"] > $stufen[$i]["students"] * (1 - $buffer)) {
            $showErrorModal = true;
            ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              Die von allen Projekten summierte Mindestteilnehmeranzahl für die <strong>Klassenstufe <?php echo $i; ?></strong> ist <?php echo ($stufen[$i]["min"] > $stufen[$i]["students"] ? "größer als" : "zu groß für"); ?> die Schüleranzahl der Stufe. Dies kann zu Problemen führen und kann <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> bearbeitet werden.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div><?php
          }
          if ($stufen[$i]["max"] < $stufen[$i]["students"] * (1 + $buffer)) {
            $showErrorModal = true;
            if ($stufen[$i]["max"] < $stufen[$i]["students"]) {
              $errorIncluded = true;
            }
            ?>
            <div class="alert alert-<?php echo ($stufen[$i]["max"] < $stufen[$i]["students"] ? "danger" : "warning"); ?>" role="alert">
              Die von allen Projekten summierte Maximalteilnehmeranzahl für die <strong>Klassenstufe <?php echo $i; ?></strong> ist <?php echo ($stufen[$i]["max"] < $stufen[$i]["students"] ? "kleiner als die" : " liegt nur wenig über der"); ?> Schüleranzahl der Stufe. Dies kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
            </div><?php
          }
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
      Es sind Fehler aufgetreten. <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.
    </div>
    <?php
  }
  elseif ($showErrorModal) {
    ?>
    <div class="alert alert-warning" role="alert">
      Es sind Warnmeldungen und Hinweise aufgetreten. <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.
    </div>
    <?php
  }

  // Auswertung
  if ($config["Stage"] > 3) {
  ?>
  <div class="alert alert-<?php echo $errorIncluded ? "danger" : (file_exists("../data/algorithmus.pid") ? "warning" : "success"); ?>" role="alert">
    <?php
    if ($errorIncluded) {
      ?>
    Aufgrund der <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">obigen Fehler</a> kann momentan keine Auswertung durchgeführt werden. Bitte korrigieren sie evtl. fehlende oder inkorrekte Angaben.
      <?php
    }
    elseif ($config["Stage"] > 4) {
      if (!file_exists("../data/algorithmus.pid")) { ?>
    <h4 class="alert-heading">Zuteilung erfolgreich</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und die Auswertung durch den Zuteilungsalgorithmus wurde vom Admin bereits <?php echo file_exists("../data/algorithmus.pid") ? "gestartet" : "durchgeführt"; ?>.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="runZuteilungsalgorithmus">
      <div class="input-group">
        <select class="form-control custom-select" name="genauigkeit">
          <option value="1">Schnellste Laufzeit</option>
          <option value="2" selected>Normale Genauigkeit</option>
          <option value="3">Optimalere Verteilung</option>
        </select>
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">
            Erneut ausführen
          </button>
        </div>
      </div>
    </form>
    <?php
      }
      else { ?>
    <h4 class="alert-heading">Am erneuten Auswerten</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und der Zuteilungsalgorithmus wurde vom Admin erneut gestartet auf Basis der Wahlen und Zwangszuzuteilungen.
    </p>
    <?php
      }
    }
    else {
      if (!file_exists("../data/algorithmus.pid")) { ?>
    <h4 class="alert-heading">Bereit zur Auswertung</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und somit kann die Auswertung durch den Zuteilungsalgorithmus vom Admin gestartet werden.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="runZuteilungsalgorithmus">
      <div class="input-group">
        <select class="form-control custom-select" name="genauigkeit">
          <option value="1">Schnellste Laufzeit</option>
          <option value="2" selected>Normale Genauigkeit</option>
          <option value="3">Optimalere Verteilung</option>
        </select>
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">
            Starten
          </button>
        </div>
      </div>
    </form>
    <?php
      }
      else { ?>
    <h4 class="alert-heading">Am Auswerten</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und der Zuteilungsalgorithmus wurde vom Admin gestartet.
    </p>
    <?php
      }
    }
    if (file_exists("../data/algorithmus.pid")) {
      ?>
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"></div>
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%;">Loading...</div>
      </div>
      <script>
        var progressbarCheck = setInterval(function () {
          $.get("progress.php", function(data, status) {
            if (status = "success") {
              data = parseFloat(data) * 100;
              console.log("Fortschritt: " + data + "%");
              if (data != 100) {
                $($('.progress-bar')[0]).css('width', data + '%').html((data > 50 ? (Math.round(data * 100) / 100) + "%" : ""));
                $($('.progress-bar')[1]).css('width', (100 - data) + '%').html((data < 50 ? (Math.round(data * 100) / 100) + "%" : ""));
              } else {
                $($('.progress-bar')[0]).css('width', '100%').html("100%");
                $($('.progress-bar')[1]).css('width', '0%').html("");
                clearInterval(progressbarCheck);
                setTimeout(function () {
                  window.location.href = "?";
                }, 2000);
              }
            }
            else {
              console.log("Progress-fetch failed!!");
            }
          });
        }, 1000);
      </script>
      <?php
    }
    ?>
  </div>
  <?php
  }
  if ($config["Stage"] > 4) {
    // Schüler die gewählt haben, aber nicht zugeteilt werden konnten
    $studentOhneZuteilung = [];
    foreach ($wahlen as $wahl) {
      if (empty($wahl["projekt"])) {
        array_push($studentOhneZuteilung, $wahl);
      }
    }
    if (!empty($studentOhneZuteilung)) {
      ?>
      <div class="alert alert-danger" role="alert">
        Es konnten <strong><?php echo count($studentOhneZuteilung); ?> Schüler</strong> keinem Projekt zugeteilt werden. Diese müssen <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier manuell</a> zugeteilt werden.
      </div><?php
    }
    else {
      ?>
      <div class="alert alert-success" role="alert">
        Es konnten <strong>alle <?php echo count($wahlen); ?> Schüler</strong> einem ihrer Wunsch-Projekte zugeteilt werden.
      </div><?php
    }

    // Projekte die Stattfinden oder nicht
    $projekteNichtStattfinden = [];
    $projekteZuViel = [];
    foreach ($projekte as $key => $projekt) {
      if (count($projekt["teilnehmer"]) < $projekt["minPlatz"]) {
        array_push($projekteNichtStattfinden, $projekt);
      }
      elseif (count($projekt["teilnehmer"]) > $projekt["maxPlatz"]) {
        array_push($projekteZuViel, $projekt);
      }
    }
    if (!empty($projekteNichtStattfinden)) {
      ?>
      <div class="alert alert-danger" role="alert">
        Es können <strong><?php echo count($projekteNichtStattfinden); ?> Projekte</strong> aufgrund mangelnder Teilnehmerzahl nicht stattfinden. <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div><?php
    }
    if (!empty($projekteZuViel)) {
      ?>
      <div class="alert alert-warning" role="alert">
        Die Teilnehmerzahl von <strong><?php echo count($projekteZuViel); ?> Projekten</strong> übersteigt die Maximalteilnehmeranzahl. <a href="javasript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div><?php
    }
    elseif (empty($projekteNichtStattfinden)) {
      ?>
      <div class="alert alert-success" role="alert">
        Es können <strong>alle <?php echo count($projekte); ?> Projekte</strong> statt finden.
      </div><?php
    }
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
              		'<option value="4">#5 Wahlen abgeschlossen</option>',
              		'<option value="5">#6 Auswertung abgeschlossen</option>'
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
              <li>
                "Nicht veröffentlicht": Keiner hat Zugriff außer der Admin
              </li>
              <li>
                "Projekte können eingereicht werden": Änderungen an den Einstellungen zu den Projekten können nicht mehr getätigt werden, jedoch die Hinweise vom Admin verändert werden sowie über das Lehrer-Interface Projekte eingesehen, bearbeitet und eingereicht werden
              </li>
              <li>
                "Projekt-Einreichung geschlossen": Es können keine weiteren Projekte mehr von den Lehrern eingereicht werden. Editierungen bereits bestehender Projekte ist weiterhin möglich durch den Admin sowie die Lehrer
              </li>
              <li>
                "Wahl-Interface geöffnet": Die Schüler können sich nun mit ihren Login-Daten anmelden und aus ihrem Projekt-Pool ihre Wahl wählen. Die Lehrerschaft hat nun nur noch Zugriff auf die Liste mit den Projekten, kann diese noch bearbeiten, jedoch nicht mehr einreichen (nur der Admin)
              </li>
              <li>
                "Wahl abgeschlossen": Der Schüler-Zugriff wird geschlossen, Lehrer können die Liste ansehen. Änderungen können nur noch händisch von einem Admin getätigt werden. Die Auswertung wird durch einen Admin durchgeführt
              </li>
              <li>
                "Auswertung abgeschlossen": Diese Phase kann nicht ausgewählt werden, sondern wird automatisch erreicht mit der fertigen Zuteilung durch den Zuteilungsalgorithmus, welcher durch den Admin ausgelöst werden muss
              </li>
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
          <hr class="my-4">
          <h5>Informationen</h5>
          <small class="form-text text-muted">
            Version: <?php
            echo $version . "<br>";
            if ($newest != $version) {
              ?>
              <span class="text-warning">
                Es ist eine neuere Version verfügbar: <?php echo $newest; ?>
              <span>
              <form method="post">
                <button type="submit" class="btn btn-success" name="action" value="update">
                  Update
                </button>
              </form>
              <?php
            }
            else {
              echo "<span class='text-success'>Die neueste Version ist installiert</span>";
            }
            ?>
          </small>
          <br>
          <h5>Lizenz</h5>
          <small class="form-text text-muted">
            <?php echo file_get_contents("../LICENSE"); ?>
          </small>
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
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
        <?php
        if ($config["Stage"] > 4) {
          if (!empty($projekteNichtStattfinden)) {
            ?>
        <h4 class="text-danger">Folgende Projekte können aufgrund mangelnder Teilnehmerzahl nicht stattfinden.</h4>
        <table class="table table-dark table-striped table-hover border border-danger">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Name</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">(Zugeteilt) Platz</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($projekteNichtStattfinden as $projekt) {
              foreach ($projekte as $k => $p) {
                if ($p["id"] == $projekt["id"]) {
                  $key = $k;
                  break;
                }
              }
              echo '
              <tr class="border-left border-danger">
                <td><a href="javascript:;" onclick="javasript: showProjektInfoModal(projekte[' . $key . ']);">' . $projekt["name"] . '</a></td>
                <td>' . $projekt["betreuer"] . '</td>
                <td>' . $projekt["minKlasse"] . '-' . $projekt["maxKlasse"] . '</td>
                <td class="bg-danger">(' . count($projekt["teilnehmer"]) . ') ' . $projekt["minPlatz"] . '-' . $projekt["maxPlatz"] . '</td>
              </tr>';
            }
            ?>
          </tbody>
        </table>
            <?php
          }
          if (!empty($projekteZuViel)) {
            ?>
        <h4 class="text-warning">Die Teilnehmerzahl der folgenden Projekte überschreitet deren Maximalteilnehmeranzahl.</h4>
        <table class="table table-dark table-striped table-hover border border-warning">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Name</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">(Zugeteilt) Platz</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($projekteNichtStattfinden as $key => $projekt) {
              foreach ($projekte as $k => $p) {
                if ($p["id"] == $projekt["id"]) {
                  $key = $k;
                  break;
                }
              }
              echo '
              <tr class="border-left border-warning">
                <td><a href="javascript:;" onclick="javasript: showProjektInfoModal(projekte[' . $key . ']);">' . $projekt["name"] . '</a></td>
                <td>' . $projekt["betreuer"] . '</td>
                <td>' . $projekt["minKlasse"] . '-' . $projekt["maxKlasse"] . '</td>
                <td class="bg-warning">(' . count($projekt["teilnehmer"]) . ') ' . $projekt["minPlatz"] . '-' . $projekt["maxPlatz"] . '</td>
              </tr>';
            }
            ?>
          </tbody>
        </table>
            <?php
          }
        }
        ?>
        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Name</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top"><?php echo $config["Stage"] > 4 ? "(Zugeteilt) " : ""; ?>Platz</th>
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
            $color = (count($projekt["teilnehmer"]) >= $projekt["minPlatz"] ? (count($projekt["teilnehmer"]) <= $projekt["maxPlatz"] ? 'success' : 'warning') : 'danger');
            echo '
            <tr' . ($config["Stage"] > 4 ? ' class="border-left border-' . $color . '"' : '') . '>
              <td><a href="javascript:;" onclick="javasript: showProjektInfoModal(projekte[' . $key . ']);">' . $projekt["name"] . '</a></td>
              <td>' . $projekt["betreuer"] . '</td>
              <td>' . $projekt["minKlasse"] . '-' . $projekt["maxKlasse"] . '</td>
              <td' . ($config["Stage"] > 4 ? ' class="bg-' . $color . '">' . '(' . count($projekt["teilnehmer"]) . ') ' : '>') . $projekt["minPlatz"] . '-' . $projekt["maxPlatz"] . '</td>
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
<div class="modal fade" id="schuelerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
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
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#zwangszuteilungModal">
          Zwangszuteilungen
        </button>
        <br>
        <small class="text-muted">Das Ergebnis der Auswertung ist erst verfügbar sobald die Auswertung durch den Admin durchgeführt wurde. Die Auswertung kann erst im Admin-Panel durchgeführt werden, sobald die Wahlen geschlossen sind.</small>
        <?php
          if (empty($wahlen)) {
            echo '
        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Klasse</th>
              <th class="sticky-top">Vorname</th>
              <th class="sticky-top">Nachname</th>
              <th class="sticky-top">Wahl</th>
              <th class="sticky-top">Ergebnis</th>
              <th class="sticky-top">Bearbeiten</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                Bisher wurde keine Wahl getätigt
              </td>
            </tr>
          </tbody>
        </table>';
          }
          elseif ($config["Stage"] > 4 && !empty($studentOhneZuteilung)) {
            echo '
        <h4 class="text-danger">Folgende Schüler konnten nicht zugeteilt werden und benötigen eine manuelle Zuteilung!</h4>
        <table class="table table-dark table-striped table-hover border border-danger">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Klasse</th>
              <th class="sticky-top">Vorname</th>
              <th class="sticky-top">Nachname</th>
              <th class="sticky-top">Wahl</th>
              <th class="sticky-top">Ergebnis</th>
              <th class="sticky-top">Bearbeiten</th>
            </tr>
          </thead>
          <tbody>';
            foreach ($studentOhneZuteilung as $student) {
            echo '
          <tr uid="' . $student["uid"] . '">
            <td>' . $student["stufe"] . '</td>
            <td>' . $student["klasse"] . '</td>
            <td>' . $student["vorname"] . '</td>
            <td>' . $student["nachname"] . '</td>
            <td>
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
                    ' . getProjektInfo($projekte, $wahl)["name"] . '
                  </a>
                </li>';
              }
              echo '
              </ol>
            </td>
            <td class="bg-danger">Konnte nicht zugeteilt werden</td>
            <td class="navbar-dark">
              <button class="navbar-toggler" type="button" onclick="javascript: editStudentModal(this);">
                <span class="navbar-toggler-icon"></span>
              </button>
            </td>
          </tr>';
            }
            echo '
          </tbody>
        </table>';
          }
          foreach ($klassen as $klasse) {
            echo '
        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Klasse <a href="javascript: ;" onclick="javascript: $(`#class-' . $klasse[0]["klasse"] . '`).collapse(`toggle`);">' . $klasse[0]["klasse"] . '</a></th>
              <th class="sticky-top">Vorname</th>
              <th class="sticky-top">Nachname</th>
              <th class="sticky-top">Wahl</th>
              <th class="sticky-top">Ergebnis</th>
              <th class="sticky-top">Bearbeiten</th>
            </tr>
          </thead>
          <tbody id="class-' . $klasse[0]["klasse"] . '" class="collapse show">';
            foreach ($klasse as $key => $student) {
              if ($key == 0) {
                continue;
              }
              echo '
            <tr uid="' . $student["uid"] . '"' . ($config["Stage"] > 4 ? " class='border-left border-" . (empty($student["projekt"]) ? "danger" : "success") . "'" : "") . '>
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
                      ' . getProjektInfo($projekte, $wahl)["name"] . '
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
              <td' . ($config["Stage"] > 4 ? " class='bg-" . (empty($student["projekt"]) ? "danger" : "success") . "'" : "") . '>';

              if (empty($student["projekt"])) {
                echo ($config["Stage"] > 4 ? "Konnte nicht zugeteilt werden" : "N/A");
              }
              else {
                $p = null;
                foreach ($projekte as $key => $projekt) {
                  if ($projekt["id"] == $student["projekt"]) {
                    $p = $key;
                    break;
                  }
                }
                echo '
                <input type="hidden" value="' . $student["projekt"] . '">
                <a href="javascript: ;" onclick="javascript: showProjektInfoModal(projekte[' . $p . ']);"' . ($config["Stage"] > 4 ? " class='text-light'" : "") . '>
                  ' . getProjektInfo($projekte, $student["projekt"])["name"] . '
                </a>';
              }
              echo '</td>
              <td class="navbar-dark">
                <button class="navbar-toggler" type="button" onclick="javascript: editStudentModal(this);">
                  <span class="navbar-toggler-icon"></span>
                </button>
              </td>
            </tr>';
            }
            echo "

            </tbody>
          </table>
          ";
          }
          ?>
        <script>
          function editStudentModal(student) {
            // button -> td -> tr
            var student = student.parentNode.parentNode;
            $("#schuelerEditForm").children("div.form-group")[0].children[1].value = $(student).attr("uid"); //uid
            $("#schuelerEditForm").children("div.form-group")[1].children[1].value = $(student).children()[0].innerHTML; //stufe
            $("#schuelerEditForm").children("div.form-group")[2].children[1].value = $(student).children()[1].innerHTML; //klasse
            $("#schuelerEditForm").children("div.form-group")[3].children[1].value = $(student).children()[2].innerHTML; //vorname
            $("#schuelerEditForm").children("div.form-group")[4].children[1].value = $(student).children()[3].innerHTML; //nachname
            $("#schuelerDeleteForm").children()[2].value = $(student).children()[2].innerHTML + " " + $(student).children()[3].innerHTML; //name
            if (window.config.Stage > 4) {
              if ($(student).children()[5].innerHTML == "Konnte nicht zugeteilt werden") {
                $("#schuelerEditForm .projekt-input").html(`
                  <input type="hidden" name="ergebnis">
                  <button type="button" onclick="javascript: changeProjektzuteilung(this);" class="btn btn-warning">
                    Projekt zuteilen
                  </button>`);
              }
              else {
                console.log($(student).children()[5]);
                $("#schuelerEditForm .projekt-input").html(`
                  <input type="hidden" name="ergebnis" required value="` + $(student).children()[5].children[0].value + `" />
                  Zuteilung
                  <button type="button" onclick="javascript: changeProjektzuteilung(this);" class="btn btn-success">
                    Ändern
                  </button>
                  <button type="button" onclick="javascript: confirmDeleteProjektzuteilung();" class="btn btn-danger">
                    Löschen
                  </button>`);
              }
            }
            else {
              $("#schuelerEditForm .projekt-input").parent().addClass("d-none");
            }
            $("#schuelerDeleteForm").children()[1].value = $(student).attr("uid");
            $("#schuelerProjektzuteilungDeleteForm").children()[1].value = $(student).attr("uid");
            $("#schuelerEditModal").modal("show");
          }
        </script>
      </div>

      <div class="modal-footer">
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Schüler-Edit-Modal -->
<div class="modal fade" id="schuelerEditModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schülereintrag bearbeiten</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <br>
        <small class="text-muted">
          Schülereinträge können hier editiert werden, wobei die Wahl selbst nicht beeinflusst werden kann.
          Um die Wahl zu ändern, lassen sie den Schüler bitte erneut wählen oder tragen diesen direkt in der Zwangszuteilungs-Tabelle ein.
        </small>
        <form method="post" id="schuelerEditForm">
          <div class="form-group">
            <label>U-ID</label>
            <input type="text" class="form-control" name="uid" placeholder="U-ID" required>
          </div>
          <div class="form-group">
            <label>Stufe</label>
            <input type="number" class="form-control" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe" placeholder="Stufe" required>
          </div>
          <div class="form-group">
            <label>Klasse</label>
            <input type="text" class="form-control" name="klasse" placeholder="Klasse" required>
          </div>
          <div class="form-group">
            <label>Vorname</label>
            <input type="text" class="form-control" name="vorname" placeholder="Vorname" required>
          </div>
          <div class="form-group">
            <label>Nachname</label>
            <input type="text" class="form-control" name="nachname" placeholder="Nachname" required>
          </div>
          <div class="form-group">
            <label>Ergebnis</label>
            <div class="projekt-input flex"></div>
          </div>
          <input type="hidden" name="action" value="editWahleintrag">
        </form>
      </div>

      <form method="post" id="schuelerDeleteForm">
        <input type="hidden" name="action" value="deleteWahleintrag">
        <input type="hidden" name="uid">
        <input type="hidden" name="name">
      </form>
      <form method="post" id="schuelerProjektzuteilungDeleteForm">
        <input type="hidden" name="action" value="deleteProjektzuteilung">
        <input type="hidden" name="uid">
      </form>
      <script>
        function confirmDeleteWahl() {
        	if (confirm("Wollen sie wirklich den Wahleintrag des Schülers '" + $("#schuelerDeleteForm").children()[2].value + "' löschen?")) {
        		$('#schuelerDeleteForm').submit();
        	}
        	else {
        		alert("Löschvorgang abgebrochen");
        	}
        }
        function confirmDeleteProjektzuteilung() {
        	if (confirm("Wollen sie wirklich die Projektzuteilung des Schülers '" + $("#schuelerDeleteForm").children()[2].value + "' löschen?")) {
        		$('#schuelerProjektzuteilungDeleteForm').submit();
        	}
        	else {
        		alert("Löschvorgang abgebrochen");
        	}
        }
      </script>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="javascript: confirmDeleteWahl();">Löschen</button>
        <button type="button" class="btn btn-success" onclick="javascript: $('#schuelerEditForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Zwangszuteilungs-Modal -->
<div class="modal fade" id="zwangszuteilungModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Zwangszuteilungen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form method="post" id="zwangszuteilungForm">
          <input type="hidden" name="action" value="updateZwangszuteilung">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" class="btn btn-primary">Änderung speichern</button>
          <br>
          <small class="text-muted">
            Um Schüler vorab fest einem Projekt zuzuteilen, tragen Sie bitte die U-ID (Login-Name des Schülers) korrekt ein und wählen sie das entsprechende Projekt aus.
            Falls sie bereits ein Projekt ausgewählt haben, färbt sich der Knopf zur Projektauswahl grün und die Beschriftung ändert sich zu "Ändern".
            Es kann jedoch weiterhin jederzeit das ausgewählte Projekt geändert werden.
            Unten sehen sie einen beispielhaften Eintrag.
            Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Schüler hinzufügen &#10010;".
            Um einen Eintrag zu entfernen, betätigen sie das rote Kreuz rechts vom Eintrag.
            Bitte beachten Sie, dass unvollständige Einträge beim Speichern gelöscht werden und Änderungen nur übernommen werden bei Betätigung des "Änderungen speichern"-Knopfes.
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
                  <input type="text" class="form-control" aria-label="U-ID" value="<?php echo $student['uid']; ?>" name="uid[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="<?php echo $student['stufe']; ?>" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="<?php echo $student['klasse']; ?>" name="klasse[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="<?php echo $student['vorname']; ?>" name="vorname[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="<?php echo $student['nachname']; ?>" name="nachname[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="hidden" class="form-control" value="<?php echo $student['projekt']; ?>" name="projekt[]">
                  <button type="button" class="btn btn-success" onclick="javascript: changeProjektzuteilung(this);">Ändern</button>
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
        </form>
        <script>
          function autoAppendTable(selector, appendFunction) {
            let inputs = $(selector)[1].querySelectorAll("input");
            for (var input of inputs) {
              if (!input.value.replace(/\s+/, '').length) {
                console.log(input);
                console.log("found empty");
                return;
              }
            }
            appendFunction();
          }

          function addStudentsInZwangszuteilungInput() {
            //var node = document.querySelector('#studentsInKlassen tbody');
            $($("#zwangszuteilungModal tbody")[1]).append($(`
            <tr>
              <td>
                <input type="text" class="form-control" aria-label="U-ID" name="uid[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Stufe" name="stufe[]" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Klasse" name="klasse[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Vorname" name="vorname[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Nachname" name="nachname[]" oninput="javascript: autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="hidden" class="form-control" name="projekt[]">
                <button type="button" class="btn btn-warning" onclick="javascript: changeProjektzuteilung(this);">Projekt festlegen</button>
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>`));
          }

          function setProjektzuteilung(projekt) {
            console.log("Changing Projekt");
            var button = $(".current-open");
            // btn -> td -> tr -> td -> input
            projekt = projekt.parentNode.parentNode.children[0].children[0].value;
            $("#editStudentProjektzuteilungModal").modal("hide");
            button.children("input").val(projekt);
            button.children("button").first().html("Ändern");
            button.children("button").first().removeClass("btn-warning");
            button.children("button").first().addClass("btn-success");
            $(".current-open").removeClass("current-open");
            $("#editStudentProjektzuteilungModal tbody").html("");
            autoAppendTable('#zwangszuteilungModal tbody', addStudentsInZwangszuteilungInput);
          }

          function changeProjektzuteilung(student) {
            student.parentNode.classList.add("current-open");
            var input = student.parentNode.children[0];
            $("#editStudentProjektzuteilungModal tbody").html("");
            for (var i = 0; i < projekte.length; i++) {
              $("#editStudentProjektzuteilungModal tbody").append(`
              <tr` + (projekte[i]["id"] == input.value ? " class='bg-success'" : "") + `>
                <td>
                  <input type="hidden" value="` + projekte[i]["id"] + `">
                  <a href="javascript: ;" onclick="javascript: showProjektInfoModal(projekte[` + i + `]);">` + projekte[i]["name"] + `</a>
                </td>
                <td>` + projekte[i]["betreuer"] + `</td>
                <td>
                  <button type="button" onclick="javascript: setProjektzuteilung(this);" class="btn btn-`
                  + (projekte[i]["id"] == input.value ? `primary">
                    OK` : `success">
                    Setzen`) + `
                  </button>
                </td>
              </tr>`);
            }
            $("#editStudentProjektzuteilungModal").modal("show");
          }

          addStudentsInZwangszuteilungInput();
        </script>
        <button onclick="javascript: addStudentsInZwangszuteilungInput();" type="button" class="btn btn-success">Schüler hinzufügen &#10010;</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="javascript: $('#zwangszuteilungForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Projektzuteilungs-Modal -->
<div class="modal fade" id="editStudentProjektzuteilungModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projektwahl für eine Zwangszuteilung</h4>
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
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Klassenauflistung-Modal -->
<div class="modal fade" id="studentsInKlassen" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüleranzahl in den Klassen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        <button type="button" class="btn btn-primary" onclick="javascript: $('#studentsInKlassenForm').submit();">Änderung speichern</button>
        <br>
        <small>
          Hier können Sie die ganzen verschiedenen Klassen eintragen mit ihrer Schüleranzahl.
          Dadurch kann eine Überprüfung der Vollständigkeit durchgeführt werden.
          Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Klasse hinzufügen &#10010;".
          Um einen Eintrag zu entfernen betätigen sie das rote Kreuz rechts vom Eintrag.
          Bitte beachten Sie, dass unvollständige Einträge beim Speichern gelöscht werden.
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
        <form id="studentsInKlassenForm" method="post">
          <input type="hidden" name="action" value="updateStudentsInKlassen">
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
        </form>

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
        <button type="button" class="btn btn-primary" onclick="javascript: $('#studentsInKlassenForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>

    </div>
  </div>
</div>

<!-- Keine Wahl-Modal -->
<div class="modal fade" id="keineWahlModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüler ohne Wahlen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form method="post" id="keineWahlForm">
          <input type="hidden" name="action" value="updateKeineWahl">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" class="btn btn-primary">Änderung speichern</button>
          <br>
          <small class="text-muted">
            Bitte tragen Sie hier alle Schüler ein, welche es versäumt haben eine Wahl zu tätigen und dementsprechend irgendein Projekt Ihrer Jahrgangsstufe zugewiesen bekommen.
            Durch den Einsatz der Schüler als "Wildcards" kann es möglich werden Projekte stattfinden zu lassen, die vorher nicht zustande kamen.
            Im Folgenden ist ein Beispieldatensatz aufgeführt.
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
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody><?php
            foreach ($keineWahl as $student) {
              ?>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" value="<?php echo $student['uid']; ?>" name="uid[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="<?php echo $student['stufe']; ?>" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="<?php echo $student['klasse']; ?>" name="klasse[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="<?php echo $student['vorname']; ?>" name="vorname[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="<?php echo $student['nachname']; ?>" name="nachname[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
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
        </form>
        <script>
          function addStudentsInKeineWahlInput() {
            //var node = document.querySelector('#studentsInKlassen tbody');
            $($("#keineWahlModal tbody")[1]).append($(`
            <tr>
              <td>
                <input type="text" class="form-control" aria-label="U-ID" name="uid[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Stufe" name="stufe[]" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Klasse" name="klasse[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Vorname" name="vorname[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Nachname" name="nachname[]" oninput="javascript: autoAppendTable('#keineWahlModal tbody', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="hidden" class="form-control" name="projekt[]">
                <button type="button" class="btn btn-warning" onclick="javascript: changeProjektzuteilung(this);">Projekt festlegen</button>
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>`));
          }

          addStudentsInKeineWahlInput();
        </script>
        <button onclick="javascript: addStudentsInKeineWahlInput();" type="button" class="btn btn-success">Schüler hinzufügen &#10010;</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="javascript: $('#keineWahlForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>



<!-- eigentlicher Seiteninhalt -->
<div class="container-fluid">
  <div class="row">
    <!-- Spalte 1 -->
    <div class="col-12 col-lg-4">
      <div class="row flex d-flex justify-content-center">

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
      <div class="row flex d-flex justify-content-center">

        <div class="col-xl-4 col-sm-6 col-xs-12">
          <div class="card w-100 text-white bg-dark p-3<?php if ($pMax < $gesamtanzahl) {echo " class='text-danger'"; } elseif ($pMax < $gesamtanzahl * (1 + $buffer) || $pMin > $gesamtanzahl * (1 - $buffer)) {echo " border border-warning"; } ?>">
            <div class="card-body">
              <h5 class="card-title">
                <span<?php if ($pMin > $gesamtanzahl * (1 - $buffer)) {echo " class='text-warning'"; } echo ">" . $pMin; ?></span> - <span<?php if ($pMax < $gesamtanzahl) {echo " class='text-danger'"; } elseif ($pMax < $gesamtanzahl * (1 + $buffer)) {echo " class='text-warning'"; } echo ">" . $pMax; ?></span>
              </h5>
              <p class="card-text">Plätze sind laut Projektangaben insgesamt verfügbar</p></p>
            </div>
          </div>
        </div>

        <?php
          for ($i = CONFIG["minStufe"]; $i <= CONFIG["maxStufe"]; $i++) {
          ?>
        <div class="col-xl-4 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3<?php if ($stufen[$i]["max"] < $stufen[$i]["students"]) {echo " class='text-danger'"; } elseif ($stufen[$i]["max"] < $stufen[$i]["students"] * (1 + $buffer) || $stufen[$i]["min"] > $stufen[$i]["students"] * (1 - $buffer)) {echo " border border-warning"; } ?>">
      			<div class="card-body">
              <h5 class="card-title">
                <span<?php if ($stufen[$i]["min"] > $stufen[$i]["students"] * (1 - $buffer)) {echo " class='text-warning'"; } echo ">" . $stufen[$i]["min"]; ?></span> - <span<?php if ($stufen[$i]["max"] < $stufen[$i]["students"]) {echo " class='text-danger'"; } elseif ($stufen[$i]["max"] < $stufen[$i]["students"] * (1 + $buffer)) {echo " class='text-warning'"; } echo ">" . $stufen[$i]["max"]; ?></span>
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
  <div class="row flex d-flex justify-content-center">

    <?php
    if ($config["Stage"] > 4) {
    ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card shadow bg-dark w-100 p-3 border <?php
      if (count($wahlen) + count($keineWahl) < $gesamtanzahl) {
        echo " border-danger text-danger";
      }
      elseif (count($wahlen) == $gesamtanzahl && count($keineWahl) == 0) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"><?php echo $gesamtanzahl - count($wahlen); ?></h5>
          <p class="card-text">Schüler ha<?php echo $gesamtanzahl - count($wahlen) != 1 ? "ben" : "t"; ?> keine Wahl getätigt</p>
          <?php if ($gesamtanzahl - count($wahlen) > 0) { ?>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#">
              Einträge bearbeiten
            </button>
          </div>
        <?php } ?>
        </div>
      </div>
    </div>
    <?php
    }
    ?>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card shadow bg-dark w-100 p-3 border <?php
      if (count($klassenliste) == 0) {
        echo " border-danger text-danger";
      }
      elseif ($klassenFertig == count($klassenliste)) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>" style="border: 4px solid !important;">
        <div class="card-body"><?php
        if (count($klassenliste) == 0) {
          ?>
          <h5 class="card-title">Keine</h5>
          <p class="card-text">Klasse wurde bisher im System eingetragen</p>
          <?php
        }
        else {
          ?>
          <h5 class="card-title"><?php echo $klassenFertig; ?> von <?php echo count($klassenliste); ?></h5>
          <p class="card-text">Klassen haben<?php echo $config["Stage"] > 3 ? "" : " bereits"; ?> vollständig gewählt</p>
          <?php
        }
        ?>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#studentsInKlassen">
              Einträge bearbeiten
            </button>
          </div>
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
      } ?>" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"><?php echo count($wahlen); ?> von <?php echo $gesamtanzahl; ?>
          </h5>
          <p class="card-text">Schüler haben<?php echo $config["Stage"] > 3 ? "" : " schon"; ?> gewählt</p>
          <!-- Button trigger modal -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#schuelerModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card shadow bg-dark w-100 p-3 border border-warning text-warning" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"><?php echo count($zwangszuteilung); ?></h5>
          <p class="card-text">Schüler wurde<?php echo count($zwangszuteilung) != 1 ? "n" : ""; ?> fest einem Projekt zugeteilt</p>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#zwangszuteilungModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php if ($config["Stage"] > 2) { ?>
  <hr class="my-4">
  <div class="row flex d-flex justify-content-center">

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
  <?php } ?>

</div>

<script src="js/dashboard.js"></script>
