<?php
if (!isLogin() ||
  ($_SESSION['benutzer']['typ'] != "admin" && $_SESSION['benutzer']['typ'] != "teachers") ||
  $_SESSION['benutzer']['typ'] == "teachers" && $config["Stage"] >= 3) {
  die("Zugriff verweigert");
}

$projekt = getProjektInfo($_GET["projekt"]);
?>

<!-- overflow field with blured background -->
<div id="bg-blur" class="container">

	<button type="button" class="btn btn-primary" onclick="window.location.href = '?';">
		Zurück
	</button>
	<!-- Formularüberschrift -->
	<div align="center">
		<h1>Projekt-Einschreibe-Formular</h1>
		<p>Hier können sie ihre Projekte eintragen
			<br> Bitte achten sie auf die Richtigkeit ihrer Eingaben, da sie im Nachhinein <b class="text-danger">NICHT</b> mehr geändert werden können</p>
		<p></p>
	</div>

	<!-- Hier ist das Formular -->
	<form method="post">

		<!-- erste Zeile -->
		<div class="form-row">

			<!-- Eingabefeld für Projektname -->
			<div class="col-sm">
				<label for="inputProjektname">Projektname</label>
				<input type="text" class="form-control" id="inputProjektname" placeholder="Projektname" required name="pName" autofocus value="<?php echo $projekt["name"]; ?>">
				<small class="text-muted">
					Bitte geben Sie einen geeigneten Projektnamen an
				</small>
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>

			<!-- Eingabefeld für die Betreuer -->
			<div class="col-sm">
				<label for="inputBetreuer">Betreuer</label>
				<input type="text" class="form-control" id="inputBetreuer" placeholder="Betreuer" required name="betreuer" value="<?php echo $projekt["betreuer"]; ?>">
				<small class="text-muted">
					Bitte geben Sie die Betreuer getrennt durch Kommata an
				</small>
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>
		</div>
    <br>


		<!-- zweite Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für die Beschreibung -->
			<div class="col">
				<label for="inputBeschreibung">Beschreibung</label>
				<textarea class="form-control" id="inputBeschreibung" placeholder="Beschreibung" required rows="4" name="beschreibung"><?php echo newlineBack($projekt["beschreibung"]); ?></textarea>
				<small class="text-muted">
					Bitte geben Sie eine geeignete Beschreibung des Projektes an.
				</small>
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>
		</div>
    <br>


		<!-- dritte Zeile -->
		<div class="form-row">
      <div class="col-sm">
  			<!-- Eingabefeld für die min. Klassenstufe -->
  			<div class="col">
  				<label for="inputMinKlasse">min. Klassenstufe</label>
  				<input type="number" class="form-control" id="inputMinKlasse" placeholder="5" required min="5" max="12" name="minKlasse" value="<?php echo $projekt["minKlasse"]; ?>">
  				<div class="invalid-feedback">
  					Ungültige Eingabe
  				</div>
  			</div>

  			<!-- Eingabefeld für die max. Klassenstufe -->
  			<div class="col">
  				<label for="inputMaxKlasse">max. Klassenstufe</label>
  				<input type="number" class="form-control" id="inputMaxKlasse" placeholder="12" required min="5" max="12" name="maxKlasse" value="<?php echo $projekt["maxKlasse"]; ?>">
  				<div class="invalid-feedback">
  					Ungültige Eingabe
  				</div>
  			</div>
      </div>

      <div class="col-sm">
  			<!-- Eingabefeld für die min. Teilnehmeranzahl -->
  			<div class="col">
  				<label for="inputMinPlaetze">min. Anzahl Teilnehmer</label>
  				<input type="number" class="form-control" id="inputMinPlaetze" placeholder="10" required min="1" name="minPlatz" value="<?php echo $projekt["minPlatz"]; ?>">
  				<div class="invalid-feedback">
  					Ungültige Eingabe
  				</div>
  			</div>

  			<!-- Eingabefeld für die max. Teilnehmeranzahl -->
  			<div class="col">
  				<label for="inputMaxPlaetze">max. Anzahl Teilnehmer</label>
  				<input type="number" class="form-control" id="inputMaxPlaetze" placeholder="30" required min="1" name="maxPlatz" value="<?php echo $projekt["maxPlatz"]; ?>">
  				<div class="invalid-feedback">
  					Ungültige Eingabe
  				</div>
  			</div>
      </div>
		</div>
    <small class="text-muted">
      Bitte die begrenzenden Rahmenbedingungen eintragen.
      Hier legen Sie fest für welche Klassenstufen sich das Projekt eignet sowie ihre mindestens benötigte Teilnehmerzahl neben der maximal möglichen.
    </small>
    <br>
    <br>


		<!-- vierte Zeile -->
		<div class="form-row">
      <!-- Eingabefeld für den Raumwunsch -->
      <div class="col-md-auto">
        <label for="inputRaumwunsch">Raumwunsch</label>
      </div>
      <div class="col-md">
        <input type="text" class="form-control" id="inputRaumwunsch" placeholder="B101/Sporthalle" name="raum" value="<?php echo $projekt["raum"]; ?>">
        <div class="invalid-feedback">
          Ungültige Eingabe
        </div>
      </div>
    </div>
    <small class="text-muted">
      Falls besondere Räumlichkeiten erforderlich sind, bitte diese angeben damit diese von der Schulleitung organisiert werden kann.
    </small>
    <br>
    <br>


		<!-- fünfte Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für Kosten/Sontiges-->
			<div class="col-md-auto">
				<label for="inputSonstiges">Kosten/Sonstiges</label>
      </div>
			<div class="col-md">
				<input type="text" class="form-control" id="inputSonstiges" placeholder="Kosten/Sonstiges" name="sonstiges" value="<?php echo $projekt["sonstiges"]; ?>">
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>
		</div>
    <small class="text-muted">
      Falls Kosten anfallen oder irgendetwas anderes mitzuteilen ist, bitte hier eintragen.
    </small>
    <br>
    <br>


		<!-- sechste Zeile -->
		<div class="form-row">
			<div class="col-md-auto">
				<label for="inputBesVoraus">Besondere Voraussetzungen</label>
      </div>
			<div class="col-md">
				<input type="text" class="form-control" id="inputBesVoraus" placeholder="Computerkenntnisse" name="vorraussetzungen" value="<?php echo $projekt["vorraussetzungen"]; ?>">
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>
		</div>
    <small class="text-muted">
      Bei Bedingungen zur Teilnahme diese bitte hier eintragen.
    </small>
    <br>
    <br>


		<!-- siebte Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für Materialwünsche -->
			<div class="col-md-auto">
				<label for="inputMaterial">Materialwünsche</label>
      </div>
			<div class="col-md">
				<input type="text" class="form-control" id="inputMaterial" placeholder="Beamer" name="material" value="<?php echo $projekt["material"]; ?>">
				<div class="invalid-feedback">
					Ungültige Eingabe
				</div>
			</div>
		</div>
    <small class="text-muted">
      Falls besondere Materialien mit der Schule organisiert werden müssen, diese bitte hier eintragen.
    </small>
    <br>
    <br>


		<!-- Überschrift für die Wochenplanung-->
		<h2 class="text-center">
			Projekt Tagesablaufplan
		</h2>

		<!-- Die einzelnen Tage -->
		<div class="form-row" id="weekdays">
			<!-- Montag -->
			<div class="col-md">
        <h4>Montag</h4>
			  <div class="form-group col">
			    <label>Vormittag</label>
			    <textarea class="form-control" id="moVor" placeholder="<?php echo $config["MontagVormittagHinweis"]; ?>" rows="3" name="moVor"<?php echo $config["MontagVormittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["moVor"]); ?></textarea>
			    <div class="invalid-feedback">
			      Ungültige Eingabe
			    </div>
			  </div>

			  <div class="form-check col">
			    <input class="form-check-input" type="checkbox" id="moMensa" name="moMensa"<?php echo $projekt["moMensa"] == "true" ? " checked" : ""; ?>>
			    <label class="form-check-label">
			      Mensaessen möglich
			    </label>
			  </div>

			  <div class="form-group col">
			    <label>Nachmittag</label>
			    <textarea class="form-control" id="moNach" placeholder="<?php echo $config["MontagNachmittagHinweis"]; ?>" rows="3" name="moNach"<?php echo $config["MontagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["moNach"]); ?></textarea>
			    <div class="invalid-feedback">
			      Ungültige Eingabe
			    </div>
			  </div>
			</div>

      <!-- Dienstag -->
      <div class="col-md">
        <h4>Dienstag</h4>
        <div class="form-group col">
          <label>Vormittag</label>
          <textarea class="form-control" id="diVor" placeholder="<?php echo $config["DienstagVormittagHinweis"]; ?>" rows="3" name="diVor"<?php echo $config["DienstagVormittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["diVor"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>

        <div class="form-check col">
          <input class="form-check-input" type="checkbox" id="diMensa" name="diMensa"<?php echo $projekt["diMensa"] == "true" ? " checked" : ""; ?>>
          <label class="form-check-label">
            Mensaessen möglich
          </label>
        </div>

        <div class="form-group col">
          <label>Nachmittag</label>
          <textarea class="form-control" id="diNach" placeholder="<?php echo $config["DienstagNachmittagHinweis"]; ?>" rows="3" name="diNach"<?php echo $config["DienstagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["diNach"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>
      </div>

      <div class="w-100 d-none d-md-block"></div>

      <!-- Mittwoch -->
      <div class="col-md">
        <h4>Mittwoch</h4>
        <div class="form-group col">
          <label>Vormittag</label>
          <textarea class="form-control" id="miVor" placeholder="<?php echo $config["MittwochVormittagHinweis"]; ?>" rows="3" name="miVor"<?php echo $config["MittwochVormittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["miVor"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>

        <div class="form-check col">
          <input class="form-check-input" type="checkbox" id="miMensa" name="miMensa"<?php echo $projekt["miMensa"] == "true" ? " checked" : ""; ?>>
          <label class="form-check-label">
            Mensaessen möglich
          </label>
        </div>

        <div class="form-group col">
          <label>Nachmittag</label>
          <textarea class="form-control" id="miNach" placeholder="<?php echo $config["MittwochNachmittagHinweis"]; ?>" rows="3" name="miNach"<?php echo $config["MittwochNachmittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["miNach"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>
      </div>

      <!-- Donnerstag -->
      <div class="col-md">
        <h4>Donnerstag</h4>
        <div class="form-group col">
          <label>Vormittag</label>
          <textarea class="form-control" id="doVor" placeholder="<?php echo $config["DonnerstagVormittagHinweis"]; ?>" rows="3" name="doVor"<?php echo $config["DonnerstagVormittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["doVor"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>

        <div class="form-check col">
          <input class="form-check-input" type="checkbox" id="doMensa" name="doMensa"<?php echo $projekt["doMensa"] == "true" ? " checked" : ""; ?>>
          <label class="form-check-label">
            Mensaessen möglich
          </label>
        </div>

        <div class="form-group col">
          <label>Nachmittag</label>
          <textarea class="form-control" id="doNach" placeholder="<?php echo $config["DonnerstagNachmittagHinweis"]; ?>" rows="3" name="doNach"<?php echo $config["DonnerstagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["doNach"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>
      </div>

      <div class="w-100 d-none d-md-block"></div>

      <!-- Freitag -->
      <div class="col-md">
        <h4>Freitag</h4>
        <div class="form-group col">
          <label>Vormittag</label>
          <textarea class="form-control" id="frVor" placeholder="<?php echo $config["FreitagVormittagHinweis"]; ?>" rows="3" name="frVor"<?php echo $config["FreitagVormittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["frVor"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>

        <div class="form-check col">
          <input class="form-check-input" type="checkbox" id="frMensa" name="frMensa"<?php //echo $projekt["frMensa"] == "true" ? " checked" : ""; ?> disabled>
          <label class="form-check-label">
            Mensaessen möglich
          </label>
        </div>

        <div class="form-group col">
          <label>Nachmittag</label>
          <textarea class="form-control" id="frNach" placeholder="<?php echo $config["FreitagNachmittagHinweis"]; ?>" rows="3" name="frNach"<?php echo $config["FreitagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo newlineBack($projekt["frNach"]); ?></textarea>
          <div class="invalid-feedback">
            Ungültige Eingabe
          </div>
        </div>
      </div>
		</div>

		<!-- Einreichen Knopf -->
		<div class="text-center">
			<div class="btn-group">
	      <button type="button" class="btn btn-danger" onclick="logout()">
					Abmelden
				</button>
				<button type="button" class="btn btn-primary" onclick="window.location.href = '?';">
					Zurück
				</button>
			</div>
			<button name="action" value="editProject" class="btn btn-success" type="submit">
				Änderungen speichern
			</button>
		</div>
	</form>
</div>
