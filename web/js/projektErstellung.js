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

window.onload = setupProjektErstellung;
