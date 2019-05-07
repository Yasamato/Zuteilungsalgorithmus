// utils
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function logout() {
	$("#logout").submit();
}

function getProjektInfo(id) {
	for (var i in window.projekte) {
		if (window.projekte[i]["id"] == id) {
			return window.projekte[i];
		}
	}
	alert("Ungültige Projekt-ID");
	return;
}

function autoAppendTable(selector, appendFunction) {
	let inputs = $(selector)[0].querySelectorAll("input");
	for (var input of inputs) {
		if (!input.value.replace(/\s+/, '').length) {
			console.log(input);
			console.log("found empty");
			return;
		}
	}
	appendFunction();
}

function removeLine(element) {
	// button -> td -> tr -> tbody
	var row = element.parentNode.parentNode;
	row.parentNode.removeChild(row);
}

function showProjektInfoModal(id) {
	p = getProjektInfo(id);
	if (!p) {
		return;
	}
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
    					<p><b>Voraussetungen:</b> ` + p.vorraussetzungen + `</p>
            </div>
            <div class="col-sm-8">
              <p>` + p.beschreibung + `</p>
            </div>
          </div>
					<table class="table table-dark table-hover table-striped">
						<thead>
							<tr>
  							<th scope="col">Zeit</th>
								` + (config["MontagVormittag"] || config["MontagNachmittag"] ? "<th>Montag</th>" : "") + `
								` + (config["DienstagVormittag"] || config["DienstagNachmittag"] ? "<th>Dienstag</th>" : "") + `
								` + (config["MittwochVormittag"] || config["MittwochNachmittag"] ? "<th>Mittwoch</th>" : "") + `
								` + (config["DonnerstagVormittag"] || config["DonnerstagNachmittag"] ? "<th>Donnerstag</th>" : "") + `
								` + (config["FreitagVormittag"] || config["FreitagNachmittag"] ? "<th>Freitag</th>" : "") + `
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">Vormittag</th>
								` + (config["MontagVormittag"] || config["MontagNachmittag"] ? "<td" + (config["MontagVormittag"] ? ">" + p.moVor : " class='bg-primary'>" + config["MontagVormittagHinweis"]) + "</td>" : "") + `
								` + (config["DienstagVormittag"] || config["DienstagNachmittag"] ? "<td" + (config["DienstagVormittag"] ? ">" + p.diVor : " class='bg-primary'>" + config["DienstagVormittagHinweis"]) + "</td>" : "") + `
								` + (config["MittwochVormittag"] || config["MittwochNachmittag"] ? "<td" + (config["MittwochVormittag"] ? ">" + p.miVor : " class='bg-primary'>" + config["MittwochVormittagHinweis"]) + "</td>" : "") + `
								` + (config["DonnerstagVormittag"] || config["DonnerstagNachmittag"] ? "<td" + (config["DonnerstagVormittag"] ? ">" + p.doVor : " class='bg-primary'>" + config["DonnerstagVormittagHinweis"]) + "</td>" : "") + `
								` + (config["FreitagVormittag"] || config["FreitagNachmittag"] ? "<td" + (config["FreitagVormittag"] ? ">" + p.frVor : " class='bg-primary'>" + config["FreitagVormittagHinweis"]) + "</td>" : "") + `
							</tr>
							<tr>
								<th scope="row">Mensa-Essen</th>
								` + (config["MontagVormittag"] || config["MontagNachmittag"] ? "<td class='h4 text-" + (p.moMensa == "true" ? "success'>&#10003;" : "danger'>&#10005;") + "</td>" : "") + `
								` + (config["DienstagVormittag"] || config["DienstagNachmittag"] ? "<td class='h4 text-" + (p.diMensa == "true" ? "success'>&#10003;" : "danger'>&#10005;") + "</td>" : "") + `
								` + (config["MittwochVormittag"] || config["MittwochNachmittag"] ? "<td class='h4 text-" + (p.miMensa == "true" ? "success'>&#10003;" : "danger'>&#10005;") + "</td>" : "") + `
								` + (config["DonnerstagVormittag"] || config["DonnerstagNachmittag"] ? "<td class='h4 text-" + (p.doMensa == "true" ? "success'>&#10003;" : "danger'>&#10005;") + "</td>" : "") + `
								` + (config["FreitagVormittag"] || config["FreitagNachmittag"] ? "<td class='h4 text-" + (p.frMensa == "true" ? "success'>&#10003;" : "danger'>&#10005;") + "</td>" : "") + `
							</tr>
							<tr>
								<th scope="row">Nachmittag</th>
								` + (config["MontagVormittag"] || config["MontagNachmittag"] ? "<td" + (config["MontagNachmittag"] ? ">" + p.moNach : " class='bg-primary'>" + config["MontagNachmittagHinweis"]) + "</td>" : "") + `
								` + (config["DienstagVormittag"] || config["DienstagNachmittag"] ? "<td" + (config["DienstagNachmittag"] ? ">" + p.diNach : " class='bg-primary'>" + config["DienstagNachmittagHinweis"]) + "</td>" : "") + `
								` + (config["MittwochVormittag"] || config["MittwochNachmittag"] ? "<td" + (config["MittwochNachmittag"] ? ">" + p.miNach : " class='bg-primary'>" + config["MittwochNachmittagHinweis"]) + "</td>" : "") + `
								` + (config["DonnerstagVormittag"] || config["DonnerstagNachmittag"] ? "<td" + (config["DonnerstagNachmittag"] ? ">" + p.doNach : " class='bg-primary'>" + config["DonnerstagNachmittagHinweis"]) + "</td>" : "") + `
								` + (config["FreitagVormittag"] || config["FreitagNachmittag"] ? "<td" + (config["FreitagNachmittag"] ? ">" + p.frNach : " class='bg-primary'>" + config["FreitagNachmittagHinweis"]) + "</td>" : "") + `
							</tr>
						</tbody>
					</table>
					<small class="text-muted">
						Blau hinterlegte Tabelleneinträge kennzeichnen, dass dort kein Projekt stattfindet sondern beispielsweise regulärer Unterricht stattfindet.
					</small>
				</div>
				<div class="modal-footer">
					` + (window.user == "admin" ? `
      		<form id="delete` + p.id + `" method="post" action="?">
      			<input type="hidden" name="action" value="deleteProjekt">
      			<input type="hidden" name="id[]" value="` + p.id + `">
      		</form>
          <button type="button" class="btn btn-danger mr-auto" onclick="javascript: confirmDeleteProjekt('` + p.id + `', '` + p.name + `');">Löschen</button>
					` : "") + `
					` + (window.user == "admin" || window.user == "teachers" ? `
          <button type="button" class="btn btn-warning" onclick="javascript: window.location.href = '?site=edit&projekt=` + p.id + `';">Bearbeiten</button>
          <button type="button" class="btn btn-secondary" onclick="javascript: window.open('printPDF.php?print=projekt&projekt=` + p.id + `');">Drucken</button>
					` : "") + `
					<button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
				</div>
			</div>
		</div>
	</div>`);
  $("#tmp-modal").modal("show");
}

function confirmDeleteProjekt(id, name) {
	if (confirm("Wollen sie wirklich das Projekt '" + name + "' löschen?")) {
		$('#delete' + id).submit();
	}
	else {
		alert("Löschvorgang abgebrochen");
	}
}

// see https://stackoverflow.com/a/9127872/9127322 by https://stackoverflow.com/users/949476/dfsq
function search(me, rows, isInput = false) {
	var val = $.trim($(me).val()).replace(/ +/g, ' ').toLowerCase();
	rows.show().filter(function() {
		var text = "";
		if (isInput) {
			$(this).find("input").each(function (index) {
				text += $(this).val().replace(/\s+/g, ' ').toLowerCase();
			});
		}
		else {
    	text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
		}
    return !~text.indexOf(val);
	}).hide();
}
function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};


function updateUpdate(progress, log = false) {
	if (log && $("#updateLog p").length && $("#updateLog p").html() != log) {
		$("#updateLog p").html(log);
	}
	if (progress == "false") {
		if (!$("#updateAlert").hasClass("d-none")) {
			$("#updateAlert").addClass("d-none");
			// reload to get updated version
			setTimeout(function () {
				alert("Das Update wurde beendet und die Seite wird nun neu geladen um die neuere Version zu erhalten.");
				window.location.href = "?";
			}, 2000);
		}
		return;
	}
	if ($("#updateAlert").hasClass("d-none")) {
		$("#updateAlert").removeClass("d-none");
	}
	if (log && $("#updateAlert details p").length) {
		$("#updateAlert details p").html(log);
	}
	console.log("Update wird durchgeführt");
}
