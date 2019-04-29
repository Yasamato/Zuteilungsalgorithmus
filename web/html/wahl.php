<div class="container text-center d-none invisible" id="tiltPhone">
	<h4>
		Handybildschirm bitte drehen
	</h4>
	<img src="pictures/tiltPhone.svg" alt="Smartphone drehen" width="150" height="150">
	<p>
		<small class="text-muted">
			Icon by <a href="https://icons8.com/icon/8566/tilt-filled">Icons8</a>
		</small>
	</p>
</div>
<div class="row" id="wahlpage">

	<div class="col-sm-7 col-md-7 col-lg-8 col-xl-9">
		<div class="d-flex flex-wrap align-content-start" id="projektliste">
			<?php
			$vorherigeWahl = null;
			foreach ($wahlen as $key => $wahl) {
				if ($_SESSION["benutzer"]["uid"] == $wahl["uid"]) {
					$vorherigeWahl = $wahl;
					break;
				}
			}
			foreach ($projekte as $key => $projekt) {
				if (!empty($vorherigeWahl["wahl"]) && in_array($projekt["id"], $vorherigeWahl["wahl"])) {
					continue;
				}
			?>
			<div class="card projekt text-black shadow list-group-item-dark">
				<input type="hidden" value="<?php echo $projekt["id"]; ?>">
				<div class="card-body">
					<h5><?php echo $projekt["name"]; ?></h5>
					<a href="javascript: ;" onclick="javascript: showProjektInfoModal('<?php echo $projekt["id"]; ?>');" class="btn btn-primary">Info</a>
				</div>
			</div>
			<?php
			}
			?>
		</div>
	</div>

	<div class="col-sm-5 col-md-5 col-lg-4 col-xl-3">
		<div class="card sticky-top" id="wahlliste">
			<form method="post"></form>
			<div class="card-body">
				<h5 class="text-dark">Projektwahl <small>hier hinein ziehen</small></h5><?php
				if (!empty($vorherigeWahl["wahl"])) {
					?><small class="text-muted">Ihre Wahl wurde bereits gespeichert, Sie können diese jedoch weiterhin während der Wahlphase editieren.</small><?php
				} ?>
				<div class="btn-group" role="group" aria-label="Button Kontrolle">
					<button class="btn btn-danger" onclick="logout()">Abmelden</button>
				</div>
				<table class="table table-striped table-hover" style="display: table !important">
	    		<thead>
	    			<tr>
	    				<th scope="col">#</th>
	    				<th scope="col">Projekt</th>
	    			</tr>
	    		</thead>
	    		<tbody>
					<?php
					for ($i = 0; $i < CONFIG["anzahlWahlen"]; $i++) {
						echo "
						<tr>
							<th>" . ($i + 1) . "</th>
							<td>";
							if (!empty($vorherigeWahl["wahl"])) {
								$projekt = getProjektInfo($projekte, $vorherigeWahl["wahl"][$i]);
							?>
							<div class="card projekt text-black shadow list-group-item-dark">
								<input type="hidden" value="<?php echo $projekt["id"]; ?>">
								<div class="card-body">
									<h5><?php echo $projekt["name"]; ?></h5>
									<a href="javascript: ;" onclick="javascript: showProjektInfoModal('<?php echo $vorherigeWahl["wahl"][$i]; ?>');" class="btn btn-primary">Info</a>
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

<script src="js/interact-1.3.4.min.js"></script>
<script src="js/wahl.js?hash=<?php echo sha1_file("js/wahl.js"); ?>"></script>
