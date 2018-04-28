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
