window.onload = function() {
	checkUp();
	var autoUpdate = setInterval(checkUp, 2000);
	$("#projekteTableSearch").keyup(debounce(projekteTableSearch, 300));
}
function projekteTableSearch() {
  return search(this, $('#projekteTable tr'));
}

function checkUp() {
	$.get("ping.php", function(data, status) {
		if (status = "success") {
			console.log("Check-Up erhalten, wende Daten-Update an");
			data = JSON.parse(data);
			updateUpdate(data["updateRunning"]);

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

			if (changes) {
				console.log("Die Datensätze wurden verändert, wende Änderungen an");
				updateInfo();
				updateProjekte();
				updateKlassen();
			}
		}
		else {
			console.log("JS-Data Update failed!!");
		}
	});
}

function updateInfo() {
	if (window.config["Stage"] > 1) {
		if ($("#einreichungGeschlossen").hasClass("d-none")) {
			$("#einreichungGeschlossen").removeClass("d-none");
		}
		if (!$("#createProjektButton").hasClass("d-none")) {
			$("#createProjektButton").addClass("d-none");
		}
	}
	else {
		if (!$("#einreichungGeschlossen").hasClass("d-none")) {
			$("#einreichungGeschlossen").addClass("d-none");
		}
		if ($("#createProjektButton").hasClass("d-none")) {
			$("#createProjektButton").removeClass("d-none");
		}
	}
}

function updateProjekte() {
  $("#projekteTable").html("");
  if (window.projekte.length == 0) {
    $("#projekteTable").append(`
    <tr>
      <td>
        Bisher wurden keine ` + (window.config["wahlTyp"] == "ag" ? "AG" : "Projekt") + ` eingereicht
      </td>
    </tr>`);
    return;
  }
  for (var i in window.projekte) {
    $("#projekteTable").append(`
    <tr>
      <td><a href="javascript:;" onclick="showProjektInfoModal('` + window.projekte[i]["id"] + `');">` + window.projekte[i]["name"] + `</a></td>
      <td>` + window.projekte[i]["betreuer"] + `</td>
      <td>` + window.projekte[i]["minKlasse"] + `-` + window.projekte[i]["maxKlasse"] + `</td>
      <td>` + window.projekte[i]["minPlatz"] + `-` + window.projekte[i]["maxPlatz"] + `</td>
    </tr>`);
  }
}

function updateKlassen() {
  if (window.config["Stage"] < 3) {
    if (!$("#klassen").hasClass("d-none")) {
      $("#klassen").addClass("d-none")
    }
    return;
  }

  $("#klassen").html("");
  for (var klasse in window.klassen) {
    var anzahl = 0, found = false;
    for (var i in window.klassenliste) {
      if (window.klassenliste[i]["klasse"].toLowerCase() == klasse.toLowerCase()) {
        anzahl = window.klassenliste[i]["anzahl"];
        found = true;
        break;
      }
    }
    $("#klassen").append(`
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
      <div class="card w-100 bg-dark p-3 border ` + (!found || anzahl < window.klassen[klasse].length - 1 ? `border-danger text-danger` : (anzahl == window.klassen[klasse].length - 1 ? `text-success border-success` : `border-warning text-warning`)) + `">
        <div class="card-body">
          ` + (!found ? " <span>Diese Klasse wurde nicht in den Datensätzen gefunden!!!</span>" : (anzahl < window.klassen[klasse].length - 1 ? " <span>Diese Klasse hat scheinbar mehr Schüler als eingetragen!!!</span>" : "")) + `
          <h5 class="card-title">` + klasse + `</h5>
          <p class="card-text">` + (window.klassen[klasse].length - 1 > 0 ? (window.klassen[klasse].length - 1) + "/" + anzahl + " Personen ha" + (window.klassen[klasse].length - 1 > 1 ? "ben" : "t") + " bereits gewählt" : "Keine Person hat gewählt") + `</p>
          <button onclick="javascript: window.open('printPDF.php?print=students&klasse=` + klasse + `');" type="button" class="btn btn-primary">Auflisten</button>
        </div>
      </div>
    </div>`);
  }

}
