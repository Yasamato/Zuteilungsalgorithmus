window.buffer = 0.10; // +/- 10% Buffer bei den Projektplätzen

function checkCheckbox(element, attr="id", action="deleteProjekt") {
	console.log("Checkbox verändert");
	var table = $(element).closest("table");
	// input -> th | td -> tr -> thead | tbody
	if ($(element).parent().parent().parent().is("thead")) {
		if (element.checked) {
			table.find("tbody input[type='checkbox']").each(function (index) {
				$(this).prop("checked", true);
			});
		}
		else {
			table.find("tbody input[type='checkbox']").each(function (index) {
				$(this).prop("checked", false);
			});
		}
	}
	else {
		var checked = 0;
		table.find("tbody input[type='checkbox']").each(function (index) {
			if (this.checked) {
				checked++;
			}
		});
		if (checked == 0) {
			table.find("thead input[type='checkbox']").prop("checked", false);
			table.find("thead input[type='checkbox']").prop("indeterminate", false);
		}
		else if (checked == table.find("tbody input[type='checkbox']").length) {
			table.find("thead input[type='checkbox']").prop("indeterminate", false);
			table.find("thead input[type='checkbox']").prop("checked", true);
		}
		else {
			table.find("thead input[type='checkbox']").prop("checked", false);
			table.find("thead input[type='checkbox']").prop("indeterminate", true);
		}
	}

	var modal = table.closest(".modal");
	var show = 0;
	modal.find("tbody input[type='checkbox']").each(function (index) {
		if (this.checked) {
			show++;
		}
	});
	if (show) {
		if (modal.find(".deleteSelected").hasClass("d-none")) {
			modal.find(".deleteSelected").removeClass("d-none");
		}
		modal.find("form.deleteSelected").html(`<button class="btn btn-danger" type="submit" name="action" value="` + action + `deleteWahleintrag">` + show + ` Einträge löschen &#10005;</button>`);
		modal.find("tbody input[type='checkbox']").each(function (index) {
			if (this.checked) {
				modal.find("form.deleteSelected").append(`<input type="hidden" name="` + attr + `[]" value="` + $(this).closest("tr").attr(attr) + `">`);
			}
		});
	}
	else {
		if (!modal.find(".deleteSelected").hasClass("d-none")) {
			modal.find(".deleteSelected").addClass("d-none");
		}
	}
}

window.onload = function() {
	checkUp();
	var autoUpdate = setInterval(checkUp, 2000);
	$("#studentTableSearch").keyup(debounce(studentTableSearch, 300));
	$("#projekteTableSearch").keyup(debounce(projekteTableSearch, 300));
	$("#zwangszuteilungTableSearch").keyup(debounce(zwangszuteilungTableSearch, 300));
	$("#keineWahlTableSearch").keyup(debounce(keineWahlTableSearch, 300));
	$("#klassenlisteTableSearch").keyup(debounce(klassenlisteTableSearch, 300));
}
function studentTableSearch() {
  return search(this, $('#studentTable tbody tr'));
}
function projekteTableSearch() {
  return search(this, $('#projekteTable tbody tr'));
}
function zwangszuteilungTableSearch() {
  return search(this, $('#zwangszuteilungTable tr'), true);
}
function keineWahlTableSearch() {
  return search(this, $('#keineWahlTable tr'), true);
}
function klassenlisteTableSearch() {
  return search(this, $('#klassenlisteTable tr'), true);
}

function checkUp() {
	$.get("ping.php", function(data, status) {
		if (status = "success") {
			console.log("Check-Up erhalten, wende Daten-Update an");
			data = JSON.parse(data);
			updateProgressbar(data);
			updateUpdate(data["updateRunning"], data["updateLog"]);

			var changes = false;
			if (JSON.stringify(window.config) != JSON.stringify(data["config"])) {
				console.log("Konfiguration geupdated");
				window.config = data["config"];
				changes = true;
			}
			if (JSON.stringify(window.projekte) != JSON.stringify(data["projekte"])) {
				console.log("Projekte geupdated");
				window.projekte = data["projekte"];
				changes = true;
			}
			if (JSON.stringify(window.wahlen) != JSON.stringify(data["wahlen"])) {
				console.log("Wahlen geupdated");
				window.wahlen = data["wahlen"];
				changes = true;
			}
			if (JSON.stringify(window.zwangszuteilungen) != JSON.stringify(data["zwangszuteilungen"])) {
				console.log("Zwangszuteilungen geupdated");
				window.zwangszuteilungen = data["zwangszuteilungen"];
				changes = true;
			}
			if (JSON.stringify(window.klassen) != JSON.stringify(data["klassen"])) {
				console.log("Klassen geupdated");
				window.klassen = data["klassen"];
				changes = true;
			}
			if (JSON.stringify(window.klassenliste) != JSON.stringify(data["klassenliste"])) {
				console.log("Klassenliste geupdated");
				window.klassenliste = data["klassenliste"];
				changes = true;
			}
			if (JSON.stringify(window.keineWahl) != JSON.stringify(data["keineWahl"])) {
				console.log("KeineWahl geupdated");
				window.keineWahl = data["keineWahl"];
				changes = true;
			}

			if (changes) {
				console.log("Die Datensätze wurden verändert, wende Änderungen an");
				updateErrors(data);
				updateProjekte();
				updateStudents();
				updateKlassenliste();
				updateZwangszuteilung();
				updateKeineWahl();
			}
		}
		else {
			console.log("JS-Data Update failed!!");
		}
	});
}

function updateProgressbar(data) {
	var progress = data["algorithmusRunning"];
	if ($("#alertAlgorithmus details p").length) {
		$("#alertAlgorithmus details p").html(data["algorithmusLog"]);
	}
	if (progress == "false") {
		if (!$(".progress-bar").length) {
			return;
		}
		progress = 1;
	}
	console.log("Algorithmus läuft");
	progress = parseFloat(progress) * 100;
	console.log("Fortschritt: " + progress + "%");
	if (progress != 100) {
		$($('.progress-bar')[0]).css('width', progress + '%').html((progress > 50 ? (Math.round(progress * 100) / 100) + "%" : ""));
		$($('.progress-bar')[1]).css('width', (100 - progress) + '%').html((progress < 50 ? (Math.round(progress * 100) / 100) + "%" : ""));
	} else {
		$($('.progress-bar')[0]).css('width', '100%').html("100%");
		$($('.progress-bar')[1]).css('width', '0%').html("");
		setTimeout(function () {
			window.location.href = "?";
		}, 2000);
	}
}

function updateProjekte() {
	// Info-Card
	$("#projekteCard .card-title").html(window.projekte.length);
	$("#projekteCard .card-text").html("Projekt" + (window.projekte.length == 1 ? " wurde" : "e wurden") + " eingereicht");

	$("#projektPlatzCard").html("");
	$("#projektPlatzCard").append(`
<div class="col-xl-4 col-sm-6 col-xs-12">
  <div class="card w-100 text-white bg-dark p-3` + (window.pMax < window.gesamtanzahl ? " border border-danger" : (window.pMax < window.gesamtanzahl * (1 + window.buffer) || window.pMin > window.gesamtanzahl * (1 - window.buffer) ? " border border-warning" : "")) +`">
    <div class="card-body">
      <h5 class="card-title">
        <span` + (window.pMin > window.gesamtanzahl * (1 - window.buffer) ? " class='text-warning'" : "") + ">" + window.pMin + "</span> - <span" + (window.pMax < window.gesamtanzahl ? " class='text-danger'" : (window.pMax < window.gesamtanzahl * (1 + window.buffer) ? " class='text-warning'" : "")) + ">" + window.pMax + `</span>
      </h5>
      <p class="card-text">Plätze sind laut Projektangaben insgesamt verfügbar</p>
    </div>
  </div>
</div>`);

	for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
		$("#projektPlatzCard").append(`
<div class="col-xl-4 col-sm-6 col-xs-12">
  <div class="card w-100 text-white bg-dark p-3` + (window.stufen[i]["max"] < window.stufen[i]["students"] ? " border border-danger" : (window.stufen[i]["max"] < window.stufen[i]["students"] * (1 + window.buffer) || window.stufen[i]["min"] > window.stufen[i]["students"] * (1 - window.buffer) ? " border border-warning" : "")) +`">
    <div class="card-body">
      <h5 class="card-title">
        <span` + (window.stufen[i]["min"] > window.stufen[i]["students"] * (1 - window.buffer) ? " class='text-warning'" : "") + ">" + window.stufen[i]["min"] + "</span> - <span" + (window.stufen[i]["max"] < window.stufen[i]["students"] ? " class='text-danger'" : (window.stufen[i]["max"] < window.stufen[i]["students"] * (1 + window.buffer) ? " class='text-warning'" : "")) + ">" + window.stufen[i]["max"] + `</span>
      </h5>
      <p class="card-text">Plätze sind laut Projektangaben verfügbar für die Klassenstufe ` + i + `</p>
    </div>
  </div>
</div>`);
	}

	// Projekte-Modal
	$("#projekteTable").html("");
	if (window.config["Stage"] > 4) {
		if (window.projekteZuViel.length > 0) {
			var appendText = `
			<h4 class="text-warning">Die Teilnehmerzahl der folgenden Projekte überschreitet deren Maximalteilnehmeranzahl.</h4>
			<table class="table table-dark table-striped table-hover border border-warning">
			  <thead class="thead-dark">
			    <tr>
						<th><input type="checkbox" onchange="javascript: checkCheckbox(this);"></th>
			      <th class="sticky-top">Name</th>
			      <th class="sticky-top">Betreuer</th>
			      <th class="sticky-top">Stufe</th>
			      <th class="sticky-top">(Zugeteilt) Platz</th>
			    </tr>
			  </thead>
			  <tbody>`;
			for (var i in window.projekteZuViel) {
				appendText += `
			    <tr class="border-left border-warning" id="` + window.projekteZuViel[i]["id"] + `">
						<td><input type="checkbox" onchange="javascript: checkCheckbox(this);"></td>
			      <td><a href="javascript:;" onclick="javasript: showProjektInfoModal('` + window.projekteZuViel[i]["id"] + `');">` + window.projekteZuViel[i]["name"] + `</a></td>
			      <td>` + window.projekteZuViel[i]["betreuer"] + `</td>
			      <td>` + window.projekteZuViel[i]["minKlasse"] + `-` + window.projekteZuViel[i]["maxklasse"] + `</td>
			      <td class="bg-warning">(` + window.projekteZuViel[i]["teilnehmer"].length + `) ` + window.projekteZuViel[i]["minPlatz"] + `-` + window.projekteZuViel[i]["maxPlatz"] + `</td>
			    </tr>`;
			}
			$("#projekteTable").append(appendText + `
				</tbody>
			</table>`)
		}
		if (window.projekteNichtStattfinden.length > 0) {
			var appendText = `
			<h4 class="text-danger">Folgende Projekte können aufgrund mangelnder Teilnehmerzahl nicht stattfinden.</h4>
			<table class="table table-dark table-striped table-hover border border-danger">
			  <thead class="thead-dark">
			    <tr>
						<th><input type="checkbox" onchange="javascript: checkCheckbox(this);"></th>
			      <th class="sticky-top">Name</th>
			      <th class="sticky-top">Betreuer</th>
			      <th class="sticky-top">Stufe</th>
			      <th class="sticky-top">(Zugeteilt) Platz</th>
			    </tr>
			  </thead>
			  <tbody>`;
			for (var i in window.projekteNichtStattfinden) {
				appendText += `
			    <tr class="border-left border-danger" id="` + window.projekteNichtStattfinden[i]["id"] + `">
						<td><input type="checkbox" onchange="javascript: checkCheckbox(this);"></td>
			      <td><a href="javascript:;" onclick="javasript: showProjektInfoModal('` + window.projekteNichtStattfinden[i]["id"] + `');">` + window.projekteNichtStattfinden[i]["name"] + `</a></td>
			      <td>` + window.projekteNichtStattfinden[i]["betreuer"] + `</td>
			      <td>` + window.projekteNichtStattfinden[i]["minKlasse"] + `-` + window.projekteNichtStattfinden[i]["maxklasse"] + `</td>
			      <td class="bg-danger">(` + window.projekteNichtStattfinden[i]["teilnehmer"].length + `) ` + window.projekteNichtStattfinden[i]["minPlatz"] + `-` + window.projekteNichtStattfinden[i]["maxPlatz"] + `</td>
			    </tr>`;
			}
			$("#projekteTable").append(appendText + `
				</tbody>
			</table>`);
		}
	}

	var appendText = `
  <table class="table table-dark table-striped table-hover">
    <thead class="thead-dark">
      <tr>
				<th><input type="checkbox" onchange="javascript: checkCheckbox(this);"></th>
        <th class="sticky-top">Name</th>
        <th class="sticky-top">Betreuer</th>
        <th class="sticky-top">Stufe</th>
        <th class="sticky-top">` + (window.config["Stage"] > 4 ? "(Zugeteilt) " : "") + `Platz</th>
      </tr>
    </thead>
    <tbody>`;
	if (window.projekte.length == 0) {
		appendText += `
      <tr>
        <td>
          Bisher wurden keine Projekte eingereicht
        </td>
      </tr>`;
	}
	for (var i in window.projekte) {
		let color = (window.projekte[i]["teilnehmer"].length >= window.projekte[i]["minPlatz"] ? (window.projekte[i]["teilnehmer"].length <= window.projekte[i]["maxPlatz"] ? 'success' : 'warning') : 'danger');
		appendText += `
      <tr` + (window.config["Stage"] > 4 ? ` class="border-left border-` + color + `"` : '') + ` id="` + window.projekte[i]["id"] + `">
				<td><input type="checkbox" onchange="javascript: checkCheckbox(this);"></td>
        <td><a href="javascript:;" onclick="javasript: showProjektInfoModal('` + window.projekte[i]["id"] + `');">` + window.projekte[i]["name"] + `</a></td>
        <td>` + window.projekte[i]["betreuer"] + `</td>
        <td>` + window.projekte[i]["minKlasse"] + `-` + window.projekte[i]["maxKlasse"] + `</td>
        <td` + (window.config["Stage"] > 4 ? ` class="bg-` + color + `">` + `(` + window.projekte[i]["teilnehmer"].length + `) ` : `>`) + window.projekte[i]["minPlatz"] + `-` + window.projekte[i]["maxPlatz"] + `</td>
      </tr>`;
	}
	$("#projekteTable").append(appendText + `
		</tbody>
	</table>`);
}

function updateStudents() {
	// Info-Card
	if (window.gesamtanzahl == 0 || window.gesamtanzahl < window.wahlen.length) {
		if ($("#wahlenCard").hasClass("border-warning")) {
			$("#wahlenCard").removeClass("border-warning");
			$("#wahlenCard").removeClass("text-warning");
		}
		else if ($("#wahlenCard").hasClass("border-success")) {
			$("#wahlenCard").removeClass("border-success");
			$("#wahlenCard").removeClass("text-success");
		}
		if (!$("#wahlenCard").hasClass("border-danger")) {
			$("#wahlenCard").addClass("border-danger");
			$("#wahlenCard").addClass("text-danger");
		}
	}
	else if (window.gesamtanzahl == window.wahlen.length) {
		if ($("#wahlenCard").hasClass("border-danger")) {
			$("#wahlenCard").removeClass("border-danger");
			$("#wahlenCard").removeClass("text-danger");
		}
		else if ($("#wahlenCard").hasClass("border-warning")) {
			$("#wahlenCard").removeClass("border-warning");
			$("#wahlenCard").removeClass("text-warning");
		}
		if (!$("#wahlenCard").hasClass("border-success")) {
			$("#wahlenCard").addClass("border-success");
			$("#wahlenCard").addClass("text-success");
		}
	}
	else {
		if ($("#wahlenCard").hasClass("border-danger")) {
			$("#wahlenCard").removeClass("border-danger");
			$("#wahlenCard").removeClass("text-danger");
		}
		else if ($("#wahlenCard").hasClass("border-success")) {
			$("#wahlenCard").removeClass("border-success");
			$("#wahlenCard").removeClass("text-success");
		}
		if (!$("#wahlenCard").hasClass("border-warning")) {
			$("#wahlenCard").addClass("border-warning");
			$("#wahlenCard").addClass("text-warning");
		}
	}
	$("#wahlenCard .card-title").html(window.wahlen.length + " von " + window.gesamtanzahl);

	// Student-Modal
	$("#studentTable").html("");
	if (window.wahlen.length == 0) {
		$("#studentTable").append(`
<table class="table table-dark table-striped table-hover">
  <thead class="thead-dark">
    <tr>
      <th class="sticky-top">Stufe</th>
      <th class="sticky-top">Klasse</th>
      <th class="sticky-top">Vorname</th>
      <th class="sticky-top">Nachname</th>
      <th class="sticky-top">Wahl</th>
      <th class="sticky-top">Ergebnis</th>
      <th class="sticky-top">Bearbeiten</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        Bisher wurde keine Wahl getätigt
      </td>
    </tr>
  </tbody>
</table>`);
	}
  else if (window.config["Stage"] > 4 && window.studentOhneZuteilung.length > 0) {
		var appendText = `
<h4 class="text-danger">Folgende Schüler konnten nicht zugeteilt werden und benötigen eine manuelle Zuteilung!</h4>
<table class="table table-dark table-striped table-hover border border-danger">
  <thead class="thead-dark">
    <tr>
			<th class="sticky-top"><input type="checkbox" onchange="javascript: checkCheckbox(this, 'uid', 'deleteWahleintrag);"></th>
      <th class="sticky-top">Stufe</th>
      <th class="sticky-top">Klasse</th>
      <th class="sticky-top">Vorname</th>
      <th class="sticky-top">Nachname</th>
      <th class="sticky-top">Wahl</th>
      <th class="sticky-top">Ergebnis</th>
      <th class="sticky-top">Bearbeiten</th>
    </tr>
  </thead>
  <tbody>`;
		for (var i in window.studentOhneZuteilung) {
			appendText += `
	  <tr uid="` + window.studentOhneZuteilung[i]["uid"] + `">
			<td><input type="checkbox" onchange="javascript: checkCheckbox(this, 'uid', 'deleteWahleintrag);"></th>
	    <td>` + window.studentOhneZuteilung[i]["stufe"] + `</td>
	    <td>` + window.studentOhneZuteilung[i]["klasse"] + `</td>
	    <td>` + window.studentOhneZuteilung[i]["vorname"] + `</td>
	    <td>` + window.studentOhneZuteilung[i]["nachname"] + `</td>
	    <td>
	      <ol>`;
	      for (var wahl in window.studentOhneZuteilung[i]["wahl"]) {
	        appendText += `
	        <li>
	          <a href="javascript:;" onclick="showProjektInfoModal('` + window.studentOhneZuteilung[i]["wahl"][wahl] + `');">
	            ` + getProjektInfo(window.studentOhneZuteilung[i]["wahl"][wahl])["name"] + `
	          </a>
	        </li>`;
	      }
        appendText += `
	      </ol>
	    </td>
	    <td class="bg-danger">Konnte nicht zugeteilt werden</td>
	    <td class="navbar-dark">
	      <button class="navbar-toggler" type="button" onclick="javascript: editStudentModal(this);">
	        <span class="navbar-toggler-icon"></span>
	      </button>
	    </td>
	  </tr>`;
		}
		$("#studentTable").append(appendText + `
  </tbody>
</table>`);
	}

	for (var klasse in window.klassen) {
		var appendText = `
<table class="table table-dark table-striped table-hover">
  <thead class="thead-dark">
    <tr>
			<th class="sticky-top"><input type="checkbox" onchange="javascript: checkCheckbox(this, 'uid', 'deleteWahleintrag);"></th>
      <th class="sticky-top">Stufe</th>
      <th class="sticky-top">Klasse <a href="javascript: ;" onclick="javascript: $('#class-` + window.klassen[klasse][0]["klasse"] + `').collapse('toggle');">` + window.klassen[klasse][0]["klasse"] + `</a></th>
      <th class="sticky-top">Vorname</th>
      <th class="sticky-top">Nachname</th>
      <th class="sticky-top">Wahl</th>
      <th class="sticky-top">Ergebnis</th>
      <th class="sticky-top">Bearbeiten</th>
    </tr>
  </thead>
  <tbody id="class-` + window.klassen[klasse][0]["klasse"] + `" class="collapse show">`;
		for (var i in window.klassen[klasse]) {
			if (i == 0) {
				continue;
			}
			appendText += `
    <tr uid="` + window.klassen[klasse][i]["uid"] + `"` + (window.config["Stage"] > 4 ? ` class="border-left border-` + (window.klassen[klasse][i]["projekt"] ? "success" : "danger") + '"' : "") + `>
			<td><input type="checkbox" onchange="javascript: checkCheckbox(this, 'uid', 'deleteWahleintrag);"></th>
      <td>` + window.klassen[klasse][i]["stufe"] + `</td>
      <td>` + window.klassen[klasse][i]["klasse"] + `</td>
      <td>` + window.klassen[klasse][i]["vorname"] + `</td>
      <td>` + window.klassen[klasse][i]["nachname"] + `</td>
      <td>`;
			if (window.klassen[klasse][i]["wahl"].length > 0) {
				appendText += `
				<ol>`;
				for (var wahl in window.klassen[klasse][i]["wahl"]) {
					appendText += `
					<li>
						<a href="javascript:;" onclick="showProjektInfoModal('` + window.klassen[klasse][i]["wahl"][wahl] + `');">
							` + getProjektInfo(window.klassen[klasse][i]["wahl"][wahl])["name"] + `
						</a>
					</li>`;
				}
				appendText += `
				</ol>`;
			}
      else {
				appendText += `Zugeteilt`;
      }
			appendText += `
			</td>
			<td` + (window.config["Stage"] > 4 ? ' class="bg-' + (window.klassen[klasse][i]["projekt"] ? "success" : "danger") + '"' : "") + `>`;
      if (window.klassen[klasse][i]["projekt"]) {
				appendText += `
        <input type="hidden" value="` + window.klassen[klasse][i]["projekt"] + `">
        <a href="javascript: ;" onclick="javascript: showProjektInfoModal('` + window.klassen[klasse][i]["projekt"] + `');"` + (window.config["Stage"] > 4 ? ' class="text-light"' : "") + `>
          ` + getProjektInfo(window.klassen[klasse][i]["projekt"])["name"] + `
        </a>`;
      }
      else {
				appendText += window.config["Stage"] > 4 ? "Konnte nicht zugeteilt werden" : "N/A";
      }
			appendText += `
			</td>
      <td class="navbar-dark">
        <button class="navbar-toggler" type="button" onclick="javascript: editStudentModal(this);">
          <span class="navbar-toggler-icon"></span>
        </button>
      </td>
    </tr>`;
		}
		$("#studentTable").append(appendText + `
  </tbody>
</table>`);
	}
}

function updateKlassenliste() {
	// Info-Card
	if (window.klassenliste.length == 0) {
		if ($("#kompletteKlassenCard").hasClass("border-warning")) {
			$("#kompletteKlassenCard").removeClass("border-warning");
			$("#kompletteKlassenCard").removeClass("text-warning");
		}
		else if ($("#kompletteKlassenCard").hasClass("border-success")) {
			$("#kompletteKlassenCard").removeClass("border-success");
			$("#kompletteKlassenCard").removeClass("text-success");
		}
		if (!$("#kompletteKlassenCard").hasClass("border-danger")) {
			$("#kompletteKlassenCard").addClass("border-danger");
			$("#kompletteKlassenCard").addClass("text-danger");
		}
	}
	else if (window.klassenFertig == window.klassenliste.length) {
		if ($("#kompletteKlassenCard").hasClass("border-danger")) {
			$("#kompletteKlassenCard").removeClass("border-danger");
			$("#kompletteKlassenCardkompletteKlassenCard").removeClass("text-danger");
		}
		else if ($("#kompletteKlassenCard").hasClass("border-warning")) {
			$("#kompletteKlassenCard").removeClass("border-warning");
			$("#kompletteKlassenCard").removeClass("text-warning");
		}
		if (!$("#kompletteKlassenCard").hasClass("border-success")) {
			$("#kompletteKlassenCard").addClass("border-success");
			$("#kompletteKlassenCard").addClass("text-success");
		}
	}
	else {
		if ($("#kompletteKlassenCard").hasClass("border-danger")) {
			$("#kompletteKlassenCard").removeClass("border-danger");
			$("#kompletteKlassenCard").removeClass("text-danger");
		}
		else if ($("#kompletteKlassenCard").hasClass("border-success")) {
			$("#kompletteKlassenCard").removeClass("border-success");
			$("#kompletteKlassenCard").removeClass("text-success");
		}
		if (!$("#kompletteKlassenCard").hasClass("border-warning")) {
			$("#kompletteKlassenCard").addClass("border-warning");
			$("#kompletteKlassenCard").addClass("text-warning");
		}
	}
	$("#kompletteKlassenCard .card-title").html((window.klassenliste.length == 0 ? "Keine" : window.klassenFertig + " von " + window.klassenliste.length));
	$("#kompletteKlassenCard .card-text").html((window.klassenliste.length == 0 ? "Klasse wurde bisher im System eingetragen" : "Klassen haben vollständig gewählt"));

	// Fortschritts-Cards der einzelnen Klassen
	if (window.config["Stage"] > 2) {
		$("#klassenProgressCards").html("");
		for (var klasse in window.klassen) {
			let anzahl = 0, found = false;
			for (var i in window.klassenliste) {
				if (window.klassenliste[i]["klasse"].toLowerCase() == klasse.toLowerCase()) {
					anzahl = window.klassenliste[i]["anzahl"];
					found = true;
					break;
				}
			}

			$("#klassenProgressCards").append(`
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
      <div class="card shadow bg-dark p-3 w-100 border ` + (!found || anzahl < window.klassen[klasse].length - 1 ? "border-danger text-danger" : (anzahl == window.klassen[klasse].length - 1 ? "border-success text-success" : "border-warning text-warning")) + `">
        <div class="card-body">
          ` + (!found ? "<span>Diese Klasse wurde nicht in den Datensätzen gefunden!!!</span>" : (anzahl < window.klassen[klasse].length - 1 ? "<span>Diese Klasse hat scheinbar mehr Schüler als eingetragen!!!</span>" : "")) + `
          <h5 class="card-title">` + klasse +`</h5>
          <p class="card-text">` + (window.klassen[klasse].length - 1 > 0 ? (window.klassen[klasse].length - 1) + "/" + anzahl + " Personen ha" + (window.klassen[klasse].length - 1 > 1 ? "ben" : "t") + " bereits gewählt" : "Keine Person hat gewählt") + `</p>
          <button onclick="javascript: window.open('printPDF.php?print=students&klasse=` + klasse +`');" type="button" class="btn btn-primary">Auflisten</button>
        </div>
      </div>
    </div>`);
		}
	}

	// Klassenliste-Modal
	if ($("#klassenlisteTable").html() != "") {
		return;
	}
	$("#klassenlisteTable").html("");
	for (var klasse in window.klassenliste) {
		$("#klassenlisteTable").append(`
    <tr>
      <td>
        <input type="text" class="form-control" aria-label="Stufe" value="` + window.klassenliste[klasse]["stufe"] + `" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" name="stufe[]" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Klasse" value="` + window.klassenliste[klasse]['klasse'] + `" name="klasse[]" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
      </td>
      <td>
        <input type="number" class="form-control" aria-label="Schüleranzahl" value="` + window.klassenliste[klasse]['anzahl'] + `" name="anzahl[]" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
      </td>
      <td>
        <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </td>
    </tr>`);
	}
  addKlassenlisteInput();
}

function updateZwangszuteilung() {
	// Info-Card
	$("#zwangszuteilungCard .card-title").html(window.zwangszuteilungen.length);
	$("#zwangszuteilungCard .card-text").html("Schüler wurde" + (window.zwangszuteilungen.length != 1 ? "n" : "") + " fest einem Projekt zugeteilt");

	// Modal
	if ($("#zwangszuteilungTable").html() != "") {
		return;
	}
	$("#zwangszuteilungTable").html("");
	for (var student in window.zwangszuteilungen) {
		$("#zwangszuteilungTable").append(`
    <tr>
      <td>
        <input type="text" class="form-control" aria-label="U-ID" value="` + window.zwangszuteilungen[student]['uid'] + `" name="uid[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
      </td>
      <td>
        <input type="number" class="form-control" aria-label="Stufe" value="` + window.zwangszuteilungen[student]['stufe'] + `" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" name="stufe[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Klasse" value="` + window.zwangszuteilungen[student]['klasse'] + `" name="klasse[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Vorname" value="` + window.zwangszuteilungen[student]['vorname'] + `" name="vorname[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Nachname" value="` + window.zwangszuteilungen[student]['nachname'] + `" name="nachname[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
      </td>
      <td>
        <input type="hidden" class="form-control" value="` + window.zwangszuteilungen[student]['projekt'] + `" name="projekt[]">
        <button type="button" class="btn btn-success" onclick="javascript: changeProjektzuteilung(this);">Ändern</button>
      </td>
      <td>
        <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </td>
    </tr>`);
	}
	addStudentsInZwangszuteilungInput();
}

function updateKeineWahl() {
	// Info-Card
	if (window.config["Stage"] > 3) {
		if ($("#keineWahlCard").parent().hasClass("d-none")) {
			$("#keineWahlCard").parent().removeClass("d-none");
		}

		if (window.wahlen.length + window.keineWahl.length < window.gesamtanzahl) {
			if ($("#keineWahlCard").hasClass("border-warning")) {
				$("#keineWahlCard").removeClass("border-warning");
				$("#keineWahlCard").removeClass("text-warning");
			}
			else if ($("#keineWahlCard").hasClass("border-success")) {
				$("#keineWahlCard").removeClass("border-success");
				$("#keineWahlCard").removeClass("text-success");
			}
			if (!$("#keineWahlCard").hasClass("border-danger")) {
				$("#keineWahlCard").addClass("border-danger");
				$("#keineWahlCard").addClass("text-danger");
			}
		}
		else if (window.gesamtanzahl == window.wahlen.length && window.keineWahl.length == 0) {
			if ($("#keineWahlCard").hasClass("border-danger")) {
				$("#keineWahlCard").removeClass("border-danger");
				$("#keineWahlCard").removeClass("text-danger");
			}
			else if ($("#keineWahlCard").hasClass("border-warning")) {
				$("#keineWahlCard").removeClass("border-warning");
				$("#keineWahlCard").removeClass("text-warning");
			}
			if (!$("#keineWahlCard").hasClass("border-success")) {
				$("#keineWahlCard").addClass("border-success");
				$("#keineWahlCard").addClass("text-success");
			}
		}
		else {
			if ($("#keineWahlCard").hasClass("border-danger")) {
				$("#keineWahlCard").removeClass("border-danger");
				$("#keineWahlCard").removeClass("text-danger");
			}
			else if ($("#keineWahlCard").hasClass("border-success")) {
				$("#keineWahlCard").removeClass("border-success");
				$("#keineWahlCard").removeClass("text-success");
			}
			if (!$("#keineWahlCard").hasClass("border-warning")) {
				$("#keineWahlCard").addClass("border-warning");
				$("#keineWahlCard").addClass("text-warning");
			}
		}
		$("#keineWahlCard .card-title").html(window.gesamtanzahl - window.wahlen.length);
		$("#keineWahlCard .card-text").html("Schüler ha" + (window.gesamtanzahl - window.wahlen.length != 1 ? "ben" : "t") + " keine Wahl getätigt");
		if (window.gesamtanzahl - window.wahlen.length > 0) {
			if ($("#keineWahlCard .btn-group").hasClass("d-none")) {
				$("#keineWahlCard .btn-group").removeClass("d-none");
			}
		}
		else if (!$("#keineWahlCard .btn-group").hasClass("d-none")) {
			$("#keineWahlCard .btn-group").addClass("d-none");
		}
	}
	else if (!$("#keineWahlCard").parent().hasClass("d-none")) {
		$("#keineWahlCard").parent().addClass("d-none");
	}

	// KeineWahl-Modal
	if ($("#keineWahlTable").html() != "") {
		return;
	}
  for (var i in window.keineWahl) {
		$("#keineWahlTable").append(`
    <tr>
      <td>
        <input type="text" class="form-control" aria-label="U-ID" value="` + window.keineWahl[i]['uid'] + `" name="uid[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
      </td>
      <td>
        <input type="number" class="form-control" aria-label="Stufe" value="` + window.keineWahl[i]['stufe'] + `" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" name="stufe[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Klasse" value="` + window.keineWahl[i]['klasse'] + `" name="klasse[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Vorname" value="` + window.keineWahl[i]['vorname'] + `" name="vorname[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
      </td>
      <td>
        <input type="text" class="form-control" aria-label="Nachname" value="` + window.keineWahl[i]['nachname'] + `" name="nachname[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
      </td>
      <td>
        <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </td>
    </tr>`);
  }
  addStudentsInKeineWahlInput();
}

function updateErrors(data) {
	$("#errorModal .modal-body").html("");
	window.stufen = [];
	for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
		window.stufen[i] = {
			"min" : 0,
			"max" : 0,
			"students": 0
		};
	}

	// read each project and add the max members to the affected classes
	window.pMin = 0;
	window.pMax = 0;
	for (var key in window.projekte) {
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (window.projekte[key]["minKlasse"] <= i && window.projekte[key]["maxKlasse"] >= i) {
				window.stufen[i]["min"] += parseInt(window.projekte[key]["minPlatz"]);
				window.stufen[i]["max"] += parseInt(window.projekte[key]["maxPlatz"]);
			}
		}
		window.pMin += parseInt(window.projekte[key]["minPlatz"]);
		window.pMax += parseInt(window.projekte[key]["maxPlatz"]);
	}

	// Gesamtanzahl der Schüler
	window.gesamtanzahl = 0;
	for (var key in window.klassenliste) {
		window.gesamtanzahl += parseInt(window.klassenliste[key]["anzahl"]);

		// für die einzelnen Stufen
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (i == window.klassenliste[key]["stufe"]) {
				window.stufen[i]["students"] += parseInt(window.klassenliste[key]["anzahl"]);
				window.stufen[i]["students"] += parseInt(window.klassenliste[key]["anzahl"]);
			}
		}
	}

	// Zählen der bereits gewählten Schüler
	window.klassenFertig = 0;
	window.nichtEingetrageneKlassen = [];
	for (var klasse in window.klassen) {
		var found = false;
		for (var index in window.klassenliste) {
			if (klasse == window.klassenliste[index]["klasse"]) {
				if (window.klassen[klasse].length - 1 == window.klassenliste[index]["anzahl"]) {
					window.klassenFertig += 1;
				}
				found = true;
				break;
			}
		}
		if (!found) {
			window.nichtEingetrageneKlassen.push(klasse);
		}
	}

	var showErrorModal = false;
	var errorIncluded = false;

	// Nicht eingetragene Klassen
	for (var key in window.nichtEingetrageneKlassen) {
		showErrorModal = true;
		errorIncluded = true;
		$("#errorModal .modal-body").append(`
		<div class="alert alert-danger" role="alert">
			Die <strong>Klasse ` + window.nichtEingetrageneKlassen[key] + `</strong> konnte nicht gefunden werden. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript:;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a> oder bearbeiten sie den Schülereintrag <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier</a>.
		</div>`);
	}

	// Zu viele Schüler in der Klasse
	for (var key in window.klassenliste) {
		if (window.klassen[window.klassenliste[key]["klasse"]].length - 1 > window.klassenliste[key]["anzahl"]) {
			showErrorModal = true;
			errorIncluded = true;
			$("#errorModal .modal-body").append(`
			<div class="alert alert-danger" role="alert">
				Die <strong>Klasse ` + window.klassenliste[key]["klasse"] + `</strong> hat mehr Schüler als eingetragen. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript:;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a> oder bearbeiten sie den Schülereintrag <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier</a>.
			</div>`);
		}
	}

	// Wahlfortschritt nach Schülern
	if (window.config["Stage"] > 2 && window.gesamtanzahl != window.wahlen.length) {
		showErrorModal = true;
		if (window.config["Stage"] > 3) {
			errorIncluded = true;
		}
		$("#errorModal .modal-body").append(`
		<div class="alert alert-` + (window.config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger") + `" role="alert">
			Es ha` + (window.gesamtanzahl > 1 ? "ben" : "t") + ` nur ` + window.wahlen.length + " von " + window.gesamtanzahl + ` Schülern gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">auflisten</a>.
			` + (window.config["Stage"] < 4 ? `
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>` : "") + `
		</div>`);
	}

	// Wahlfortschritt nach Klassen
	if (window.config["Stage"] > 2 && window.klassenFertig != window.klassenliste.length) {
		showErrorModal = true;
		if (window.config["Stage"] > 3) {
			errorIncluded = true;
		}
		$("#errorModal .modal-body").append(`
		<div class="alert alert-` + (window.config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger") + `" role="alert">
			Es ha` + (window.klassenFertig > 1 ? "ben " : "t ") + window.klassenFertig + " von " + window.klassenliste.length + ` Klassen vollständig gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#klassenlisteModal').modal('show');" class="alert-link">auflisten</a>
			` + (window.config["Stage"] < 4 ? `
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>` : "") + `
		</div>`);
	}

	// Warnungen vor der Auswertung
	if (window.config["Stage"] < 5) {

		// Ausreichend Plätze für Schüler
		if (window.pMin > window.gesamtanzahl * (1 - window.buffer)) {
			window.showErrorModal = true;
			$("#errorModal .modal-body").append(`
			<div class="alert alert-warning" role="alert">
				Die von allen Projekten summierte Mindestteilnehmeranzahl ist ` + (window.pMin > window.gesamtanzahl ? "größer als" : "zu groß für") + ` die Gesamtschülerzahl. Falls nicht Projekte nicht stattfinden sollen, passen Sie bitte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> ggf. die Mindestteilnehmeranzahl an.
			</div>`);
		}
		if (window.pMax < window.gesamtanzahl * (1 + window.buffer)) {
			showErrorModal = true;
			if (window.pMax < window.gesamtanzahl) {
				errorIncluded = true;
			}
			$("#errorModal .modal-body").append(`
			<div class="alert alert-` + (window.pMax < window.gesamtanzahl ? "danger" : "warning") + `" role="alert">
				Die von allen Projekten summierte Maximalteilnehmeranzahl ` + (window.pMax < window.gesamtanzahl ? "ist kleiner als die" : "liegt nur wenig über der") + ` Gesamtschülerzahl und kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
			</div>`);
		}

		// Platz pro Stufe
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (window.stufen[i]["min"] > window.stufen[i]["students"] * (1 - window.buffer)) {
				showErrorModal = true;
				$("#errorModal .modal-body").append(`
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					Die von allen Projekten summierte Mindestteilnehmeranzahl für die <strong>Klassenstufe ` + i + `</strong> ist ` + (window.stufen[i]["min"] > window.stufen[i]["students"] ? "größer als" : "zu groß für") + ` die Schüleranzahl der Stufe. Dies kann zu Problemen führen und kann <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> bearbeitet werden.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>`);
			}
			if (window.stufen[i]["max"] < window.stufen[i]["students"] * (1 + window.buffer)) {
				showErrorModal = true;
				if (window.stufen[i]["max"] < window.stufen[i]["students"]) {
					errorIncluded = true;
				}
				$("#errorModal .modal-body").append(`
				<div class="alert alert-` + (window.stufen[i]["max"] < window.stufen[i]["students"] ? "danger" : "warning") + `" role="alert">
					Die von allen Projekten summierte Maximalteilnehmeranzahl für die <strong>Klassenstufe ` + i + `</strong> ist ` + (window.stufen[i]["max"] < window.stufen[i]["students"] ? "kleiner als die" : " liegt nur wenig über der") + ` Schüleranzahl der Stufe. Dies kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
				</div>`);
			}
		}
	}

	// Anzeigen des Hinweistextes
	if (showErrorModal) {
		if ($("#alertErrorModal").hasClass("d-none")) {
			$("#alertErrorModal").removeClass("d-none");
		}
  	if (errorIncluded) {
			console.log("Fehldermeldungen gefunden");
			if ($("#alertErrorModal").hasClass("alert-warning")) {
				$("#alertErrorModal").removeClass("alert-warning");
			}
			if (!$("#alertErrorModal").hasClass("alert-danger")) {
				$("#alertErrorModal").addClass("alert-danger");
			}
			$("#alertErrorModal").html(`Es sind Fehler aufgetreten. <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.`);
	  }
	  else {
			console.log("Warnungen gefunden");
			if ($("#alertErrorModal").hasClass("alert-danger")) {
				$("#alertErrorModal").removeClass("alert-danger");
			}
			if (!$("#alertErrorModal").hasClass("alert-warning")) {
				$("#alertErrorModal").addClass("alert-warning");
			}
	    $("#alertErrorModal").html(`Es sind Warnmeldungen und Hinweise aufgetreten. <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.`);
  	}
	}
	else {
		if (!$("#alertErrorModal").hasClass("d-none")) {
			$("#alertErrorModal").addClass("d-none");
		}
	}

  // Auswertung
  if (window.config["Stage"] > 3) {
		if ($("#alertAlgorithmus").hasClass("d-none")) {
			$("#alertAlgorithmus").removeClass("d-none");
		}
		if ($("#alertAlgorithmusResult").hasClass("d-none")) {
			$("#alertAlgorithmusResult").removeClass("d-none");
		}
		if (errorIncluded) {
			if (!$("#alertAlgorithmus").hasClass("alert-danger")) {
				$("#alertAlgorithmus").addClass("alert-danger");
			}
			if ($("#alertAlgorithmus").hasClass("alert-warning")) {
				$("#alertAlgorithmus").removeClass("alert-warning");
			}
			if ($("#alertAlgorithmus").hasClass("alert-success")) {
				$("#alertAlgorithmus").removeClass("alert-success");
			}
		}
		else if (data["algorithmusRunning"] != "false") {
			if (!$("#alertAlgorithmus").hasClass("alert-warning")) {
				$("#alertAlgorithmus").addClass("alert-warning");
			}
			if ($("#alertAlgorithmus").hasClass("alert-danger")) {
				$("#alertAlgorithmus").removeClass("alert-danger");
			}
			if ($("#alertAlgorithmus").hasClass("alert-success")) {
				$("#alertAlgorithmus").removeClass("alert-success");
			}
		}
		else {
			if (!$("#alertAlgorithmus").hasClass("alert-success")) {
				$("#alertAlgorithmus").addClass("alert-success");
			}
			if ($("#alertAlgorithmus").hasClass("alert-warning")) {
				$("#alertAlgorithmus").removeClass("alert-warning");
			}
			if ($("#alertAlgorithmus").hasClass("alert-danger")) {
				$("#alertAlgorithmus").removeClass("alert-danger");
			}
		}


		if (errorIncluded) {
			$("#alertAlgorithmus").html(`Aufgrund der <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">obigen Fehler</a> kann momentan keine Auswertung durchgeführt werden. Bitte korrigieren sie evtl. fehlende oder inkorrekte Angaben.`);
		}
    else if (window.config["Stage"] > 4) {
      if (data["algorithmusRunning"] == "false") {
				$("#alertAlgorithmus").html(`
    <h4 class="alert-heading">Zuteilung erfolgreich</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und die Auswertung durch den Zuteilungsalgorithmus wurde vom Admin bereits ` + (data["algorithmusRunning"] != "false" ? "gestartet" : "durchgeführt") + `.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="runZuteilungsalgorithmus">
      <div class="input-group">
        <select class="form-control custom-select" name="genauigkeit">
          <option value="1">Schnellste Laufzeit</option>
          <option value="2" selected>Normale Genauigkeit</option>
          <option value="3">Optimalere Verteilung</option>
        </select>
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">
            Erneut ausführen
          </button>
        </div>
      </div>
    </form>
		<details>
			<summary>Algorithmus-Log</summary>
			<p></p>
		</details>`);
      }
      else {
				$("#alertAlgorithmus").html(`
    <h4 class="alert-heading">Am erneuten Auswerten</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und der Zuteilungsalgorithmus wurde vom Admin erneut gestartet auf Basis der Wahlen und Zwangszuzuteilungen.
    </p>`);
      }
    }
    else {
      if (data["algorithmusRunning"] == "false") {
				$("#alertAlgorithmus").html(`
    <h4 class="alert-heading">Bereit zur Auswertung</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und somit kann die Auswertung durch den Zuteilungsalgorithmus vom Admin gestartet werden.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="runZuteilungsalgorithmus">
      <div class="input-group">
        <select class="form-control custom-select" name="genauigkeit">
          <option value="1">Schnellste Laufzeit</option>
          <option value="2" selected>Normale Genauigkeit</option>
          <option value="3">Optimalere Verteilung</option>
        </select>
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">
            Starten
          </button>
        </div>
      </div>
    </form>`);
      }
      else {
				$("#alertAlgorithmus").html(`
    <h4 class="alert-heading">Am Auswerten</h4>
    <p>
      Die Wahlphase wurde erfolgreich abgeschlossen und der Zuteilungsalgorithmus wurde vom Admin gestartet.
    </p>`);
      }
    }

		// Fortschritts-Balken
    if (data["algorithmusRunning"] != "false") {
			$("#alertAlgorithmus").append(`
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"></div>
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%;">Loading...</div>
      </div>
	    <details>
	      <summary>Algorithmus-Log</summary>
	      <p></p>
	    </details>`);
    }

		// Auswerungs-Ergebnis
		$("#alertAlgorithmusResult").html("");
    // Schüler die gewählt haben, aber nicht zugeteilt werden konnten
    window.studentOhneZuteilung = [];
    for (var wahl in window.wahlen) {
      if (window.wahlen[wahl]["projekt"] == "") {
        window.studentOhneZuteilung.push(window.wahlen[wahl]);
      }
    }
    if (window.studentOhneZuteilung.length + (window.gesamtanzahl - window.wahlen.length) > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-danger" role="alert">
        Es konnte` + (window.studentOhneZuteilung.length + window.gesamtanzahl - window.wahlen.length == 1 ? "" : "n") + ` <strong>` + (window.studentOhneZuteilung.length + window.gesamtanzahl - window.wahlen.length) + ` Schüler</strong> keinem Projekt zugeteilt werden. Diese müssen <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier manuell</a> zugeteilt werden.
      </div>`);
    }
    else {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-success" role="alert">
        Es konnten <strong>alle ` + window.wahlen.length + ` Schüler</strong> einem ihrer Wunsch-Projekte zugeteilt werden.
      </div>`);
    }

    // Projekte die Stattfinden oder nicht
    window.projekteNichtStattfinden = [];
    window.projekteZuViel = [];
    for (var projekt in window.projekte) {
      if (window.projekte[projekt]["teilnehmer"].length < window.projekte[projekt]["minPlatz"]) {
        window.projekteNichtStattfinden.push(window.projekte[projekt]);
      }
      else if (window.projekte[projekt]["teilnehmer"].length > window.projekte[projekt]["maxPlatz"]) {
        window.projekteZuViel.push(window.projekte[projekt]);
      }
    }
    if (window.projekteNichtStattfinden.length > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-danger" role="alert">
        Es können <strong>` + window.projekteNichtStattfinden.length + ` Projekte</strong> aufgrund mangelnder Teilnehmerzahl nicht stattfinden. <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div>`);
    }
    if (window.projekteZuViel.length > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-warning" role="alert">
        Die Teilnehmerzahl von <strong>` + window.projekteZuViel.length + ` Projekten</strong> übersteigt die Maximalteilnehmeranzahl. <a href="javasript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div>`);
    }
    else if (window.projekteNichtStattfinden.length == 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-success" role="alert">
        Es können <strong>alle ` + window.projekte.length + ` Projekte</strong> statt finden.
      </div>`);
    }
	}
	else {
		if (!$("#alertAlgorithmus").hasClass("d-none")) {
			$("#alertAlgorithmus").addClass("d-none");
		}
		if (!$("#alertAlgorithmusResult").hasClass("d-none")) {
			$("#alertAlgorithmusResult").addClass("d-none");
		}
	}
}
