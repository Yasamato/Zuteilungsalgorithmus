// super-function
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
window.onload = function() {
  console.log("Page: " + site);
  switch(site) {
    case "wahl": return setupWahl();
    case "dashboard": return setupDashboard();
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
					<button type="button" class="btn btn-danger" onclick="window.location.href = '?site=edit&projekt=` + p.id + `';">Bearbeiten</button>
					<button type="button" class="btn btn-secondary" onclick="javascript: window.open('printPDF.php?print=projekt&projekt=` + p.id + `');">Drucken</button>

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
	for (var i = 0; i < 5; i++) {
		if (i == config["Stage"]) {
			continue;
		}
		$("#stageSelect").append($(stages[i]));
	}
}

function createDashboardStudentsTable(students) {
  console.log("Creating: schuelerTable");
  console.log(students);
  $.each(students, function(index, student){
    var html = `
    <tr>
      <td>` + student["stufe"] + `</td>
      <td>` + student["klasse"] + `</td>
      <td>` + student["vorname"] + `</td>
      <td>` + student["nachname"] + `</td>
      <td>
        <ol>`;
    for (var i = 0; i < student["wahl"].length; i++) {
      html += `
          <li><a href="#" onclick="console.log(window.schueler); showProjektInfoModal(window.schueler['` + index + `']['wahl'][` + i + `]);">` + student["wahl"][i]["name"] + `</a></li>`;
    }
    html += `
      </ol>
    </tr>`;
    $("#schuelerTable>tbody").append($(html));
  });
}

function setupDashboard() {
	console.log("Current Configuration:");
  console.log(config);
  console.log(projekte);

	// formular setup
	createStageSelect(config["Stage"]);

  // Erstellen der Projekte- und Schüler-Tabellen
  createDashboardStudentsTable(window.schueler);
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

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
