<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" style="color: #000;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel">Gesperrt</h5>
			</div>
			<div class="modal-body">
				<?php
				foreach ($zwangszuteilung as $key => $zuteilung) {
					if ($_SESSION["benutzer"]["uid"] == $zuteilung["uid"]) {
						// code...
					}
				}
				?>
				<p>
					Aufgrund einer Zwangszuteilung, kannst du keine Wahl tätigen.
					Du bist <a href="javascript: showProjektInfoModal(projekte[<?php echo $key; ?>]);;">diesem Projekt</a> zugeteilt.
					Bei Fragen und Problemen bitte an den Zuständigen wenden
				</p>
			</div>
			<div class="modal-footer">
				<button id="okbtn" type="button" class="btn btn-primary">OK</button>
			</div>
		</div>
	</div>
</div>
<script src="js/closedModal.js"></script>
