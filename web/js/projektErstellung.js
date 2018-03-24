// JavaScript Magie
function replaceShit(string) {
  document.getElementById(string).value = document.getElementById(string).value.replace("'", "\'");
}

function check() {
  replaceShit("inputProjektname");
  replaceShit("inputBeschreibung");
  replaceShit("inputBetreuer");

	if (parseInt(document.getElementById("minPlatz").value) > parseInt(document.getElementById("maxPlatz").value)) {
		alert("Mindestanzahl der Teilnehmerplätze kann nicht größer sein als die die Maximalanzahl");
    return false;
	}
	if (parseInt(document.getElementById("minKlasse").value) > parseInt(document.getElementById("maxKlasse").value)) {
		alert("Mindeststufe der Klassenstufe kann nicht größer sein als die die Maximalstufe");
    return false;
	}
	return true;
}

function createWeekdays() {
  var wochentage = [
    "Montag",
    "Dienstag",
    "Mittwoch",
    "Donnerstag",
    "Freitag"
  ];
  var weekdays = [
    "mo",
    "di",
    "mi",
    "do",
    "fr"
  ];
  for (var i = 0; i < wochentage.length; i++) {
      document.getElementById('weekdays').innerHTML += `<div class="weekday">
        <label>`+ wochentage[i] + `</label>
        <div class="form-group">
          <label for="">Vormittag</label>
          <textarea class="form-control" id="weekday` + weekdays[i] + `Forenoon" placeholder="" required rows="3" name="` + weekdays[i] + `Vor"></textarea>
          <div class="invalid-feedback">
          Ungültige Eingabe
        </div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="true" checked id="checkboxFood` + weekdays[i] + `" name="` + weekdays[i] + `Mensa">
          <label class="form-check-label" for="defaultCheck1">
          Mensaessen möglich
          </label>
        </div>

        <div class="form-group">
          <label for="">Nachmittag</label>
          <textarea class="form-control" id="weekday` + weekdays[i] + `Afternoon" placeholder="" required rows="3" name="` + weekdays[i] + `Nach"></textarea>
          <div class="invalid-feedback">
          Ungültige Eingabe
          </div>
        </div>

      </div>`;
  }
  document.getElementById("frMensa").checked = false;
  document.getElementById("frMensa").disabled = true;
}

window.onload = createWeekdays;

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
