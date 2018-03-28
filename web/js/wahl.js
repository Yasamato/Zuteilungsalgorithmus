var colors = [
	"primary",
	"success",
	"dark",
];

function logout(){
	$("#logout").submit();
}

function getInput(){
	console.log("counting choosen projekts");
	var wahl = [];
	for(var i = 0; i < $(".wahlliste tbody>tr").length; i++){
		if($("#wahl" + i + ">td").children().length == 0){
			if($(".btn-group").children().length > 2){
				$($(".btn-group").children()[2]).remove();
			}
			return;
		}
		else{
			wahl.push($($("#wahl" + i + ">td").children()[0]));
		}
	}
	if($(".btn-group").children().length < 3){
		$(".btn-group").append($("<button class='btn btn-success' name='action' value='wahl'>Wahl abschicken</button>").on("click", function(e){
			$(".wahlliste>form").empty();
			for(var i = 0; i < wahl.length; i++){
				$(".wahlliste>form").append($("<input type='hidden' name='wahl[" + i + "]'>").val($(wahl[i].children()[0]).val()));
				console.log("input angehängt");
			}
			$(".wahlliste>form").append($("<input type='hidden' name='action'>").val("wahl"));
			$(".wahlliste>form").submit();
		}));
	}
	return;
}




// x = anzahl Schüler / Anzahl Plätze
function calcAnzahlProjekte(students, platz, anzahlProjekte){
	var x = students / platz;
	if(x < 0.6) return 4;
	var t = (75.4745 * Math.pow(x, 4) - 223.148 * Math.pow(x, 3) + 246.143 * Math.pow(x, 2) - 119.873 * x + 21.8101) * anzahlProjekte;
	console.log("Schüler: " + students + " / Plätze: " + platz + " = " + x);
	console.log("f(" + x + ") = " + t);
	return (t < 4 ? 4 : t);
}


//--------------------------------------------------------
// Drag n Drop
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
	if(!$(ele1).hasClass("card")){
		ele1 = $(ele1).find("div")[0];
		console.log("ele1: no card");
	}
	if(!$(ele2).hasClass("card")){
		ele2 = $(ele2).find("div")[0];
		console.log("ele2: no card");
	}

	// vertausche bzw. setze ein
	if(ele1 == ele2){
		console.log("mit sich selbst vertauscht....");
		return;
	}
	if(iEle1 > -1 && iEle2 > -1){
		if(typeof ele2 !== "undefined"){
			$("#wahl" + iEle1).children()[1].append(ele2);
		}
		if(typeof ele1 !== "undefined"){
			$("#wahl" + iEle2).children()[1].append(ele1);
		}
	}
	else if(iEle1 > -1){
		$("#wahl" + iEle1).children()[1].append(ele2);
		if(typeof ele1 !== "undefined"){
			$(".projektliste>div").append(ele1);
		}
	}
	else if(iEle2 > -1){
		$("#wahl" + iEle2).children()[1].append(ele1);
		if(typeof ele2 !== "undefined"){
			$(".projektliste>div").append(ele2);
		}
	}
}

function createProjektCard(e){
	$(".projektliste>div").append($(`
		<div class="card list-group-item-` + colors[Math.floor(Math.random() *  colors.length)] + ` projekt-card" id="drag` + e.projektId + `">
			<input  type="hidden"value="` + e.projektId + `">
			<div class="card-body">
				<h5 class="card-title">` + e.name + `</h5>
				<a href="#" class="btn btn-` + colors[Math.floor(Math.random() *  colors.length)] + `" data-toggle="modal" data-target="#modal` + e.projektId + `">Info</a>
			</div>
		</div>`));
}

function createInfoModal(e){
	$(".modalHolder").append($(`
	<!-- Modal -->
	<div class="modal fade" id="modal` + e.projektId + `" tabindex="-1" role="dialog" aria-labelledby="modalLabel` + e.projektId + `" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="modalLabel` + e.projektId + `">` + e.name + `</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>` + e.beschreibung + `</p>
					<p><b>Betreuer:</b> ` + e.betreuer + `</p>
					<p><b>Klassenstufe:</b> ` + e.minKlasse + `-` + e.maxKlasse + `</p>
					<p><b>Teilnehmerzahl:</b> ` + e.minTeilnehmer + `-` + e.maxTeilnehmer + `</p>
					<p><b>Kosten/Sonstiges:</b> ` + e.sonstiges + `</p>
					<p><b>Vorraussetungen:</b> ` + e.vorraussetzungen + `</p>
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
									<td>` + e.moVor + `</td>
									<td>` + e.diVor + `</td>
									<td>` + e.miVor + `</td>
									<td>` + e.doVor + `</td>
									<td>` + e.frVor + `</td>
								</tr>
								<tr>
									<th scope="row">Mensa-Essen</th>
									<td>` + e.moMensa + `</td>
									<td>` + e.diMensa + `</td>
									<td>` + e.miMensa + `</td>
									<td>` + e.doMensa + `</td>
									<td>Nein</td>
								</tr>
								<tr>
									<th scope="row">Nachmittag</th>
									<td>` + e.moNach + `</td>
									<td>` + e.diNach + `</td>
									<td>` + e.miNach + `</td>
									<td>` + e.doNach + `</td>
									<td>` + e.frNach + `</td>
								</tr>
							</tbody>
						</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>`));
}

function ondragover(e){
	e.originalEvent.stopPropagation();
	e.originalEvent.preventDefault();
}

function dropCard(e){
	e.originalEvent.stopPropagation();
	e.originalEvent.preventDefault();
	var el = document.getElementById(e.originalEvent.dataTransfer.getData('Text'));
	console.log("dropped element on card");
	console.log(e.originalEvent.currentTarget);
	if($(".wahlliste").has(e.originalEvent.currentTarget).length){
		console.log("in Wahlliste");
		switchInTable(el, e.originalEvent.currentTarget);
	}
	else{
		console.log("in Projektliste");
		$(e.originalEvent.currentTarget).before(el);
	}
	getInput();
}

function cardDragstart(e){
	e.originalEvent.stopPropagation();
	if(e.originalEvent.dataTransfer){
		e.originalEvent.dataTransfer.setData("Text", this.id);
	}
	else{
		console.log("---------------------------------------------------------");
		console.log("WARUMM?? gibt es kein dataTransfer?!");
	}
}

function addCardListener(card){
	//$(this).draggable();
	$(this).on("touchstart touchmove touchend", touchHandler);
	$(this).attr("draggable", "true");
	$(this).on("dragstart", cardDragstart);
	//$(this).on("touchstart", cardDragstart);
	$(this).on("dragover", ondragover);
	//$(this).on("touchmove", ondragover);
	$(this).on("drop", dropCard);
	//$(this).on("touchend", dropCard);
}

function dropWahlliste(e){
	e.originalEvent.preventDefault();
	e.originalEvent.stopPropagation();
	var el = document.getElementById(e.originalEvent.dataTransfer.getData('Text'));
	console.log(el);
	console.log("dropped element on wahlliste");
	console.log(e.originalEvent.currentTarget);
	for(var i = $(".wahlliste tbody>tr").length - 1; i >= 0; i--){
		if($("#wahl" + i + ">td").children().length == 0){
			$("#wahl" + i + ">td").append(el);
		}
	}
	getInput();
}

function addTableListener(row){
	$(this).on("drop", function(e){
		e.originalEvent.preventDefault();
		e.originalEvent.stopPropagation();
		var el = document.getElementById(e.originalEvent.dataTransfer.getData('Text'));
		console.log("dropped element on tabelle");
		console.log(e.originalEvent.currentTarget);
		switchInTable(el, e.originalEvent.currentTarget);
		getInput();
	});
	$(this).on("dragover", function(e){
		e.originalEvent.preventDefault();
		e.originalEvent.stopPropagation();
	});
}

function dropProjektliste(e){
	e.originalEvent.preventDefault();
	e.originalEvent.stopPropagation();
	var el = document.getElementById(e.originalEvent.dataTransfer.getData('Text'));
	console.log("dropped element on projektliste");
	console.log(e.originalEvent.currentTarget);
	$(".projektliste>div").append(el);
	getInput();
}

// setup
window.onload = function(){
	var projektAnzahl = calcAnzahlProjekte(50, 70, 20);
	for(var i = 0; i < projektAnzahl; i++){
		$(".wahlliste tbody").append("<tr id='wahl" + i + "'><th scope='row'>" + (i + 1) + "</th><td></td></tr>");
	}

	projekte.forEach(function(e){
		createProjektCard(e);
		createInfoModal(e);
	});

	// Die Projekte selbst
	$(".card").each(addCardListener);

	// Wahlliste (rechts)
	$(".wahlliste").on("drop", dropWahlliste);
	$(".wahlliste").on("dragover", ondragover);
	// tabelle rechts zum reinziehen
	$(".wahlliste tbody>tr").each(addTableListener);

	// Projektliste (links)
	$(".projektliste").on("drop", dropProjektliste);
	$(".projektliste").on("dragover", ondragover);
}
