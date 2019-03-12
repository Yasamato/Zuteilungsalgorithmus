<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "teachers") {
  die("Zugriff verweigert");
}
?>

<div class="container-fluid">
  <div class="container text-center">
		<div class="card-deck">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h3 class="card-title">Liste aller Projekte</h3>
					<p class="card-text">Lehrer - Portal</p>
				</div>
        <div class="card-footer">
          <small class="text-muted">
            Um Projekte zu löschen, wenden Sie sich an den verwaltenden Administrator.
            Nachdem die Wahlen begonnen haben oder die Einreich-Phase geschlossen wurde, können keine neuen Projekte eingereicht werden.
            Auf Anfrage kann jedoch der Administrator jederzeit Projekte manuell hinzufügen.
            Editierungen an bereits bestehenden Projekten können jederzeit von allen Lehrern vorgenommen werden.
          </small>
          <div class="text-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <button type="button" class="btn btn-danger" onclick="logout()">
                Abmelden
              </button>
            </div>
        		<div class="btn-group btn-group-toggle" data-toggle="buttons">
              <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Projekte drucken</button>
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
  </div>

  <div class="container">
    <div class="card-columns">
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
      <div class="card text-white bg-dark p-3 border <?php
      if (!$found || $anzahl < count($klasse) - 1 || count($klasse) - 1 <= 0) {
        echo "border-danger";
        if (count($klasse) - 1 < 1) {
          echo " text-danger";
        }
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
            echo " <span class='text-danger'>Diese Klasse wurde nicht in den Datensätzen gefunden!!!</span>";
          }
          elseif ($anzahl < count($klasse) - 1) {
            echo " <span class='text-danger'>Diese Klasse hat scheinbar mehr Schüler als eingetragen!!!</span>";
          }
          ?>
          <h5 class="card-title"><?php if (count($klasse) - 1 > 0) {echo count($klasse) - 1; ?> / <?php echo $anzahl; } else {echo  "Keine";} ?></h5>
          <p class="card-text">Person<?php echo count($klasse) - 1 > 0 ? "en" : ""; ?> aus Klasse <?php echo $key; ?> ha<?php echo count($klasse) - 1 > 0 ? "ben" : "t"; ?> bereits gewählt</p>
        </div>

        <div class="card-footer">
          <button onclick="javascript: window.open('printPDF.php?print=students&klasse=<?php echo $key; ?>');" type="button" class="btn btn-primary">Auflisten</button>
        </div>
      </div><?php
      }
      ?>

  </div>
</div>
