// a pain in the ass...............
function touchHandler(e) {
  if(e.changedTouches.length != 1){
    return;
  }
  var touch = e.changedTouches[0];
  var type = "";

  // find dragged card
  var card = null;
  for(i = 0; i < $("div.card").length; i++){
    if($($("div.card")[i]).has(e.target).length){
      card = $("div.card")[i];
      break;
    }
  }
  if(!card){
    return;
  }

  //apply ghost image
  $(card).css({
    "top": touch.clientY + 5,
    "left": touch.clientX + 5
  });

  // get target and currentTarget
  var target = document.elementFromPoint(touch.clientX, touch.clientY);
  console.log(target);
  var currentTarget = null;
  for(i = 0; i < projekte.length; i++){
    if($("#drag" + projekte.projektId) == $(target) || $("#drag" + projekte.projektId).has(target)){
      currentTarget = document.getElementById("#drag" + projekte.projektId);
    }
  }
  if(currentTarget == null){
    if($(".wahlliste") == $(target) || $(".wahlliste").has(target)){
      currentTarget = document.getElementById("wahlliste");
    }
    else{
      currentTarget = document.getElementById("projektliste");
    }
  }

  switch(e.type) {
    case "touchstart":
      type = "dragstart";
      $(card).toggleClass("ghost");
      break;
    case "touchmove":
      type = "dragover";
      break;
    case "touchend":
      type = "drop";
      $(card).toggleClass("ghost");
      $(card).css({
        "top": "",
        "left": ""
      });
      break;
    default:
      return;
  }

  var data = new DataTransfer();
  data.setData("Text", card.id);
  data.dropEffect = "move";
  data.setDragImage(e.target, touch.clientX, touch.clientY);
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

  if(type == "drop"){
    target.dispatchEvent(simulatedEvent);
  }
  else{
    card.dispatchEvent(simulatedEvent);
  }
  e.preventDefault();
}
