<script>
<?php
	$projekte = [];
	foreach (dbRead("../data/projekte.csv") as $p) {
		if ($p['minKlasse'] <= $_SESSION['benutzer']['stufe'] && $p['maxKlasse'] >= $_SESSION['benutzer']['stufe']) {
			array_push($projekte, $p);
		}
	}


	echo 'var projekte = [';
	foreach ($projekte as $p) {
		echo '{projektId: `' . $p['id'] . '`, ';
		echo 'name: `' . $p['name'] . '`, ';
		echo 'beschreibung: `' . $p['beschreibung'] . '`, ';
		echo 'betreuer: `' . $p['betreuer'] . '`, ';
		echo 'minKlasse: `' . $p['minKlasse'] . '`, ';
		echo 'maxKlasse: `' . $p['maxKlasse'] . '`, ';
		echo 'minTeilnehmer: `' . $p['minPlatz'] . '`, ';
		echo 'maxTeilnehmer: `' . $p['maxPlatz'] . '`, ';
		echo 'sonstiges: `' . $p['sonstiges'] . '`, ';
		echo 'vorraussetzungen: `' . $p['vorraussetzungen'] . '`, ';
		echo 'moVor: `' . $p['moVor'] . '`, ';
		echo 'moMensa: `' . $p['moMensa'] . '`, ';
		echo 'moNach: `' . $p['moNach'] . '`, ';
		echo 'diVor: `' . $p['diVor'] . '`, ';
		echo 'diMensa: `' . $p['diMensa'] . '`, ';
		echo 'diNach: `' . $p['diNach'] . '`, ';
		echo 'miVor: `' . $p['miVor'] . '`, ';
		echo 'miMensa: `' . $p['miMensa'] . '`, ';
		echo 'miNach: `' . $p['miNach'] . '`, ';
		echo 'doVor: `' . $p['doVor'] . '`, ';
		echo 'doMensa: `' . $p['doMensa'] . '`, ';
		echo 'doNach: `' . $p['doNach'] . '`, ';
		echo 'frVor: `' . $p['frVor'] . '`, ';
		echo 'frNach: `' . $p['frNach'] . '`, ';
		echo '}';
		if ($p != $projekte[sizeof($projekte) - 1]) {
			echo ",\n";
		}
	}
	echo '];';
?>
</script>

<div class="modalHolder"></div>
<div class="projektliste" id="projektliste">
	<div class="container-fluid maxWidth row text-center justify-content-center"></div>
</div>
<div class="wahlliste" id="wahlliste">
	<form method="post"></form>
	<div class="jumbotron text-center" style="color: black;">
		<h5>Projektwahl <small>hier hinein ziehen</small></h5>
		<hr class="my-4">
		<div class="btn-group" role="group" aria-label="Button Kontrolle">
			<button class="btn btn-danger" onclick="logout()">Abbrechen</button>
		</div>
		<hr class="my-4">
		<div class="table-responsive">
			<table class="table table-striped table-dark table-hover">
		    		<thead>
		    			<tr>
		    				<th scope="col">#</th>
		    				<th scope="col">Projekt</th>
		    			</tr>
		    		</thead>
		    		<tbody>
		    		</tbody>
		    	</table>
		</div>
	</div>
</div>