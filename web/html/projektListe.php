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
      if (empty($klassen)) {
        ?>
        <div class="card text-white bg-dark p-3">
          <div class="card-body">
            <h5 class="card-title">Niemand</h5>
            <p class="card-text">hat bereits gewählt, hier würden alle Klassen aufgelistet werden von denen bereits Schüler gewählt haben.</p>
          </div>
        </div><?php
      }
      foreach ($klassen as $key => $klasse) {
        ?>
        <div class="card text-white bg-dark p-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo count($klasse) > 0 ? count($klasse) : "0"; ?></h5>
            <p class="card-text">Person<?php echo count($klasse) == 1 ? "" : "en"; ?> aus Klasse <?php echo $key; ?> ha<?php echo count($klasse) == 1 ? "t" : "ben"; ?> bereits gewählt</p>
            <button onclick="javascript: window.open('printPDF.php?print=students&klasse=<?php echo $key; ?>');" type="button" class="btn btn-secondary">Liste Drucken</button>
          </div>
        </div><?php
      }
      ?>

    </div>

    <table class="table table-dark table-striped table-hover" id="projekteTable">
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
          <td><a href="#" class="btn btn-success" onclick="showProjektInfoModal(projekte[' . $key . ']);">Info</a> ' . $projekt["name"] . '</td>
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
