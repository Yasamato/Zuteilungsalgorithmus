// utils
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function logout() {
	$("#logout").submit();
}

function showProjektInfoModal(p) {
	$(".tmp-modal").html(`
	<div class="modal fade" id="tmp-modal" tabindex="-1" role="dialog" aria-labelledby="tmp-modalLabel" aria-hidden="true" style="z-index: 1051 !important;">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content bg-dark">
				<div class="modal-header">
				<h5 class="modal-title" id="tmp-modalLabel">` + p.name + `</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
				</div>
				<div class="modal-body">
          <div class="row">
            <div class="col-sm-4">
    					<p><b>Betreuer:</b> ` + p.betreuer + `</p>
    					<p><b>Klassenstufe:</b> ` + p.minKlasse + `-` + p.maxKlasse + `</p>
    					<p><b>Teilnehmerzahl:</b> ` + p.minPlatz + `-` + p.maxPlatz + `</p>
    					<p><b>Kosten/Sonstiges:</b> ` + p.sonstiges + `</p>
    					<p><b>Vorraussetungen:</b> ` + p.vorraussetzungen + `</p>
            </div>
            <div class="col-sm-8">
              <p>` + p.beschreibung + `</p>
            </div>
          </div>
					<table class="table table-dark table-hover table-striped">
						<thead>
							<tr>
  							<th scope="col">Zeit</th>
  							<th>Montag</th>
								<th>Dienstag</th>
								<th>Mittwoch</th>
								<th>Donnerstag</th>
								<th>Freitag</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th scope="row">Vormittag</th>
									<td>` + p.moVor + `</td>
									<td>` + p.diVor + `</td>
									<td>` + p.miVor + `</td>
									<td>` + p.doVor + `</td>
									<td>` + p.frVor + `</td>
								</tr>
								<tr>
									<th scope="row">Mensa-Essen</th>
									<td>` + (p.moMensa == "true" ? "Ja" : "Nein") + `</td>
									<td>` + (p.diMensa == "true" ? "Ja" : "Nein") + `</td>
									<td>` + (p.miMensa == "true" ? "Ja" : "Nein") + `</td>
									<td>` + (p.doMensa == "true" ? "Ja" : "Nein") + `</td>
									<td>Nein</td>
								</tr>
								<tr>
									<th scope="row">Nachmittag</th>
									<td>` + p.moNach + `</td>
									<td>` + p.diNach + `</td>
									<td>` + p.miNach + `</td>
									<td>` + p.doNach + `</td>
									<td>` + p.frNach + `</td>
								</tr>
							</tbody>
						</table>
				</div>
				<div class="modal-footer">
					` + (window.user == "admin" ? `
      		<form id="delete` + p.id + `" method="post" action="/">
      			<input type="hidden" name="action" value="deleteProjekt">
      			<input type="hidden" name="projekt" value="` + p.id + `">
      		</form>
          <button type="button" class="btn btn-danger" onclick="javascript: $('#delete` + p.id + `').submit();">Löschen</button>
					` : "") + `
					` + (window.user == "admin" || window.user == "teachers" ? `
          <button type="button" class="btn btn-danger" onclick="javascript: window.location.href = '?site=edit&projekt=` + p.id + `';">Bearbeiten</button>
          <button type="button" class="btn btn-secondary" onclick="javascript: window.open('printPDF.php?print=projekt&projekt=` + p.id + `');">Drucken</button>
					` : "") + `
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>`);
  $("#tmp-modal").modal("show");
}
