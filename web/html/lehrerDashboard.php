<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "teachers") {
  die("Zugriff verweigert");
}
?>

<div class="container-fluid">
  <div class="container">
    <div class="alert alert-danger d-none" role="alert" id="updateAlert">
      <div class="spinner-border text-primary m-2" role="status"></div>
    	Momentan wird ein Update durchgeführt, daher kann es zur Einschränkung aller Funktionen kommen.<br>
    	Die Seite wird automatisch aktualisiert, sobald das Update abgeschlossen ist, um die Änderungen zu übernehmen.<br>
    	Alle nicht gespeicherten Änderungen gehen dabei verloren!
    </div>

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
            <p id="einreichungGeschlossen" class="text-danger d-none">
              Die Projekteinreichephase ist geschlossen!
            </p>
          </div>

          <button type="button" class="btn btn-danger" onclick="logout()">
            Abmelden
          </button>
          <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">
            Alle Projekte drucken
          </button>
    			<button id="createProjektButton" type="button" class="btn btn-success d-none" onclick="javascript: window.location.href = '?site=create';">
            Reiche ein neues Projekt ein
          </button>
				</div>
			</div>
		</div>
  </div>

  <div class="container">
    <div id="klassen" class="row flex d-flex justify-content-center d-none"></div>

    <div class="card w-100 bg-dark p-3">
      <div class="card-body">
        <h5 class="card-title">Liste aller eingereichten Projekte</h5>
        <div class="input-group m-2">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="projekteTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <table class="table table-dark table-striped table-hover text-left">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Name</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top">Stufe</th>
              <th class="sticky-top">Platz</th>
            </tr>
          </thead>
          <tbody id="projekteTable"></tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<script src="js/lehrerDashboard.js?hash=<?php echo sha1_file("js/lehrerDashboard.js"); ?>"></script>
