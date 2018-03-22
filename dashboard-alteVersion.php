<?php
include "php/db.php";
if(!empty(array_filter($_POST))) {
	$data = read("csv/config.csv")[0];
	$names = [
		"Stage",
		"Schüleranzahl",
		"Montag",
		"Dienstag",
		"Mittwoch",
		"Donnerstag",
		"Freitag",
		"Schule_am_ersten_Vormittag"
	];


	$stage = $data["Stage"];
	$numberOfStudents = $_POST["inputSchuelerAnzahl"];
	if (array_key_exists("inlineCheckbox1", $_POST)) {
		$monday = "true";
	} else {
		$monday = "false";
	}
	if (array_key_exists("inlineCheckbox2", $_POST)) {
		$tuesday = "true";
	} else {
		$tuesday = "false";
	}
	if (array_key_exists("inlineCheckbox3", $_POST)) {
		$wednesday = "true";
	} else {
		$wednesday = "false";
	}
	if (array_key_exists("inlineCheckbox4", $_POST)) {
		$thursday = "true";
	} else {
		$thursday = "false";
	}
	if (array_key_exists("inlineCheckbox5", $_POST)) {
		$friday = "true";
	} else {
		$friday = "false";
	}
	/*
	$tuesday = ($_POST["inlineCheckbox2"] == "true");
	$wednesday = ($_POST["inlineCheckbox3"] == "true");
	$thursday = ($_POST["inlineCheckbox4"] == "true");
	$friday = ($_POST["inlineCheckbox5"] == "true");
	*/
	$firstDay = $_POST["firstDay"];

	$values = [
		$stage,
		$numberOfStudents,
		$monday,
		$tuesday,
		$wednesday,
		$thursday,
		$friday,
		$firstDay
	];

	// open the file
	$fp = fopen('csv/config.csv', 'w');

	// write the data and check for success
	if (!fputcsv($fp, $names, "#") or !fputcsv($fp, $values, "#")) {
		die("Projekt konnte nicht gespeichert werden!");
	}
	// close the file
	fclose($fp);

}

$stufen = array(5, 6, 7, 8, 9, 10, 11, 12);
$test = 0;
$anzahl = array(0, 0, 0, 0, 0, 0, 0, 0);
foreach($stufen as $stufe) {
	$all = read("csv/projekte.csv");
	foreach($all as $aaaa){
		if($aaaa["min Klasse"]<=$stufe && $aaaa["max Klasse"]>= $stufe){
			$anzahl[$test] += $aaaa["max Teilnehmer"];
		}
	}
	
	$test += 1;
}

$plaetze5 = $anzahl[0]; 
$plaetze6 = $anzahl[1];
$plaetze7 = $anzahl[2];
$plaetze8 = $anzahl[3];
$plaetze9 = $anzahl[4];
$plaetze10 = $anzahl[5]; 
$plaetze11 = $anzahl[6]; 
$plaetze12 = $anzahl[7]; 





?>
<!doctype html>
<html lang="de">
  <head>
  	
 
  	
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content= "Tim Schneider und Lukas Fausten">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/dashboard.css"> 
		<title>Dashboard</title>


  </head>
	<body>
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
    						<h5 class="card-title" id="eingereichteProjekte"><?php
                  echo count(file('csv/projekte.csv'))-1;


                      ?></h5>
    						<p class="card-text">Projekte wurden eingereicht</p></p>
  						</div>
					</div>

				  <div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
								<h5 class="card-title" id="anzahlPlaetze"><?php
									$all = read("csv/projekte.csv");
									$anzahl = 0;

									foreach($all as $aaaa){
										$anzahl += $aaaa["max Teilnehmer"];
									}
                    echo $anzahl;
                ?></h5>
    						<p class="card-text">Plätze sind verfügbar</p></p>
  						</div>
					</div>

					<div class="card text-white bg-dark mb-3" >
  						<div class="card-body">
    						<h5 class="card-title" id="schuelergewaehlt">
                  <?php echo count(file('csv/schueler.csv'))-1;?>

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
  						<h5 class="card-title" id="plaetze5">
						
							<?php echo $plaetze5 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 5</p>
						</div>
			</div>
		  <div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze6">
						
							<?php echo $plaetze6 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 6</p>
						</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze7">
						
							<?php echo $plaetze7 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 7</p>
						</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze8">
						
							<?php echo $plaetze8 ?>
							</h5>
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
  						<h5 class="card-title" id="plaetze9">
						
							<?php echo $plaetze9 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 9</p>
						</div>
			</div>
		  <div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze10">
						
							<?php echo $plaetze10 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 10</p>
						</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze11">
						
							<?php echo $plaetze11 ?>
							</h5>
  						<p class="card-text">Plätze sind verfügbar für Klassenstufe 11</p>
						</div>
			</div>
			<div class="card text-white bg-dark mb-3" >
						<div class="card-body">
  						<h5 class="card-title" id="plaetze12">
						
							<?php echo $plaetze12 ?>
							</h5>
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
            aria-valuenow="<?php echo count(file('csv/schueler.csv'))-1; ?>"
            aria-valuemin="0"
            aria-valuemax="1000">
            <?php echo(count(file('csv/schueler.csv'))-1)/1000*100  ?> %
          </div>
				</div>
				<p> <?php echo count(file('csv/schueler.csv'))-1;?> von ### Schülern haben bereits gewählt</p>
  		</div>
  		
  		-->

		</div>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="js/dashboard.js"></script>
		<script>
		a = <?php
		$daten = read("csv/config.csv")[0];/*
		if (($handle = fopen("csv/config.csv", "r")) !== FALSE) {
			$i = 0;
			while (($data = fgetcsv($handle, filesize('csv/config.csv'), ",")) !== FALSE) {
				foreach ($data as $temp) {
					if ($i != 0) {
						$daten = explode("#", $temp);
					}
					$i += 1;
				}
			}
			fclose($handle);
		}*/
		/*
		TODO:
		$daten["SchuleAmErstenVormittag"]
		*/
		echo "['" . $daten["Schüleranzahl"] . "', '" . $daten["Montag"] . "', '" . $daten["Dienstag"] . "', '" . $daten["Mittwoch"] . "', '" . $daten["Donnerstag"] . "', '" . $daten["Freitag"] . "', '" . "false" . "'];";
		?>
		</script>
	</body>
</html>
