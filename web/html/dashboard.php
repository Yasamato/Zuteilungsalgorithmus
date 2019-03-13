<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Zugriff verweigert");
}

// generate the statistics how many places are available in each class
// initialize the data array
$stufen = [
  5 => [
    "min" => 0,
    "max" => 0
  ],
  6 => [
    "min" => 0,
    "max" => 0
  ],
  7 => [
    "min" => 0,
    "max" => 0
  ],
  8 => [
    "min" => 0,
    "max" => 0
  ],
  9 => [
    "min" => 0,
    "max" => 0
  ],
  10 => [
    "min" => 0,
    "max" => 0
  ],
  11 => [
    "min" => 0,
    "max" => 0
  ],
  12 => [
    "min" => 0,
    "max" => 0
  ]
];

// read each project and add the max members to the affected classes
foreach (dbRead("../data/projekte.csv") as $p) {
  for ($i = 5; $i <= 12; $i++) {
		if ($p["minKlasse"] <= $i && $p["maxKlasse"] >= $i) {
			$stufen[$i]["min"] += $p["minPlatz"];
			$stufen[$i]["max"] += $p["maxPlatz"];
		}
	}
}
?>
<!-- Einstellungs-Modal -->
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-labelledby="configModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title" id="configModalLabel">Projektwahlkonfiguration</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="configForm" method="post">

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

          <hr class="my-4">
          <h5>Projekt-Einstellungen</h5>
          <small class="form-text text-muted">
            Stellen sie die Dauer der Projektwoche ein sowie ggf. Hinweise für die Lehrer zur Projekterstellung.
            Diese Anmerkungen werden als zusätzliche Information beim Einreichen von Projekten beim entsprechenden Feld angezeigt.
            Durch das Auswählen der Checkboxen wird festgelegt, ob Projekte an dem jeweiligem Vor-/Nachmittag statt finden.
          </small>
          <br>
          <table class="table table-striped">
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

          <div id="configurebuttons">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button onclick="javascript: $('form#configForm').submit();" type="submit" name="action" value="updateConfiguration" class="btn btn-primary">Speichere Änderungen</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Projekte-Modal -->
<div class="modal fade" id="projekteModal" tabindex="-1" role="dialog" aria-labelledby="projekteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projekte</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <table class="table table-dark table-striped table-hover">
          <thead>
            <tr>
              <th>Name</th>
              <th>Betreuer</th>
              <th>Stufe</th>
              <th>Platz</th>
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
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <br>
        <small class="text-muted">Das Ergebnis der Auswertung ist erst verfügbar sobald die Auswertung durch den Admin durchgeführt wurde. Die Auswertung kann erst im Admin-Panel durchgeführt werden, sobald die Wahlen geschlossen sind.</small>

        <table class="table table-dark table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th>Stufe</th>
              <th>Klasse</th>
              <th>Vorname</th>
              <th>Nachname</th>
              <th>Wahl</th>
              <th>Ergebnis</th>
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
          foreach ($wahlen as $key => $student) {
            echo '
            <tr>
              <td>' . $student["stufe"] . '</td>
              <td>' . $student["klasse"] . '</td>
              <td>' . $student["vorname"] . '</td>
              <td>' . $student["nachname"] . '</td>
              <td>
                <ol>';
              foreach ($student["wahl"] as $key => $wahl) {
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
                </ol>
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

<!-- Klassenauflistung-Modal -->
<div class="modal fade" id="studentsInKlassen" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark">
      <form id="studentsInKlassenForm" method="post">

      <div class="modal-header">
        <h4 class="modal-title">Schüleranzahl in den Klassen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <table class="table table-striped">
          <thead class="thead-dark">
            <tr>
              <th>Stufe</th>
              <th>Klasse</th>
              <th>Schüleranzahl</th>
              <th></th>
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
                <input type="text" class="form-control" placeholder="Bsp: 5" aria-label="Stufe" value="<?php echo $klasse['stufe']; ?>" name="stufe[]">
              </td>
              <td>
                <input type="text" class="form-control" placeholder="Bsp: 5a" aria-label="Klasse" value="<?php echo $klasse['klasse']; ?>" name="klasse[]">
              </td>
              <td>
                <input type="number" class="form-control" placeholder="0" aria-label="Schüleranzahl" value="<?php echo $klasse['anzahl']; ?>" name="anzahl[]">
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
          function addStudentsInKlassenInput() {
            //var node = document.querySelector('#studentsInKlassen tbody');
            $("#studentsInKlassen tbody").append($(`
            <tr>
              <td>
                <input type="text" class="form-control" placeholder="Bsp: 5" aria-label="Stufe" name="stufe[]">
              </td>
              <td>
                <input type="text" class="form-control" placeholder="Bsp: 5a" aria-label="Klasse" name="klasse[]">
              </td>
              <td>
                <input type="number" class="form-control" placeholder="0" aria-label="Schüleranzahl" name="anzahl[]">
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button onclick="javascript: $('form#studentsInKlassenForm').submit();" type="submit" name="action" value="updateStudentsInKlassen" class="btn btn-primary">Speichere Änderungen</button>
      </div>

      </form>
    </div>
  </div>
</div>




<div class="container-fluid">
  <div class="row">
    <!-- Spalte 1 -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="row flex">

        <div class="col-md-12 col-sm-6 col-xs-12">
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

        <div class="col-md-12 col-sm-6 col-xs-12">
          <?php
            $klassenFertig = 0;
            foreach ($klassen as $klasse => $liste) {
              foreach ($klassenliste as $k) {
                if ($klasse == $k["klasse"]) {
                  if (count($liste) - 1 == $k["anzahl"]) {
                    $klassenFertig += 1;
                  }
                  break;
                }
              }
            }
          ?>
          <div class="card w-100 text-white bg-dark p-3 border <?php
          if (count($klassenliste) == 0) {
            echo " border-danger text-danger";
          }
          elseif ($klassenFertig == count($klassenliste)) {
            echo "text-success border-success";
          }
          else {
            echo "border-warning text-warning";
          } ?>">
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
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#studentsInKlassen">
              Einstellung
            </button>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php
      					$min = 0;
                $max = 0;
      					foreach (dbRead("../data/projekte.csv") as $p) {
      						$min += $p["minPlatz"];
      						$max += $p["maxPlatz"];
      					}
      				 	echo $min . " - " . $max;
      				?></h5>
      				<p class="card-text">Plätze sind insgesamt verfügbar</p></p>
      			</div>
      		</div>
        </div>

        <div class="col-md-12 col-sm-6 col-xs-12">
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
    <div class="col-12 col-md-6 col-lg-8">
      <div class="row flex">

        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[5]["min"] . " - " . $stufen[5]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 5</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      	 	<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[6]["min"] . " - " . $stufen[6]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 6</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[7]["min"] . " - " . $stufen[7]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 7</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[8]["min"] . " - " . $stufen[8]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 8</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[9]["min"] . " - " . $stufen[9]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 9</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[10]["min"] . " - " . $stufen[10]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 10</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[11]["min"] . " - " . $stufen[11]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 11</p>
      			</div>
      		</div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-6 col-xs-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"><?php echo $stufen[12]["min"] . " - " . $stufen[12]["max"]; ?></h5>
      				<p class="card-text">Plätze sind verfügbar für Klassenstufe 12</p>
      			</div>
      		</div>
        </div>

    	</div>
    </div>
  </div>


  <!-- Klassenauflistung -->
  <div class="row flex">

    <div class="col-xl-25 col-lg-3 col-md-4 col-sm-6 col-xs-12">
      <div class="card bg-dark p-3 w-100 border <?php
      $gesamtanzahl = 0;
      foreach ($klassenliste as $klasse) {
        $gesamtanzahl += $klasse["anzahl"];
      }
      if ($gesamtanzahl == 0) {
        echo "border-danger text-danger";
      }
      elseif ($gesamtanzahl == count($wahlen)) {
        echo "text-success border-success";
      }
      else {
        echo "border-warning text-warning";
      } ?>">
        <div class="card-body">
          <h5 class="card-title"><?php echo count($wahlen); ?> von <?php echo $gesamtanzahl; ?>
          </h5>
          <p class="card-text">Schüler haben schon gewählt</p>
          <!-- Button trigger modal -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Drucken</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#schuelerModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>

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
      <div class="card bg-dark p-3 w-100 border <?php
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
          <h5 class="card-title"><?php if (count($klasse) - 1 > 0) {echo count($klasse) - 1; ?> / <?php echo $anzahl; } else {echo  "Keine";} ?></h5>
          <p class="card-text">Person<?php echo count($klasse) - 1 > 0 ? "en" : ""; ?> aus Klasse <?php echo $key; ?> ha<?php echo count($klasse) - 1 > 1 ? "ben" : "t"; ?> bereits gewählt</p>
            <button onclick="javascript: window.open('printPDF.php?print=students&klasse=<?php echo $key; ?>');" type="button" class="btn btn-primary">Auflisten</button>
        </div>
      </div>
    </div><?php
    }
    ?>

  </div>

</div>
