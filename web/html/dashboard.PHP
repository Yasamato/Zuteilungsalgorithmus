<?php
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
$test = 0;
$anzahl = [0, 0, 0, 0, 0, 0, 0, 0];
foreach(read("data/projekte.csv") as $p){
  for($i = 5; $i < 12; $i++) {
		if($p["minKlasse"] <= $i && $p["maxKlasse"] >= $i){
			$stufen[$i] += $p["maxPlatz"];
		}
	}
}
?>
  	<div class="container-fluid">

  		<div class="container">
  			<div class="card-deck">
  				<div class="card text-white bg-dark mb-3" >
  					<div class="card-body">
    					<h5 class="card-title">Dashboard Projektwahl</h5>
    					<p class="card-text">Übersicht über die Projektwahl-Datenbank</p>
  					</div>
					</div>
				</div>
			</div>



			<div class="container" style="padding-bottom: 1rem">
						<div class="btn-group btn-group-toggle bg-dark" data-toggle="buttons" style="display: flex;">

						<!-- Button trigger modal -->
						<button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#exampleModal">
							Konfiguration
						</button>

						<!-- Modal -->
						<div class="modal fade bd-example-modal-lg1" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content bg-dark">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Projektwahlkonfiguration</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span class="closebutton" aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form id="configForm" method="post">

											<div class="form-row">
													<label for="inputSchuelerAnzahl">Anzahl Schüler</label>
													<input class="form-control" type="number" placeholder="1000" id="inputSchuelerAnzahl" name="inputSchuelerAnzahl">
											</div>

											<div class="form-row" id="wochentagecheckboxescontainer">

												<label for="wochentagecheckboxes">Wochentage</label>

												<div id="wochentagecheckboxes">

													<div class="form-check form-check-inline">
														<input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="inlineCheckbox1" value="true" checked>
														<label class="form-check-label" for="inlineCheckbox1">Montag</label>
													</div>

													<div class="form-check form-check-inline">
														<input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="inlineCheckbox2" value="true" checked>
														<label class="form-check-label" for="inlineCheckbox2">Dienstag</label>
													</div>

													<div class="form-check form-check-inline">
														<input class="form-check-input" type="checkbox" id="inlineCheckbox3" name="inlineCheckbox3" value="true" checked>
														<label class="form-check-label" for="inlineCheckbox3">Mittwoch</label>
													</div>

													<div class="form-check form-check-inline">
														<input class="form-check-input" type="checkbox" id="inlineCheckbox4" name="inlineCheckbox4" value="true" checked>
														<label class="form-check-label" for="inlineCheckbox4">Donnerstag</label>
													</div>

													<div class="form-check form-check-inline">
														<input class="form-check-input" type="checkbox" id="inlineCheckbox5" name="inlineCheckbox5" value="true" checked>
														<label class="form-check-label" for="inlineCheckbox5">Freitag</label>
													</div>

												</div>

											</div>

											<div class="form-row" id="ersterVormittagUnterricht">

												<label class="form-check-label" for="firstDay">Unterricht am ersten Projekttag-Vormittag?</label>

												<div id="firstdayradio">
													<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" id="firstDaytrue" name="firstDay" value="true">
															<label class="form-check-label" for="firstDaytrue">Ja</label>
													</div>
													<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" id="firstDayfalse" name="firstDay" value="false" checked>
															<label class="form-check-label" for="firstDayfalse">Nein</label>
													</div>
												</div>

											</div>
											<div id="configurebuttons">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
												<button onclick="form_submit()" type="submit" name="action" value="updateConfiguration" class="btn btn-primary">Save changes</button>
											</div>
										</form>

										<!-- DO EVERYTHING BUT DONT DELETE THIS!!!!-->
										<form name='searchdata' id="search_form" method="post">
										</form>

										<script type="text/javascript">
										function form_submit() {
											document.getElementById("search_form").submit();
										}
										</script>
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


      Auswählen, wieviele Projekte gewählt werden soll. Mit Formel Empfehlung abgeben, auch aufgrund der verfügbaren Plätze?? Erst wenn Projektwahl-Stage eingestellt wird

      			-->
  		<div class="container">
  			<div class="card-deck">

				  <div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="eingereichteProjekte"><?php echo count(read('data/projekte.csv')); ?></h5>
    						<p class="card-text">Projekte wurden eingereicht</p>
  						</div>
					</div>

				  <div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
								<h5 class="card-title" id="anzahlPlaetze"><?php
									$anzahl = 0;
									foreach(read("data/projekte.csv") as $p){
										$anzahl += $p["maxPlatz"];
									}
                  echo $anzahl;
                ?></h5>
    						<p class="card-text">Plätze sind verfügbar</p></p>
  						</div>
					</div>

					<div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="schuelergewaehlt">
                  <?php echo count(read('data/schueler.csv')); ?>

                </h5>
    						<p class="card-text">Schüler haben schon gewählt</p>
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
            aria-valuenow="<?php echo count(read('data/schueler.csv')); ?>"
            aria-valuemin="0"
            aria-valuemax="1000">
            <?php echo(count(read('data/schueler.csv')))/1000*100  ?> %
          </div>
				</div>
				<p> <?php echo count(read('data/schueler.csv'));?> von ### Schülern haben bereits gewählt</p>
  		</div>

  		-->

		</div>
		<script src="js/dashboard.js"></script>
		<script>
		a = <?php
		/*
		TODO:
		$daten["SchuleAmErstenVormittag"]
		*/
		echo "['" . $config["Schüleranzahl"] . "', '" . $config["Montag"] . "', '" . $config["Dienstag"] . "', '" . $config["Mittwoch"] . "', '" . $config["Donnerstag"] . "', '" . $config["Freitag"] . "', '" . "false" . "'];";
		?>
		</script>
