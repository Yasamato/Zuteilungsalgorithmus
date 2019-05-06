<div class="container text-center login-box d-flex justify-content-center">
  <div class="alert alert-danger d-none" role="alert" id="updateAlert">
    <div class="spinner-border text-primary m-2" role="status"></div>
  	Momentan wird ein Update durchgeführt, daher kann es zur Einschränkung aller Funktionen kommen.<br>
  	Die Seite wird automatisch aktualisiert, sobald das Update abgeschlossen ist, um die Änderungen zu übernehmen.<br>
  	Alle nicht gespeicherten Änderungen gehen dabei verloren!
  </div>

	<form class="form-signin" method="post">
<?php
	if (isset($loginResult)) {
?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Verweigert</strong> Falsche Benutzerdaten!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
<?php } ?>
		<img class="mb-4" src="pictures/wahlbox.svg" alt="Wahlbox" width="144" height="144">
		<h1 class="h3 mb-3 font-weight-normal">Anmeldung</h1>
		<label for="inputBenutzername" class="sr-only">Benutzername</label>
		<input type="text" name="user" class="form-control" placeholder="Benutzername" required autofocus>
		<label for="inputPasswort" class="sr-only">Passwort</label>
		<input type="password" name="pw" class="form-control" placeholder="Passwort" required>
		<button name="action" value="login" class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
	</form>
</div>

<script src="js/pingUpdateOnly.js"></script>
