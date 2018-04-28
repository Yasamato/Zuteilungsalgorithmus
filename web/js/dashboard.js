// Dashboard-Interface
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function createStageSelect(currentStage) {
	var stages = [
		'<option value="0">#1 Nicht veröffentlicht</option>',
		'<option value="1">#2 Projekte können eingereicht werden</option>',
		'<option value="2">#3 Projekt-Einreichung geschlossen</option>',
		'<option value="3">#4 Wahl-Interface geöffnet</option>',
		'<option value="4">#5 Wahlen abgeschlossen</option>'
	];
	$("#stageSelect").append($(stages[currentStage]));
	for(var i = 0; i < 5; i++){
		if(i == config["Stage"]) {
			continue;
		}
		$("#stageSelect").append($(stages[i]));
	}
}

function createDashboardWeekdays() {
	//disable projekt-settings, when inital phase is over
  $("#montag").prop("checked", config["Montag"]);
  if(config['Stage'] != 0){
    $("#montag").prop("disabled", true);
    $("#montag").addClass("disabled");
  }

  $("#dienstag").prop("checked", config["Dienstag"]);
  if(config['Stage'] != 0){
    $("#dienstag").prop("disabled", true);
    $("#dienstag").addClass("disabled");
  }

  $("#Mittwoch").prop("checked", config["Mittwoch"]);
  if(config['Stage'] != 0){
    $("#mittwoch").prop("disabled", true);
    $("#mittwoch").addClass("disabled");
  }

  $("#donnerstag").prop("checked", config["Donnerstag"]);
  if(config['Stage'] != 0){
    $("#donnerstag").prop("disabled", true);
    $("#donnerstag").addClass("disabled");
  }

  $("#freitag").prop("checked", config["Freitag"]);
  if(config['Stage'] != 0){
    $("#freitag").prop("disabled", true);
    $("#freitag").addClass("disabled");
  }
}

function setFirstDayIsSchool() {
  // convert the string into a bool
  config["SchuleAmErstenVormittag"] = (config["SchuleAmErstenVormittag"] == 'true');
  $("#firstDaytrue").prop("checked", config["SchuleAmErstenVormittag"]);
  //disable projekt-settings, when inital phase is over
  if(config["Stage"] != 0){
    $("#firstDaytrue").prop("disabled", true);
    $("#firstDaytrue").addClass("disabled");
    $("#firstDayfalse").prop("disabled", true);
    $("#firstDayfalse").addClass("disabled");
  }
}

function createDashboardProjekteTable(projekte) {
  console.log("Creating: projekteTable");
  $.each(projekte[0], function(index, value){
    $("#projekteTable>thead>tr").append($("<th>" + index  + "</th>"));
  });
  $.each(projekte, function(index, value){
    $("#projekteTable>tbody").append($("<tr></tr>"));
    $.each(projekte[index], function(index, value){
      $("#projekteTable>tbody:last-child").append($("<td>" + value + "</td>"));
    });
  });
}

function createDashboardStudentsTable(students) {
  console.log("Creating: schuelerTable");
  $.each(schueler[0], function(index, value){
    $("#schuelerTable>thead>tr").append($("<th>" + index  + "</th>"));
  });
  $.each(schueler, function(index, value){
    $("#schuelerTable>tbody").append($("<tr></tr>"));
    $.each(schueler[index], function(index, value){
      $("#schuelerTable>tbody:last-child").append($("<td>" + value + "</td>"));
    });
  });
}

function setupDashboard() {
	console.log("Current Configuration:");
  console.log(config);
  console.log(projekte);
  console.log(schueler);

	// formular setup
	createStageSelect(config["Stage"]);
  $("#inputSchuelerAnzahl").val(parseInt(config['Schüleranzahl']));
	createDashboardWeekdays();
	setFirstDayIsSchool();

  // Erstellen der Projekte- und Schüler-Tabellen
	createDashboardProjekteTable(projekte);
	createDashboardStudentsTable(schueler);

	// Hinzufügen der EventListener
	$('input:checkbox').on('click', function(e) {
	    e.stopImmediatePropagation();
	    var checked = (e.currentTarget.checked) ? false : true;
	    e.currentTarget.checked = (checked) ? false : checked.toString();
	});

	$('input:radio').on('click', function(e) {
	    e.stopImmediatePropagation();
	    var checked = (e.currentTarget.checked) ? false : true;
	    e.currentTarget.checked = (checked) ? false : checked.toString();
	});
}

// Drucken
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function saveSmallProjectList(projectList){
// Erstellt ein pdf-Dokument mit der Nummer, dem Titel und der Stufe aller Projekte
    var doc = new jsPDF('p', 'pt');
    // Überschrift
    var columns = ["Nr.", "Projekttitel", "Stufe"];
    var rows = [];

    // Eingabe der Projekte in rows
    projectList.forEach(function(e){
        rows.push([e.id, e.name, e.minKlasse + "-" + e.maxKlasse]);
    });
    // Einfügen der Tabelle
    doc.autoTable(columns, rows, {
        // Linien außen
        tableLineColor: [44, 62, 80],
        tableLineWidth: 0.75,
        styles: {
            // Zellen machen Zeilensprung bei großen Inhalten
            overflow: 'linebreak',
            // Linien innen
            lineColor: [44, 62, 80],
            lineWidth: 0.05
        },
        // Größe der Spalten, Wrap nicht möglich, 'wrap' funktioniert nicht, also feste Werte
        columnStyles: {
            0: {columnWidth: 25},
            1:{columnWidth:455},
            2: {columnWidth: 35},
        }
    });
    // Öffnet Dokument in neuem Tab
    doc.output('dataurlnewwindow');
    // Öffnet Speicherdialog
    //doc.save();
}

// this is removed in main.js!! a super-function will call setupDashboard();
window.onload = setupDashboard;
