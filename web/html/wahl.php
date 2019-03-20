<div class="container-fluid">
	<div class="container text-center d-none invisible" id="tiltPhone">
		<h4>
			Handybildschirm bitte drehen
		</h4>
		<img class="m-4" src="pictures/tiltPhone.svg" alt="Smartphone drehen" width="144" height="144">
		<p>
			<small class="text-muted">
				Icon by <a href="https://icons8.com/icon/8566/tilt-filled">Icons8</a>
			</small>
		</p>
	</div>
	<div class="container-fluid row" id="wahlpage">

		<div class="col-sm-6 col-md-7 col-lg-8 col-xl-9 d-flex flex-wrap align-content-start" id="projektliste">
				<?php
				$vorherigeWahl = null;
				$vorherigeWahlKey = array();
				foreach ($wahlen as $key => $wahl) {
					if ($_SESSION["benutzer"]["uid"] == $wahl["uid"]) {
						$vorherigeWahl = $wahl;
						break;
					}
				}
				foreach ($projekte as $key => $projekt) {
					if (!empty($vorherigeWahl["wahl"]) && in_array($projekt["id"], $vorherigeWahl["wahl"])) {
						array_push($vorherigeWahlKey, $key);
						continue;
					}
				?>
				<div class="card projekt text-black shadow list-group-item-dark p-3">
					<input type="hidden" value="<?php echo $projekt["id"]; ?>">
					<div class="card-body">
						<h5><?php echo $projekt["name"]; ?></h5>
						<a href="javascript:;" class="btn btn-primary" onclick="showProjektInfoModal(projekte[<?php echo $key; ?>]);">Info</a>
					</div>
				</div>
				<?php
				}
				?>
		</div>

		<div class="col-sm-6 col-md-5 col-lg-4 col-xl-3 card" id="wahlliste">
			<form method="post"></form>
			<div class="card-body">
				<h5 class="text-dark">Projektwahl <small>hier hinein ziehen</small></h5><?php
				if (!empty($vorherigeWahl["wahl"])) {
					?><small class="text-muted">Ihre Wahl wurde bereits gespeichert, sie können diese jedoch weiterhin während der Wahlphase editieren.</small><?php
				} ?>
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
						return intval($t < 4 ? 4 : $t);
					}

					$platz = 0;
					$anzahlProjekte = 0;
					foreach (dbRead("../data/projekte.csv") as $projekt) {
						$platz += $projekt["maxPlatz"];
						$anzahlProjekte += 1;
					}

					$gesamtanzahl = 0;
					foreach ($klassenliste as $klasse) {
						$gesamtanzahl += $klasse["anzahl"];
					}
					for ($i = 0; $i < calcAnzahlProjekte($gesamtanzahl, $platz, $anzahlProjekte); $i++) {
						echo "
						<tr>
							<th>" . ($i + 1) . "</th>
							<td>";
							if (!empty($vorherigeWahl["wahl"])) {
								$projekt = getProjektInfo($vorherigeWahl["wahl"][$i]);
							?>
							<div class="card projekt text-black shadow list-group-item-dark p-3">
								<input type="hidden" value="<?php echo $projekt["id"]; ?>">
								<div class="card-body">
									<h5><?php echo $projekt["name"]; ?></h5>
									<a href="javascript:;" class="btn btn-primary" onclick="showProjektInfoModal(projekte[<?php echo $vorherigeWahlKey[$i]; ?>]);">Info</a>
								</div>
							</div>
							<?php
							}
							echo "</td>
						</tr>";
					}
					?>
					</tbody>
	    	</table>
			</div>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/interactjs@next/dist/interact.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/interactjs@1.3.4/dist/interact.min.js"></script>-->
<script src="js/wahl.js"></script>
