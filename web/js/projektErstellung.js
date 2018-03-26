// JavaScript Magie

// create the UI when the page is loaded
window.onload = createWeekdays;

// check if the input is logical correct
function check() {
  // check if the minimum is smaller or equal to the maximum
  var numberOfPlacesAreCorrect = parseInt(document.getElementById("inputMinPlaetze").value) <= parseInt(document.getElementById("inputMaxPlaetze").value);
	if (!numberOfPlacesAreCorrect) {
		alert("Mindestanzahl der Teilnehmerplätze kann nicht größer sein als die die Maximalanzahl!");
	}
  // check if the minimum is smaller or equal to the maximum
	var levelsAreCorrect = parseInt(document.getElementById("inputMinKlasse").value) <= parseInt(document.getElementById("inputMaxKlasse").value);
	if (!levelsAreCorrect) {
		alert("Mindeststufe der Klassenstufe kann nicht größer sein als die die Maximalstufe!");
	}
	return numberOfPlacesAreCorrect && levelsAreCorrect;
}

// JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
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
  // yep thats definitly necessary ;) | rly? -.-
  var weekdays = [
    "mo",
    "di",
    "mi",
    "do",
    "fr"
  ];

  // generate the schedule for each day
  // it doesn't matter if it's necessary or not
  for (var i = 0; i < weekdays.length; i++) {
    $('#weekdays').append($(`<div class="weekday">
      <label>`+ wochentage[i] + `</label>
      <div class="form-group">
        <label for="">Vormittag</label>
        <textarea class="form-control" id="` + weekdays[i] + `Vor" placeholder="" required rows="3" name="` + weekdays[i] + `Vor"></textarea>
        <div class="invalid-feedback">
          Ungültige Eingabe
        </div>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="ja" checked id="` + weekdays[i] + `Mensa" name="` + weekdays[i] + `Mensa">
        <label class="form-check-label" for="defaultCheck1">
          Mensaessen möglich
        </label>
      </div>

      <div class="form-group">
        <label for="">Nachmittag</label>
        <textarea class="form-control" id="` + weekdays[i] + `Nach" placeholder="" required rows="3" name="` + weekdays[i] + `Nach"></textarea>
        <div class="invalid-feedback">
          Ungültige Eingabe
        </div>
      </div>

    </div>`));
    if(!config[wochentage[i]]){
      $("#" + weekdays[i] + "Vor").prop("disabled", true);
      $("#" + weekdays[i] + "Vor").addClass("disabled");
      $("#" + weekdays[i] + "Mensa").prop("disabled", true);
      $("#" + weekdays[i] + "Mensa").addClass("disabled");
      $("#" + weekdays[i] + "Nach").prop("disabled", true);
      $("#" + weekdays[i] + "Nach").addClass("disabled");
    }
  }
  // disable the option to eat in the canteen on friday
  $("#frMensa").prop("checked", false);
  $("#frMensa").prop("disabled", true);
}
