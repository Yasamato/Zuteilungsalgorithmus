<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="color: #000;">
	<div class="modal-dialog" role="document">
		<div class="modal-content bg-dark text-light">
			<div class="modal-header">
				<h5 class="modal-title">Gesperrt</h5>
			</div>
			<div class="modal-body">
				<p><?php echo ($config["wahlTyp"] == "ag" ? "AGs" : "Projekte"); ?> können noch nicht eingereicht werden. Bei Fragen und Problemen bitte an den zuständigen Administrator wenden.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="javascript: window.location.href = '?';">OK</button>
			</div>
		</div>
	</div>
</div>

<script>
	$('.modal').modal({
		show: true,
		keyboard: false,
		backdrop: 'static'
	});
</script>
