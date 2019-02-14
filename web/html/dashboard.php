<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Zugriff verweigert");
}

// generate the statistics how many places are available in each class
// initialize the data array
$stufen = [
  5 => 0,
  6 => 0,
  7 => 0,
  8 => 0,
  9 => 0,
  10 => 0,
  11 => 0,
  12 => 0
];

// read each project and add the max members to the affected classes
foreach (dbRead("../data/projekte.csv") as $p) {
  for ($i = 5; $i <= 12; $i++) {
		if ($p["minKlasse"] <= $i && $p["maxKlasse"] >= $i) {
			$stufen[$i] += $p["maxPlatz"];
		}
	}
}
?>
<!-- Einstellungs-Modal -->
<div class="modal fade bd-example-modal-lg1" id="configModal" tabindex="-1" role="dialog" aria-labelledby="configModalLabel" aria-hidden="true">
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
              <select class="form-control" name="stage" id="stageSelect" aria-describedby="stageHelper"></select>
            </div>
          </div>
          <small id="stageHelper" class="form-text text-muted">
            <ul>
              <li>"Nicht veröffentlicht": Keiner hat Zugriff außer der Admin</li>
              <li>"Projekte können eingereicht werden": Änderungen an den Einstellungen zu den Projekten können nicht mehr getätigt werden, jedoch die Hinweise vom Admin verändert werden sowie über das Lehrer-Interface Projekte eingesehen, bearbeitet und eingereicht werden</li>
              <li>"Projekt-Einreichung geschlossen": Es können keine weiteren Projekte mehr von den Lehrern eingereicht werden. Editierungen bereits bestehender Projekte ist weiterhin möglich durch den Admin sowie die Lehrer</li>
              <li>"Wahl-Interface geöffnet": Die Schüler können sich nun mit ihren Login-Daten anmelden und aus ihrem Projekt-Pool ihre Wahl wählen. Die Lehrerschaft hat nun nur noch Zugriff auf die Liste mit den Projekten, kann diese jedoch aber nicht mehr abändern (nur der Admin)</li>
              <li>"Wahl abgeschlossen": Der Schüler-Zugriff wird geschlossen, Lehrer können die Liste ansehen. Änderungen können nur noch händisch von einem Admin getätigt werden. Die Auswertung wird durch einen Admin durchgeführt</li>
            </ul>
          </small>

          <div class="form-group row">
              <label class="col-sm-2 col-form-label">Anzahl an Schülern:</label>
              <div class="col-sm-10">
                <input class="form-control" type="number" placeholder="Gesamtanzahl in #" name="schuelerAnzahl" value="<?php echo $config["Schüleranzahl"]; ?>" required>
              </div>
          </div>
          <small class="form-text text-muted">
            Geben sie die Gesamtanzahl an wahlberechtigten Schülern an. Diese wird benötigt um die benötigte Anzahl an Wahlfeldern zu errechnen.
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
            <button onclick="javascript:function(){$('form#configForm').submit();}" type="submit" name="action" value="updateConfiguration" class="btn btn-primary">Speichere Änderungen</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Projekte-Modal -->
<div class="modal fade bd-example-modal-lg1" id="projekteModal" tabindex="-1" role="dialog" aria-labelledby="projekteModalLabel" aria-hidden="true">
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
        <table class="table table-dark table-striped table-hover" id="projekteTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Betreuer</th>
              <th>Stufe</th>
              <th>Platz</th>
            </tr>
          </thead>
          <tbody></tbody>
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
<div class="modal fade bd-example-modal-lg1" id="schuelerModal" tabindex="-1" role="dialog" aria-labelledby="schuelerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüler</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button onclick="javascript: alert('*drucke Schülerliste aus*');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <table class="table table-dark table-striped table-hover" id="schuelerTable">
          <thead class="thead-dark">
            <tr>
              <th>Stufe</th>
              <th>Klasse</th>
              <th>Vorname</th>
              <th>Nachname</th>
              <th>Wahl</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button onclick="javascript: alert('*drucke Schülerliste aus*');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>


<div class="container-fluid">

	<div class="container">
		<div class="card-deck">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title">Dashboard Projektwahl</h5>
					<p class="card-text">Übersicht über die Projektwahl-Datenbank</p>
				</div>
        <div class="card-footer">
          <div class="text-center">
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
		</div>
	</div>



  <!-- Beim laden der Seite soll aus Datei erlesen werden in welcher Stage die Projektwahl sich befindet. TEXT-Datei mit Werten 0-3 z.B.
  Je nach Stage, sollen andere Seiten das Abschicken der Formulare erlauben oder nicht.

  Beim Projektwahl einrichten soll eine config datei erstellt werden und folgende Werte bestimmt werden:
  Wochentage der Projektwoche, Felder deaktiveren und vielleicht text hinterlegen

  Auswählen, wieviele Projekte gewählt werden soll. Mit Formel Empfehlung abgeben, auch aufgrund der verfügbaren Plätze?? Erst wenn Projektwahl-Stage eingestellt wird -->

	<div class="container">
		<div class="card-deck">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="eingereichteProjekte"><?php echo count(dbRead("../data/projekte.csv")); ?></h5>
					<p class="card-text">Projekte wurden eingereicht</p>
      		<!-- Button trigger modal -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Drucken</button>
        		<button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#projekteModal">
        			Auflisten
        		</button>
          </div>
				</div>
			</div>

			<div class="card text-white bg-dark mb-3" >
			<div class="card-body">
				<h5 class="card-title" id="anzahlPlaetze"><?php
					$anzahl = 0;
					foreach (dbRead("../data/projekte.csv") as $p) {
						$anzahl += $p["maxPlatz"];
					}
				 	echo $anzahl;
				?></h5>
				<p class="card-text">Plätze sind insgesamt verfügbar</p></p>
			</div>
		</div>

		<div class="card text-white bg-dark mb-3" >
			<div class="card-body">
				<h5 class="card-title" id="schuelergewaehlt">
	  				<?php echo count(dbRead("../data/wahl.csv")); ?>
				</h5>
				<p class="card-text">Schüler haben schon gewählt</p>
        <!-- Button trigger modal -->
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <button onclick="javascript: alert('*drucke Schülerliste aus*');" type="button" class="btn btn-secondary">Drucken</button>
      		<button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#schuelerModal">
            Auflisten
          </button>
        </div>
			</div>
		</div>

		</div>
	</div>

	<!-- Verfügbare Plätze 5 - 8 -->

	<div class="container">
		<div class="card-deck" id="verteilungPlaetze">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze5"><?php echo $stufen[5]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 5</p>
				</div>
			</div>
		 	<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze6"><?php echo $stufen[6]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 6</p>
				</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze7"><?php echo $stufen[7]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 7</p>
				</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze8"><?php echo $stufen[8]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 8</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Verfügbare Plätze 9 - 12 -->

	<div class="container">
		<div class="card-deck" id="verteilungPlaetze">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze9"><?php echo $stufen[9]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 9</p>
				</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze10"><?php echo $stufen[10]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 10</p>
				</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze11"><?php echo $stufen[11]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 11</p>
				</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h5 class="card-title" id="plaetze12"><?php echo $stufen[12]; ?></h5>
					<p class="card-text">Plätze sind verfügbar für Klassenstufe 12</p>
				</div>
			</div>
		</div>
	</div>

			<!-- Noch nicht funktionsfähig
  		<div class="container">
  			<div class="progress" style= "height: 4em;" >
  				<div class="progress-bar bg-dark"
            role="progressbar"
            style="width: 25%;"
            aria-valuenow="<?php echo count(dbRead("../data/schueler.csv")); ?>"
            aria-valuemin="0"
            aria-valuemax="1000">
            <?php echo(count(dbRead("../data/schueler.csv"))) / 1000 * 100; ?> %
          </div>
				</div>
				<p> <?php echo count(dbRead("../data/schueler.csv")); ?> von ### Schülern haben bereits gewählt</p>
  		</div>

  		-->

</div>
