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

function setupDashboard() {
	console.log("Current Configuration:");
  console.log(config);
  console.log(projekte);

	// formular setup
	createStageSelect(config["Stage"]);
}

// Wahl-Interface
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// x = anzahl Schüler / Anzahl Plätze
function calcAnzahlProjekte(students, platz, anzahlProjekte) {
	var x = students / platz;
	if(x < 0.6) return 4;
	var t = (75.4745 * Math.pow(x, 4) - 223.148 * Math.pow(x, 3) + 246.143 * Math.pow(x, 2) - 119.873 * x + 21.8101) * anzahlProjekte;
	console.log("Schüler: " + students + " / Plätze: " + platz + " = " + x);
	console.log("f(" + x + ") = " + t);
	return (t < 4 ? 4 : t);
}

function createWahlTable(projektAnzahl) {
	for(var i = 0; i < projektAnzahl; i++){
		$("#wahlliste tbody").append("<tr id='wahl" + i + "'><th scope='row'>" + (i + 1) + "</th><td></td></tr>");
	}
}

function getInput() {
	console.log("counting choosen projekts");
	var wahl = [];
	for(var i = 0; i < $("#wahlliste tbody>tr").length; i++) {
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
			$("#wahlliste>form").empty();
			for(var i = 0; i < wahl.length; i++) {
				$("#wahlliste>form").append($("<input type='hidden' name='wahl[" + i + "]'>").val($(wahl[i].children()[0]).val()));
				console.log("input angehängt");
			}
			$("#wahlliste>form").append($("<input type='hidden' name='action'>").val("wahl"));
			$("#wahlliste>form").submit();
		}));
	}
	return;
}

function setupWahl() {
	// html creation
	createWahlTable(calcAnzahlProjekte(50, 70, 20));

  function appendWahlliste(card) {
  	for(var i = $("#wahlliste tbody>tr").length - 1; i >= 0; i--) {
  		if($("#wahl" + i + ">td").children().length == 0) {
  			$("#wahl" + i + ">td").append($(card));
  		}
  	}
  }

  interact('body').dropzone({
    accept: '.card.projekt',
    overlap: 0.85,
    ondrop: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      $("#projektliste").append($(draggableElement));
    }
  });
  interact('#wahlliste').dropzone({
    accept: '.card.projekt',
    overlap: 0.3,
    ondragenter: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      dropzoneElement.classList.add('drop-active');
    },
    ondragleave: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      dropzoneElement.classList.remove('drop-active');
    },
    ondrop: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      appendWahlliste(draggableElement);
      dropzoneElement.classList.remove('drop-active');
    }
  });
  // -- Projekt-Cards
  interact('.card.projekt').dropzone({
    accept: '.card.projekt',
    overlap: 0.2,
    ondragenter: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      dropzoneElement.classList.add('drag-over');
    },
    ondragleave: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      dropzoneElement.classList.remove('drag-over');
    },
    ondrop: function (event) {
      var draggableElement = event.relatedTarget,
          dropzoneElement = event.target;
      if (dropzoneElement.parentNode == document.querySelector('#projektliste')) {
        dropzoneElement.parentNode.insertBefore(draggableElement, dropzoneElement);
      }
      // tbody>tr>td>card
      else if (dropzoneElement.parentNode.parentNode.parentNode == document.querySelector('#wahlliste tbody')) {
        if (draggableElement.parentNode.parentNode.parentNode == document.querySelector('#wahlliste tbody')) {
          draggableElement.parentNode.appendChild(dropzoneElement);
          appendWahlliste(draggableElement);
        }
        else {
          $("#projektliste").append($(dropzoneElement));
          appendWahlliste(draggableElement);
        }
      }
      dropzoneElement.classList.remove('drag-over');
    }
  }).draggable({
    autoScroll: false,
    onmove: function (event) {
      var target = event.target,
        // keep the dragged position in the data-x/data-y attributes
        x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
        y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

      target.classList.add('dragged');
      // translate the element
      target.style.webkitTransform =
      target.style.transform =
        'translate(' + x + 'px, ' + y + 'px)';

      // update the posiion attributes
      target.setAttribute('data-x', x);
      target.setAttribute('data-y', y);
    },
    onend: function (event) {
      var target = event.target;

      target.classList.remove('dragged');
      // translate the element
      target.style.webkitTransform =
      target.style.transform =
        'translate(0px, 0px)';

      // update the posiion attributes
      target.removeAttribute('data-x');
      target.removeAttribute('data-y');
    }
  });
}
