<div class="container-fluid row">

	<div class="col-sm-6 col-md-7 col-lg-8 col-xl-9 d-flex flex-wrap align-content-start" id="projektliste">
			<?php
			foreach ($projekte as $key => $projekt) {
			?>
			<div class="card projekt list-group-item-dark p-3">
				<input type="hidden" value="<?php echo $key; ?>">
				<div class="card-body">
					<h5><?php echo $projekt["name"]; ?></h5>
					<a href="#" class="btn btn-primary" onclick="showProjektInfoModal(projekte[<?php echo $key; ?>]);">Info</a>
				</div>
			</div>
			<?php
			}
			?>
	</div>

	<div class="col-sm-6 col-md-5 col-lg-4 col-xl-3 card" id="wahlliste">
		<form method="post"></form>
		<div class="card-body">
			<h5>Projektwahl <small>hier hinein ziehen</small></h5>
			<hr class="my-4">
			<div class="btn-group" role="group" aria-label="Button Kontrolle">
				<button class="btn btn-danger" onclick="logout()">Abmelden</button>
			</div>
			<hr class="my-4">
			<table class="table table-striped table-dark table-hover">
    		<thead>
    			<tr>
    				<th scope="col">#</th>
    				<th scope="col">Projekt</th>
    			</tr>
    		</thead>
    		<tbody>
					<?php
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					// Das ist sogar important!
					// x = anzahl Schüler / Anzahl Plätze
					function calcAnzahlProjekte($students, $platz, $anzahlProjekte) {
						$x = $students / $platz;
						if ($x < 0.6) return 4;
						$t = (75.4745 * pow($x, 4) - 223.148 * pow($x, 3) + 246.143 * pow($x, 2) - 119.873 * $x + 21.8101) * $anzahlProjekte;
						return ($t < 4 ? 4 : $t);
					}

					$platz = 0;
					$anzahlProjekte = 0;
					foreach (dbRead("../data/projekte.csv") as $projekt) {
						$platz += $projekt["maxPlatz"];
						$anzahlProjekte += 1;
					}

					for ($i = 0; $i < calcAnzahlProjekte($config["Schüleranzahl"], $platz, $anzahlProjekte); $i++) {
						echo "<tr><th>" . ($i + 1) . "</th><td></td></tr>";
					}
					?>
    		</tbody>
    	</table>
		</div>
	</div>

</div>
