// super-function
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
window.onload = function() {
  console.log("Page: " + site);
  switch(site) {
    case "wahl": return setupWahl();
    case "dashboard": return setupDashboard();
    case "projektErstellung": return setupProjektErstellung();
    case "closed": return setupClosedModal();
    default: return;
  }
}

// utils
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function logout() {
	$("#logout").submit();
}

function showProjektInfoModal(p) {
	$(".tmp-modal").html(`
	<div class="modal fade" id="tmp-modal" tabindex="-1" role="dialog" aria-labelledby="tmp-modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="tmp-modalLabel">` + p.name + `</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>` + p.beschreibung + `</p>
					<p><b>Betreuer:</b> ` + p.betreuer + `</p>
					<p><b>Klassenstufe:</b> ` + p.minKlasse + `-` + p.maxKlasse + `</p>
					<p><b>Teilnehmerzahl:</b> ` + p.minTeilnehmer + `-` + p.maxTeilnehmer + `</p>
					<p><b>Kosten/Sonstiges:</b> ` + p.sonstiges + `</p>
					<p><b>Vorraussetungen:</b> ` + p.vorraussetzungen + `</p>
					<table class="table table-hover table-responsive table-striped">
						<thead class="thead-dark">
							<tr>
							<th scope="col">Zeit</th>
							<th >Montag</th>
								<th >Dienstag</th>
								<th >Mittwoch</th>
								<th >Donnerstag</th>
								<th >Freitag	</th>
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
									<td>` + p.moMensa + `</td>
									<td>` + p.diMensa + `</td>
									<td>` + p.miMensa + `</td>
									<td>` + p.doMensa + `</td>
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
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>`);
  $("#tmp-modal").modal("show");
}

// Seite gesperrt
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function setupClosedModal() {
	$('.modal').modal({
		show: true,
		keyboard: false,
		backdrop: 'static'
  });
	$("#okbtn").on("click", function() {
		window.location.href = "/";
	});
}

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

function createWeekdays() {
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
	createWeekdays();
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

// Wahl-Interface
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
var colors = [
	"primary",
	"success",
	"dark",
];

// x = anzahl Schüler / Anzahl Plätze
function calcAnzahlProjekte(students, platz, anzahlProjekte) {
	var x = students / platz;
	if(x < 0.6) return 4;
	var t = (75.4745 * Math.pow(x, 4) - 223.148 * Math.pow(x, 3) + 246.143 * Math.pow(x, 2) - 119.873 * x + 21.8101) * anzahlProjekte;
	console.log("Schüler: " + students + " / Plätze: " + platz + " = " + x);
	console.log("f(" + x + ") = " + t);
	return (t < 4 ? 4 : t);
}

function createProjektCard(e) {
	$(".projektliste>div").append($(`
		<div class="card list-group-item-` + colors[Math.floor(Math.random() *  colors.length)] + ` projekt-card" id="drag` + e.id + `">
			<input  type="hidden" value="` + e.id + `">
			<div class="card-body">
				<h5 class="card-title">` + e.name + `</h5>
				<a href="#" class="btn btn-` + colors[Math.floor(Math.random() *  colors.length)] + `" onclick="showProjektInfoModal(projekte[` + projekte.indexOf(e) + `]);">Info</a>
			</div>
		</div>`));
}

function createWahlTable(projektAnzahl) {
	for(var i = 0; i < projektAnzahl; i++){
		$(".wahlliste tbody").append("<tr id='wahl" + i + "'><th scope='row'>" + (i + 1) + "</th><td></td></tr>");
	}
}

function getInput() {
	console.log("counting choosen projekts");
	var wahl = [];
	for(var i = 0; i < $(".wahlliste tbody>tr").length; i++) {
		if($("#wahl" + i + ">td").children().length == 0) {
			if($(".btn-group").children().length > 1) {
				$($(".btn-group").children()[2]).remove();
			}
			return;
		}
		else {
			wahl.push($($("#wahl" + i + ">td").children()[0]));
		}
	}
	if($(".btn-group").children().length < 2) {
		$(".btn-group").append($("<button class='btn btn-success' name='action' value='wahl'>Wahl abschicken</button>").on("click", function(e){
			$(".wahlliste>form").empty();
			for(var i = 0; i < wahl.length; i++) {
				$(".wahlliste>form").append($("<input type='hidden' name='wahl[" + i + "]'>").val($(wahl[i].children()[0]).val()));
				console.log("input angehängt");
			}
			$(".wahlliste>form").append($("<input type='hidden' name='action'>").val("wahl"));
			$(".wahlliste>form").submit();
		}));
	}
	return;
}

// init listeners
function addCardListener(card) {
	$(this).on("touchstart touchmove touchend", touchHandler);
	$(this).attr("draggable", "true");
	$(this).on("dragstart", cardDragstart);
	$(this).on("dragover", preventDefaultActions);
	$(this).on("drop", dropCard);
}

function addTableListener(row) {
	$(this).on("drop", dropOnTable);
	$(this).on("dragover", preventDefaultActions);
}

function setupWahl() {
	// html creation
	createWahlTable(calcAnzahlProjekte(50, 70, 20));
	projekte.forEach(function(e){
		createProjektCard(e);
	});

	// Die Projekte selbst
	$(".card").each(addCardListener);

	// Wahlliste (rechts)
	$(".wahlliste").on("drop", dropWahlliste);
	$(".wahlliste").on("dragover", preventDefaultActions);
	// tabelle rechts zum reinziehen
	$(".wahlliste tbody>tr").each(addTableListener);

	// Projektliste (links)
	$(".projektliste").on("drop", dropProjektliste);
	$(".projektliste").on("dragover", preventDefaultActions);
}

// Drag Handles
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function switchInTable(ele1, ele2) {
	console.log("switch.....");
	// suche Position in der Tabelle
	var iEle1 = -1, iEle2 = -1;
	for(var i = 0; i < $(".wahlliste tbody>tr").length; i++){
		if($("#wahl" + i).has(ele1).length || $("#wahl" + i).is(ele1)) {
			iEle1 = i;
		}
		else if($("#wahl" + i).has(ele2).length || $("#wahl" + i).is(ele2)) {
			iEle2 = i;
		}
	}

	// check ob auch projekt-card ausgewählt wurde, ersetze ggf.
	if(!$(ele1).hasClass("card")) {
		ele1 = $(ele1).find("div")[0];
		console.log("ele1: no card");
	}
	if(!$(ele2).hasClass("card")) {
		ele2 = $(ele2).find("div")[0];
		console.log("ele2: no card");
	}

	// vertausche bzw. setze ein
	if(ele1 == ele2) {
		console.log("mit sich selbst vertauscht....");
		return;
	}
	if(iEle1 > -1 && iEle2 > -1) {
		if(typeof ele2 !== "undefined") {
			$("#wahl" + iEle1).children()[1].append(ele2);
		}
		if(typeof ele1 !== "undefined") {
			$("#wahl" + iEle2).children()[1].append(ele1);
		}
	}
	else if(iEle1 > -1) {
		$("#wahl" + iEle1).children()[1].append(ele2);
		if(typeof ele1 !== "undefined") {
			$(".projektliste>div").append(ele1);
		}
	}
	else if(iEle2 > -1) {
		$("#wahl" + iEle2).children()[1].append(ele1);
		if(typeof ele2 !== "undefined") {
			$(".projektliste>div").append(ele2);
		}
	}
}

function appendWahlliste(card) {
	for(var i = $(".wahlliste tbody>tr").length - 1; i >= 0; i--) {
		if($("#wahl" + i + ">td").children().length == 0) {
			$("#wahl" + i + ">td").append(card);
		}
	}
}

function preventDefaultActions(e) {
	e.originalEvent.stopPropagation();
	e.originalEvent.preventDefault();
}

function cardDragstart(e) {
	e.originalEvent.stopPropagation();
	if(e.originalEvent.dataTransfer) {
		e.originalEvent.dataTransfer.setData("Text", this.id);
	}
	else {
		console.log("!!------------------------!!");
		console.log("DataTransfer is missing!!");
	}
}

function dropCard(e) {
	preventDefaultActions(e);
	var card = $("#" + e.originalEvent.dataTransfer.getData('Text'))[0];
	console.log("dropped element on card");
	console.log(e.originalEvent.currentTarget);
	if($(".wahlliste").has(e.originalEvent.currentTarget).length) {
		console.log("in Wahlliste");
		switchInTable(card, e.originalEvent.currentTarget);
	}
	else {
		console.log("in Projektliste");
		$(e.originalEvent.currentTarget).before(card);
	}
	getInput();
}

function dropWahlliste(e) {
	preventDefaultActions(e);
	console.log("dropped element on wahlliste");
	console.log(e.originalEvent.currentTarget);
	appendWahlliste($("#" + e.originalEvent.dataTransfer.getData('Text')));
	getInput();
}

function dropOnTable(e) {
	preventDefaultActions(e);
	console.log("dropped element on tabelle");
	console.log(e.originalEvent.currentTarget);
	switchInTable($("#" + e.originalEvent.dataTransfer.getData('Text'))[0], e.originalEvent.currentTarget);
	getInput();
}

function dropProjektliste(e) {
	preventDefaultActions(e);
	console.log("dropped element on projektliste");
	console.log(e.originalEvent.currentTarget);
	$(".projektliste>div").append($("#" + e.originalEvent.dataTransfer.getData('Text')));
	getInput();
}

// Touch Events -> Drag Events
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function findProjektCard(element) {
  for(i = 0; i < $("div.card").length; i++){
    if($("div.card")[i] == element || $($("div.card")[i]).has(element).length){
      return $("div.card")[i];
    }
  }
  return null;
}

// Searches the element the target is child of (prjekt-card, wahlliste, projketliste)
function findCurrentTarget(target) {
  for(i = 0; i < projekte.length; i++) {
    if($("#drag" + projekte.projektId) == $(target) || $("#drag" + projekte.projektId).has(target)) {
      return document.getElementById("#drag" + projekte.projektId);
    }
  }

  if($(".wahlliste") == $(target) || $(".wahlliste").has(target)) {
    return document.getElementById("wahlliste");
  }
  return document.getElementById("projektliste");
}

function getDragEvent(type){
  switch(type) {
    case "touchstart": return "dragstart";
    case "touchmove": return "dragover";
    case "touchend": return "drop";
    default: return null;
  }
}

// Handles all touch events on projekt-cards
function touchHandler(e) {
  // disable multi-touch
  if(e.changedTouches.length != 1) return;
  var touch = e.changedTouches[0];

  // find dragged card
  var card = findProjektCard(e.target);
  if(!card) return;

  // in case of pressing the info button, return
  if($(card).find("a")[0] == e.target) return;

  //apply ghost image
  $(card).css({
    "top": touch.clientY + 5,
    "left": "calc(" + touch.clientX + "px - 7.5em)"
  });

  // get target and currentTarget
  var target = document.elementFromPoint(touch.clientX, touch.clientY); // currently pointing on
  var currentTarget = findCurrentTarget(target); // super-positioned element the event is being applyed (projekt-cards, wahlliste, projektliste)

  // match corresponding drag-event
  var type = getDragEvent(e.type);
  if(type == null) return;
  if(type == "dragstart" || type == "drop"){
    // toggle ghost effect
    $(card).toggleClass("ghost");
  }
  if(type == "drop") {
    $(card).css({
      "top": "",
      "left": ""
    });
  }

  // set dragging Projekt-Card
  var data = new DataTransfer();
  data.setData("Text", card.id);
  data.setDragImage(e.target, touch.clientX, touch.clientY);

  // simulate dragging
  var simulatedEvent = new DragEvent(type, {
    "view": window,
    "bubbles": true,
    "cancelable": true,
    "screenX": touch.screenX,
    "screenY": touch.screenY,
    "clientX": touch.clientX,
    "clientY": touch.clientY,
    "dataTransfer": data,
    "currentTarget": currentTarget,
    "target": target
  });

  if(type == "drop") {
    // dispatch event on the target
    target.dispatchEvent(simulatedEvent);
  }
  else {
    // dispatch the dragging on the card
    card.dispatchEvent(simulatedEvent);
  }
  e.preventDefault();
}

// projektErstellung-Interface
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function createTag(day) {
  return $(`<div class="weekday">
  <label>` + day + `</label>
  <div class="form-group">
    <label for="">Vormittag</label>
    <textarea class="form-control" id="` + day + `Vor" placeholder="" required rows="3" name="` + day + `Vor"></textarea>
    <div class="invalid-feedback">
      Ungültige Eingabe
    </div>
  </div>

  <div class="form-check">
    <input class="form-check-input" type="checkbox" value="ja" checked id="` + day + `Mensa" name="` + day + `Mensa">
    <label class="form-check-label" for="defaultCheck1">
      Mensaessen möglich
    </label>
  </div>

  <div class="form-group">
    <label for="">Nachmittag</label>
    <textarea class="form-control" id="` + day + `Nach" placeholder="" required rows="3" name="` + day + `Nach"></textarea>
    <div class="invalid-feedback">
      Ungültige Eingabe
    </div>
  </div>

</div>`);
}

function disableWochentag(disabled, day) {
  if(disabled){
    $("#" + day + "Vor").prop("disabled", true);
    $("#" + day + "Vor").addClass("disabled");
    $("#" + day + "Mensa").prop("disabled", true);
    $("#" + day + "Mensa").addClass("disabled");
    $("#" + day + "Nach").prop("disabled", true);
    $("#" + day + "Nach").addClass("disabled");
  }
}

// creates the schedule input of each weekday
function createWeekdays() {
  // weekdays for the UI
  var wochentage = [
    "Montag",
    "Dienstag",
    "Mittwoch",
    "Donnerstag",
    "Freitag"
  ];
  // weekdays for the code
  var weekdays = [
    "mo",
    "di",
    "mi",
    "do",
    "fr"
  ];

  // generate the schedule for each day
  for (var i = 0; i < weekdays.length; i++) {
    $('#weekdays').append(createTag(weekdays[i]));
    disableWochentag(!config[wochentage[i]], weekdays[i]);
  }

  // disable the option to eat in the canteen on friday
  $("#frMensa").prop("checked", false);
  $("#frMensa").prop("disabled", true);
}

function setupProjektErstellung() {
  createWeekdays();
}

// JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    if(site != "projektErstellung") return;
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity() || !check()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

function check() {
  // check if the minimum is smaller or equal to the maximum
  var numberOfPlacesAreCorrect = parseInt($("#inputMinPlaetze").val()) <= parseInt($("#inputMaxPlaetze").val());
	if (!numberOfPlacesAreCorrect) {
		alert("Mindestanzahl der Teilnehmerplätze kann nicht größer sein als die die Maximalanzahl!");
	}

  // check if the minimum is smaller or equal to the maximum
	var levelsAreCorrect = parseInt($("#inputMinKlasse").val()) <= parseInt($("#inputMaxKlasse").val());
	if (!levelsAreCorrect) {
		alert("Mindeststufe der Klassenstufe kann nicht größer sein als die die Maximalstufe!");
	}
	return numberOfPlacesAreCorrect && levelsAreCorrect;
}

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
