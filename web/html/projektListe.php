<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "teachers") {
  die("Zugriff verweigert");
}
?>

<div class="container-fluid">
  <div class="container text-center">
		<div class="card-deck">
			<div class="card text-white bg-dark mb-3" >
				<div class="card-body">
					<h3 class="card-title">Liste aller Projekte</h3>
					<p class="card-text">Lehrer - Portal</p>
				</div>
        <div class="card-footer">
          <div class="text-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <button type="button" class="btn btn-danger" onclick="logout()">
                Abmelden
              </button>
            </div>
        		<div class="btn-group btn-group-toggle" data-toggle="buttons">
              <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        			<button type="button" class="btn btn-success" onclick="window.location.href = '?site=create';">
                Reiche ein neues Projekt ein
              </button>
        		</div>
          </div>
        </div>
			</div>
		</div>
  </div>

  <div class="container">
    <table class="table table-dark table-striped table-hover" id="projekteTable">
      <thead>
        <tr>
          <th>Name</th>
          <th>Betreuer</th>
          <th>Stufe</th>
          <th>Platz</th>
        </tr>
      </thead>
      <tbody><?php
          foreach ($projekte as $key => $projekt) {
            echo '
        <tr>
          <td><a href="#" class="btn btn-success" onclick="showProjektInfoModal(projekte[' . $key . ']);">Info</a> ' . $projekt["name"] . '</td>
          <td>' . $projekt["betreuer"] . '</td>
          <td>' . $projekt["minKlasse"] . '-' . $projekt["maxKlasse"] . '</td>
          <td>' . $projekt["minPlatz"] . '-' . $projekt["maxPlatz"] . '</td>
        </tr>';
          }
        ?>

      </tbody>
    </table>
  </div>
</div>
