// Wahl-Interface
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function getInput() {
	console.log("counting choosen projekts");
	for (var i = 0; i < $("#wahlliste tbody>tr").length; i++) {
		if ($("#wahlliste tbody>tr")[i].children[1].children.length == 0) {
      if ($(".btn-group").children().length > 1) {
			     $($(".btn-group").children()[1]).remove();
      }
      return;
		}
	}
	if ($(".btn-group").children().length < 2) {
		$(".btn-group").append($("<button class='btn btn-success' name='action' value='wahl'>Wahl abschicken</button>").on("click", function(e){
			$("#wahlliste>form").empty();
      $("#wahlliste tbody>tr").each(function (index, wahl) {
				$("#wahlliste>form").append($("<input type='hidden' name='wahl[" + index + "]'>").val(wahl.children[1].children[0].children[0].value));
				console.log("Wahl Nr. " + index + " angehängt");
      })
			$("#wahlliste>form").append($("<input type='hidden' name='action'>").val("wahl"));
			$("#wahlliste>form").submit();
		}));
	}
	return;
}

function appendWahlliste(card) {
  $($("#wahlliste tbody>tr").get().reverse()).each(function (index, value) {
		if (value.children[1].children.length == 0) {
			$(value.children[1]).append($(card));
		}
  });
}

function addProjektListener() {
	interact('.card.projekt').dropzone({
	  accept: '.card.projekt',
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
					let dropParent = dropzoneElement.parentNode;
	        draggableElement.parentNode.appendChild(dropzoneElement);
					$(dropParent).append($(draggableElement));
	      }
	      else {
					$(dropzoneElement.parentNode).append($(draggableElement));
	        $("#projektliste").append($(dropzoneElement));
	      }
	    }
	    dropzoneElement.classList.remove('drag-over');
	    getInput();
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

	    // reset translate
	    target.style.webkitTransform =
	    target.style.transform =
	      'translate(0px, 0px)';
	    target.removeAttribute('data-x');
	    target.removeAttribute('data-y');
	  }
	});
}

window.onload = function() {
	/* Doku hier: http://interactjs.io/ */
	interact('body').dropzone({
	  accept: '.card.projekt',
	  ondrop: function (event) {
	    var draggableElement = event.relatedTarget,
	      dropzoneElement = event.target;
	    if (draggableElement.parentNode != document.querySelector('#projektliste')) {
	      $("#projektliste").append($(draggableElement));
	    }
	    getInput();
	  }
	});

	interact('#wahlliste').dropzone({
	  accept: '.card.projekt',
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
	    getInput();
	  }
	});

	interact('#wahlliste tbody td').dropzone({
	  accept: '.card.projekt',
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
			if (dropzoneElement.childElementCount > 0) {
				if (draggableElement.parentNode.parentNode.parentNode == document.querySelector('#wahlliste tbody')) {
					$(draggableElement.parentNode).append($(dropzoneElement.children[0]));
				}
				else {
					$("#projektliste").append($(dropzoneElement.children[0]));
				}
			}
      $(dropzoneElement).append($(draggableElement));
	    dropzoneElement.classList.remove('drop-active');
	    getInput();
	  }
	});

	// -- Projekt-Cards
	addProjektListener();

	checkUp();
	var autoUpdate = setInterval(checkUp, 2000);
	$("#projektlisteSearch").keyup(debounce(projekteTableSearch, 300));
}

function checkUp() {
	$.get("ping.php", function(data, status) {
		if (status = "success") {
			console.log("Check-Up erhalten, wende Daten-Update an");
			data = JSON.parse(data);
			updateUpdate(data["updateRunning"]);

			if (!window.config) {
				window.config = data["config"];
			}
			else if (JSON.stringify(window.config) != JSON.stringify(data["config"])) {
				console.log("config geupdated");
				window.config = data["config"];
			}

			if (!window.vorherigeWahl) {
				window.vorherigeWahl = data["vorherigeWahl"];
			}
			else if (JSON.stringify(window.vorherigeWahl) != JSON.stringify(data["vorherigeWahl"])) {
				if (data["vorherigeWahl"] != "false") {
					alert("Es wurde auf einem anderen Gerät deine Wahl verändert.");
					window.location.href = "?";
				}
				else {
					alert("Deine Wahl wurde durch das Sytem automatisch zurückgesetzt. Bitte speichere deine Wahl erneut.");
				}
			}

			if (!window.projekte) {
				window.projekte = data["projekte"];
			}
			else if (JSON.stringify(window.projekte) != JSON.stringify(data["projekte"])) {
				console.log("Projekte geupdated");
				updateProjekte(data);
				window.projekte = data["projekte"];
			}
		}
		else {
			console.log("JS-Data Update failed!!");
		}
	});
}

function updateProjekte(data) {
	var neueProjekte = [], entfernteProjekte = [], msg = "";

	// Projekte, die verändert wurden
	for (var p in window.projekte) {
		var found = false;
		for (var i in data["projekte"]) {
			if (data["projekte"][i]["id"] == window.projekte[p]["id"]) {
				found = true;
				break;
			}
		}
		if (!found) {
			$(".card.projekt").each(function(index) {
				if ($(this).find("input").val() == window.projekte[p]["id"]) {
					$(this).remove();
				}
			});
			entfernteProjekte.push(window.projekte[p]);
		}
	}
	if (entfernteProjekte) {
		msg += "Es wurde" + (entfernteProjekte.length > 1 ? "" : "n") + " " + entfernteProjekte.length + " Projekt" + (entfernteProjekte.length > 1 ? "e" : "") + " entfernt:\n";
		for (var i in entfernteProjekte) {
			msg += "- " + entfernteProjekte["name"] + "\n";
		}
		msg += "\n";
	}

	// neue Projekte, welche hinzugefügt wurden
	for (var p in data["projekte"]) {
		var found = false;
		for (var i in window.projekte) {
			if (data["projekte"][p]["id"] == window.projekte[i]["id"]) {
				found = true;
				break;
			}
		}
		if (!found) {
			addProjektListener();
			$(`
			<div class="card projekt text-black shadow list-group-item-dark">
				<input type="hidden" value="` + data["projekte"][p]["id"] + `">
				<div class="card-body">
					<h5>` + data["projekte"][p]["name"] + `</h5>
					<a href="javascript: ;" onclick="javascript: showProjektInfoModal('` + data["projekte"][p]["id"] + `');" class="btn btn-primary">Info</a>
				</div>
			</div>`).appendTo("#projektliste");
			neueProjekte.push(data["projekte"][p]);
		}

		$(".card.projekt").each(function(index) {
			if ($(this).find("input").val() == data["projekte"][p]["id"]) {
				if ($(this).find("h5").html() != data["projekte"][p]["name"]){
					$(this).find("h5").html(data["projekte"][p]["name"]);
				}
			}
		});
	}
	if (neueProjekte) {
		msg += "Es wurde" + (neueProjekte.length > 1 ? "" : "n") + " " + neueProjekte.length + " Projekt" + (neueProjekte.length > 1 ? "e" : "") + " hinzugefügt:\n";
		for (var i in neueProjekte) {
			msg += "- " + neueProjekte["name"] + "\n";
		}
		msg += "\n";
	}

	if (msg) {
		alert(msg);
	}
}

function projekteTableSearch() {
  return search(this, $('#projektliste .card.projekt'));
}
