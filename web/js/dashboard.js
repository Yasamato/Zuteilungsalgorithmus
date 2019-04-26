window.onload = function() {
	checkUp();
	var autoUpdate = setInterval(checkUp, 2000);
}

function checkUp() {
	$.get("adminPing.php", function(data, status) {
		if (status = "success") {
			console.log("Check-Up erhalten, wende Daten-Update an");
			data = JSON.parse(data);
			updateProgressbar(data["algorithmusRunning"]);
			updateUpdate(data["updateRunning"]);
			if (window.config != data["config"]) {
				window.config = data["config"];
			}
			if (window.projekte != data["projekte"]) {
				window.projekte = data["projekte"];
			}
			if (window.wahlen != data["wahlen"]) {
				window.wahlen = data["wahlen"];
			}
			if (window.zwangszuteilungen != data["zwangszuteilungen"]) {
				window.zwangszuteilungen = data["zwangszuteilungen"];
			}
			if (window.klassen != data["klassen"]) {
				window.klassen = data["klassen"];
			}
			if (window.klassenliste != data["klassenliste"]) {
				window.klassenliste = data["klassenliste"];
			}
			updateErrors(data);
		}
		else {
			console.log("JS-Data Update failed!!");
		}
	});
}

function updateUpdate(data) {
	if (data == "false") {
		console.log("Aktuell kein Update am laufen");
		if (!$("#updateAlert").hasClass("d-none")) {
			$("#updateAlert").addClass("d-none");
			// reload to get updated version
			setTimeout(function () {
				window.location.href = "?";
			}, 2000);
		}
		return;
	}
	if ($("#updateAlert").hasClass("d-none")) {
		$("#updateAlert").removeClass("d-none");
	}
	console.log("Update wird durchgeführt");
}

function updateProgressbar(data) {
	if (data == "false") {
		console.log("Algorithmus läuft nicht");
		return;
	}
	console.log("Algorithmus läuft");
	data = parseFloat(data) * 100;
	console.log("Fortschritt: " + data + "%");
	if (data != 100) {
		$($('.progress-bar')[0]).css('width', data + '%').html((data > 50 ? (Math.round(data * 100) / 100) + "%" : ""));
		$($('.progress-bar')[1]).css('width', (100 - data) + '%').html((data < 50 ? (Math.round(data * 100) / 100) + "%" : ""));
	} else {
		$($('.progress-bar')[0]).css('width', '100%').html("100%");
		$($('.progress-bar')[1]).css('width', '0%').html("");
		setTimeout(function () {
			window.location.href = "?";
		}, 2000);
	}
}

function updateProjekt(data) {

}

function updateErrors(data) {
	$("#errorModal .modal-body").html("");
	var stufen = [];
	for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
		stufen[i] = {
			"min" : 0,
			"max" : 0,
			"students": 0
		};
	}

	// read each project and add the max members to the affected classes
	var pMin = 0;
	var pMax = 0;
	for (var key in window.projekte) {
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (window.projekte[key]["minKlasse"] <= i && window.projekte[key]["maxKlasse"] >= i) {
				stufen[i]["min"] += parseInt(window.projekte[key]["minPlatz"]);
				stufen[i]["max"] += parseInt(window.projekte[key]["maxPlatz"]);
			}
		}
		pMin += parseInt(window.projekte[key]["minPlatz"]);
		pMax += parseInt(window.projekte[key]["maxPlatz"]);
	}

	// Gesamtanzahl der Schüler
	var gesamtanzahl = 0;
	for (var key in window.klassenliste) {
		gesamtanzahl += parseInt(window.klassenliste[key]["anzahl"]);

		// für die einzelnen Stufen
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (i == window.klassenliste[key]["stufe"]) {
				stufen[i]["students"] += parseInt(window.klassenliste[key]["anzahl"]);
				stufen[i]["students"] += parseInt(window.klassenliste[key]["anzahl"]);
			}
		}
	}

	// Zählen der bereits gewählten Schüler
	var klassenFertig = 0;
	var nichtEingetrageneKlassen = [];
	for (var klasse in window.klassen) {
		var found = false;
		for (var index in window.klassenliste) {
			if (klasse == window.klassenliste[index]["klasse"]) {
				if (window.klassen[klasse].length - 1 == window.klassenliste[index]["anzahl"]) {
					klassenFertig += 1;
				}
				found = true;
				break;
			}
		}
		if (!found) {
			nichtEingetrageneKlassen.push(klasse);
		}
	}

	var buffer = 0.10; // +/- 10% Buffer bei den Projektplätzen
	var showErrorModal = false;
	var errorIncluded = false;

	// Nicht eingetragene Klassen
	for (var key in nichtEingetrageneKlassen) {
		showErrorModal = true;
		errorIncluded = true;
		$("#errorModal .modal-body").append(`
		<div class="alert alert-danger" role="alert">
			Die <strong>Klasse ` + nichtEingetrageneKlassen[key] + `</strong> konnte nicht gefunden werden. Korrigieren Sie bitte die Klassseneinträge entsprechend <a href="javascript:;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">hier</a> oder bearbeiten sie den Schülereintrag <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier</a>.
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
	if (window.config["Stage"] > 2 && gesamtanzahl != window.wahlen.length) {
		showErrorModal = true;
		if (window.config["Stage"] > 3) {
			errorIncluded = true;
		}
		$("#errorModal .modal-body").append(`
		<div class="alert alert-` + (window.config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger") + `" role="alert">
			Es ha` + (gesamtanzahl > 1 ? "ben" : "t") + ` nur ` + window.wahlen.length + " von " + gesamtanzahl + ` Schülern gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">auflisten</a>.
			` + (window.config["Stage"] < 4 ? `
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>` : "") + `
		</div>`);
	}

	// Wahlfortschritt nach Klassen
	if (window.config["Stage"] > 2 && klassenFertig != window.klassenliste.length) {
		showErrorModal = true;
		if (window.config["Stage"] > 3) {
			errorIncluded = true;
		}
		$("#errorModal .modal-body").append(`
		<div class="alert alert-` + (window.config["Stage"] < 4 ? "primary alert-dismissible fade show" : "danger") + `" role="alert">
			Es ha` + (klassenFertig > 1 ? "ben " : "t ") + klassenFertig + " von " + window.klassenliste.length + ` Klassen vollständig gewählt. Einträge <a href="javascript: ;" onclick="javascript: $('#studentsInKlassen').modal('show');" class="alert-link">auflisten</a>
			` + (window.config["Stage"] < 4 ? `
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>` : "") + `
		</div>`);
	}

	// Warnungen vor der Auswertung
	if (window.config["Stage"] < 5) {

		// Ausreichend Plätze für Schüler
		if (pMin > gesamtanzahl * (1 - buffer)) {
			showErrorModal = true;
			$("#errorModal .modal-body").append(`
			<div class="alert alert-warning" role="alert">
				Die von allen Projekten summierte Mindestteilnehmeranzahl ist ` + (pMin > gesamtanzahl ? "größer als" : "zu groß für") + ` die Gesamtschülerzahl. Falls nicht Projekte nicht stattfinden sollen, passen Sie bitte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> ggf. die Mindestteilnehmeranzahl an.
			</div>`);
		}
		if (pMax < gesamtanzahl * (1 + buffer)) {
			showErrorModal = true;
			if (pMax < gesamtanzahl) {
				errorIncluded = true;
			}
			$("#errorModal .modal-body").append(`
			<div class="alert alert-` + (pMax < gesamtanzahl ? "danger" : "warning") + `" role="alert">
				Die von allen Projekten summierte Maximalteilnehmeranzahl ` + (pMax < gesamtanzahl ? "ist kleiner als die" : "liegt nur wenig über der") + ` Gesamtschülerzahl und kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
			</div>`);
		}

		// Platz pro Stufe
		for (var i = window.config["minStufe"]; i <= window.config["maxStufe"]; i++) {
			if (stufen[i]["min"] > stufen[i]["students"] * (1 - buffer)) {
				showErrorModal = true;
				$("#errorModal .modal-body").append(`
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					Die von allen Projekten summierte Mindestteilnehmeranzahl für die <strong>Klassenstufe ` + i + `</strong> ist ` + (stufen[i]["min"] > stufen[i]["students"] ? "größer als" : "zu groß für") + ` die Schüleranzahl der Stufe. Dies kann zu Problemen führen und kann <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> bearbeitet werden.
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>`);
			}
			if (stufen[i]["max"] < stufen[i]["students"] * (1 + buffer)) {
				showErrorModal = true;
				if (stufen[i]["max"] < stufen[i]["students"]) {
					errorIncluded = true;
				}
				$("#errorModal .modal-body").append(`
				<div class="alert alert-` + (stufen[i]["max"] < stufen[i]["students"] ? "danger" : "warning") + `" role="alert">
					Die von allen Projekten summierte Maximalteilnehmeranzahl für die <strong>Klassenstufe ` + i + `</strong> ist ` + (stufen[i]["max"] < stufen[i]["students"] ? "kleiner als die" : " liegt nur wenig über der") + ` Schüleranzahl der Stufe. Dies kann zu Problemen führen. Bitte erweitern sie die Maximalzahl bestehender Projekte <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">hier</a> oder fügen sie weitere Projekte <a href="javascript: ;" onclick="javascript: window.location.href = '?site=create';" class="alert-link">hier</a> hinzu.
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
			if ($("#alertErrorModal").hasClass("alert-warning")) {
				$("#alertErrorModal").removeClass("alert-warning");
			}
			if (!$("#alertErrorModal").hasClass("alert-danger")) {
				$("#alertErrorModal").addClass("alert-danger");
			}
			$("#alertErrorModal").html(`Es sind Fehler aufgetreten. <a href="javascript:;" onclick="javascript: $('#errorModal').modal('show');" class="alert-link">Details</a>.`);
	  }
	  else {
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
    </form>`);
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
			$("#alertAlgorithmus").html(`
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"></div>
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%;">Loading...</div>
      </div>`);
    }

		// Auswerungs-Ergebnis
		$("#alertAlgorithmusResult").html("");
    // Schüler die gewählt haben, aber nicht zugeteilt werden konnten
    var studentOhneZuteilung = [];
    for (var wahl in window.wahlen) {
      if (window.wahlen[wahl]["projekt"] == "") {
        studentOhneZuteilung.push(window.wahlen[wahl]);
      }
    }
    if (studentOhneZuteilung.length > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-danger" role="alert">
        Es konnten <strong>` + studentOhneZuteilung.length + ` Schüler</strong> keinem Projekt zugeteilt werden. Diese müssen <a href="javascript: ;" onclick="javascript: $('#schuelerModal').modal('show');" class="alert-link">hier manuell</a> zugeteilt werden.
      </div>`);
    }
    else {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-success" role="alert">
        Es konnten <strong>alle ` + window.wahlen.length + ` Schüler</strong> einem ihrer Wunsch-Projekte zugeteilt werden.
      </div>`);
    }

    // Projekte die Stattfinden oder nicht
    var projekteNichtStattfinden = [];
    var projekteZuViel = [];
    for (var projekt in window.projekte) {
      if (window.projekte[projekt]["teilnehmer"].length < window.projekte[projekt]["minPlatz"]) {
        projekteNichtStattfinden.push(window.projekte[projekt]);
      }
      else if (window.projekte[projekt]["teilnehmer"].length > window.projekte[projekt]["maxPlatz"]) {
        projekteZuViel.push(window.projekte[projekt]);
      }
    }
    if (projekteNichtStattfinden.length > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-danger" role="alert">
        Es können <strong>` + projekteNichtStattfinden.length + ` Projekte</strong> aufgrund mangelnder Teilnehmerzahl nicht stattfinden. <a href="javascript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div>`);
    }
    if (projekteZuViel.length > 0) {
			$("#alertAlgorithmusResult").append(`
      <div class="alert alert-warning" role="alert">
        Die Teilnehmerzahl von <strong>` + projekteZuViel.length + ` Projekten</strong> übersteigt die Maximalteilnehmeranzahl. <a href="javasript: ;" onclick="javascript: $('#projekteModal').modal('show');" class="alert-link">Projekte einsehen</a>
      </div>`);
    }
    else if (projekteNichtStattfinden.length == 0) {
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
