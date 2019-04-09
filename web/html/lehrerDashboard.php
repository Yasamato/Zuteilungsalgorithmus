<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "teachers") {
  die("Zugriff verweigert");
}
?>

<div class="container-fluid">
  <div class="container">
		<div class="card-deck">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h3 class="card-title">Lehrer - Portal</h3>
          <div class="card-text">
            <small class="text-muted">
              Um Projekte zu löschen, wenden Sie sich an den verwaltenden Administrator.
              Nachdem die Wahlen begonnen haben oder die Einreich-Phase geschlossen wurde, können keine weiteren neuen Projekte eingereicht werden.
              Auf Anfrage kann jedoch der Administrator jederzeit noch Projekte manuell hinzufügen.
              Editierungen an bereits bestehenden Projekten können jederzeit von allen Lehrern vorgenommen werden.
              Die Listen der Wahlergebnisse werden durch den Administrator nach Abschluss der Auswertung an die Lehrerschaft sowie Projektleiter verteilt.
            </small>
            <?php
            if ($config["Stage"] > 1) {
            ?>
            <p class="text-danger">
              Die Projekteinreichephase ist geschlossen!
            </p>
            <?php
            }
            ?>
          </div>

          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-danger" onclick="logout()">
              Abmelden
            </button>
          </div>
      		<div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">
              Alle Projekte drucken
            </button>
            <?php if ($config["Stage"] < 2) { ?>
        			<button type="button" class="btn btn-success" onclick="window.location.href = '?site=create';">
                Reiche ein neues Projekt ein
              </button>
              <?php
            } ?>
          </div>
				</div>
			</div>
		</div>
  </div>

  <div class="container">
    <?php if ($config["Stage"] > 2) { ?>
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
        <div class="card w-100 bg-dark p-3 border <?php
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

    <div class="card w-100 bg-dark p-3">
      <div class="card-body">
        <h5 class="card-title">Liste aller eingereichten Projekte</h5>
        <table class="table table-dark table-striped table-hover text-left">
          <thead>
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
    </div>

  </div>

</div>
