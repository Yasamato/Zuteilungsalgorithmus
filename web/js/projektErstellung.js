// JavaScript Magie

window.onload = createWeekdays;

function replaceShit(string) {
  document.getElementById(string).value = document.getElementById(string).value.replace("'", "\'");
}


function check() {

    replaceShit("inputProjektname");
    replaceShit("inputBeschreibung");
    replaceShit("inputBetreuer");

	var checkPlaetze = parseInt(document.getElementById("inputMinPlaetze").value) <= parseInt(document.getElementById("inputMaxPlaetze").value);
	if (!checkPlaetze) {
		alert("Mindestanzahl der Teilnehmerplätze kann nicht größer sein als die die Maximalanzahl");
	}
	var checkKlasse = parseInt(document.getElementById("inputMinKlasse").value) <= parseInt(document.getElementById("inputMaxKlasse").value);
	if (!checkKlasse) {
		alert("Mindeststufe der Klassenstufe kann nicht größer sein als die die Maximalstufe");
		}	
	return checkPlaetze && checkKlasse;
}
// Example starter JavaScript for disabling form submissions if there are invalid fields
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

function createWeekdays() {
  var wochentage = [
    "Montag",
    "Dienstag",
    "Mittwoch",
    "Donnerstag",
    "Freitag"
  ];
  var weekdays = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday"
  ];
  for (var i = 0; i < weekdays.length; i++) {
      document.getElementById('weekdays').innerHTML += `<div class="weekday">
        <label>`+ wochentage[i] + `</label>
        <div class="form-group">
          <label for="">Vormittag</label>
          <textarea class="form-control" id="weekday` + weekdays[i] + `Forenoon" placeholder="" required rows="3" name="weekday` + weekdays[i] + `Forenoon"></textarea>
          <div class="invalid-feedback">
          Ungültige Eingabe
        </div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="true" checked id="checkboxFood` + weekdays[i] + `" name="checkboxFood` + weekdays[i] + `">
          <label class="form-check-label" for="defaultCheck1">
          Mensaessen möglich
          </label>
        </div>

        <div class="form-group">
          <label for="">Nachmittag</label>
          <textarea class="form-control" id="weekday` + weekdays[i] + `Afternoon" placeholder="" required rows="3" name="weekday` + weekdays[i] + `Afternoon"></textarea>
          <div class="invalid-feedback">
          Ungültige Eingabe
          </div>
        </div>

      </div>`;
  }
  document.getElementById("checkboxFoodFriday").checked = false;
  document.getElementById("checkboxFoodFriday").disabled = true;
}