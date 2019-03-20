
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
	interact('#wahlliste').draggable({
	  onmove: function (event) {
	    var target = event.target,
	      // keep the dragged position in the data-x/data-y attributes
	      y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

	    target.classList.add('dragged');
	    // translate the element
	    target.style.webkitTransform =
	    target.style.transform =
	      'translate(0px, ' + y + 'px)';

	    // update the posiion attributes
	    target.setAttribute('data-y', y);
	  }
	});
}
