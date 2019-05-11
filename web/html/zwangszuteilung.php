<?php
	$id = "";
	foreach ($zwangszuteilung as $zuteilung) {
		if ($_SESSION["benutzer"]["uid"] == $zuteilung["uid"]) {
			$id = $zuteilung["projekt"];
			break;
		}
	}

	if (empty($id)) {
		die("Du wurdest einem Projekt Zwangszugeteilt, es konnte jedoch kein Projekt gefunden werden. Kontaktieren Sie bitte einen Admin.");
	}
?>

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="color: #000;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Gesperrt</h5>
			</div>
			<div class="modal-body">
				<p>
					Aufgrund einer Zwangszuteilung, kannst du keine Wahl tätigen.
					Du bist <a href="javascript: ;" onclick="javascript: showProjektInfoModal('<?php echo $id; ?>');">diese<?php echo ($config["wahlTyp"] == "ag" ? "r AG" : "m Projekt"); ?></a> zugeteilt.
					Bei Fragen und Problemen bitte an den Zuständigen wenden
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary">OK</button>
			</div>
		</div>
	</div>
</div>

<script>
	window.projekte = [<?php echo JSON_encode(getProjekteInfo($projekte, $id)); ?>];
	$('.modal').modal({
		show: true,
		keyboard: false,
		backdrop: 'static'
	});
</script>
