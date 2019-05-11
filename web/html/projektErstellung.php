<?php
if (!isLogin() || ($_SESSION['benutzer']['typ'] != "admin" && $_SESSION['benutzer']['typ'] != "teachers") ||
  $_SESSION['benutzer']['typ'] == "teachers" && ($config["Stage"] < 1 || $config["Stage"] != 1 && $_GET['site'] != "edit")) {
  die("Zugriff verweigert");
}

$projekt = null;
if (empty($_GET["projekt"]) && $_SESSION['benutzer']['typ'] == "teachers" && $config["Stage"] >= 3) {
  die("Zugriff verweigert");
}
elseif (!empty($_GET["projekt"])) {
  $projekt = getProjektInfo($projekte, $_GET["projekt"]);
}
?>
<div class="container">
  <div class="alert alert-danger d-none" role="alert" id="updateAlert">
    <div class="spinner-border text-primary m-2" role="status"></div>
  	Momentan wird ein Update durchgeführt, daher kann es zur Einschränkung aller Funktionen kommen.<br>
  	Die Seite wird automatisch aktualisiert, sobald das Update abgeschlossen ist, um die Änderungen zu übernehmen.<br>
  	Alle nicht gespeicherten Änderungen gehen dabei verloren!
  </div>
</div>

<!-- overflow field with blured background -->
<div id="bg-blur" class="container-fluid">

	<button type="button" class="btn btn-primary" onclick="window.location.href = '/';">
		Zurück
	</button>
	<!-- Formularüberschrift -->
	<div align="center">
		<h1><?php echo ($config["wahlTyp"] == "ag" ? "AG" : "Projekt"); ?>-Einschreibe-Formular</h1>
		<p>
      Hier können Sie ihr<?php echo ($config["wahlTyp"] == "ag" ? "e AG" : " Projekt"); ?> einreichen.
    </p>
	</div>

	<!-- Hier ist das Formular -->
	<form class="needs-validation" method="post" novalidate>

		<!-- erste Zeile -->
		<div class="form-row">

			<!-- Eingabefeld für Projektname -->
			<div class="col-md">
				<label><?php echo ($config["wahlTyp"] == "ag" ? "AG-N" : "Projektn"); ?>ame</label>
				<input type="text" class="form-control" placeholder="Beispielsweise: Mein<?php echo ($config["wahlTyp"] == "ag" ? "e Traum-AG" : " Traumprojekt"); ?>" required name="pName" autofocus value="<?php echo empty($_POST["pName"]) ? (empty($projekt["name"]) ? "" : $projekt["name"]) : $_POST["pName"]; ?>">
				<small class="text-muted">
					Bitte geben Sie einen geeigneten <?php echo ($config["wahlTyp"] == "ag" ? "AG-N" : "Projektn"); ?>amen an
				</small>
			</div>

			<!-- Eingabefeld für die Betreuer -->
			<div class="col-md">
				<label>Betreuer</label>
				<input type="text" class="form-control" placeholder="Beispielsweise: Hr. Mustermann, Fr. Mustermann" required name="betreuer" value="<?php echo empty($_POST["betreuer"]) ? (empty($projekt["betreuer"]) ? "" : $projekt["betreuer"]) : $_POST["betreuer"]; ?>">
				<small class="text-muted">
					Bitte geben Sie die Betreuer getrennt durch Kommata an
				</small>
			</div>
		</div>


		<!-- zweite Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für die Beschreibung -->
      <div class="col">
  			<label>Beschreibung</label>
  			<textarea class="form-control" placeholder="Beispielsweise:" required rows="12" name="beschreibung"><?php echo empty($_POST["beschreibung"]) ? (empty($projekt["beschreibung"]) ? "" : newlineBack($projekt["beschreibung"])) : $_POST["beschreibung"]; ?></textarea>
  			<small class="text-muted">
  				Bitte geben Sie eine geeignete Beschreibung de<?php echo ($config["wahlTyp"] == "ag" ? "r AG" : "s Projekts"); ?> an.
  			</small>
      </div>
		</div>


		<!-- dritte Zeile -->
		<div class="form-row">
      <div class="col-md-3 col-sm-6">
  			<!-- Eingabefeld für die min. Klassenstufe -->
				<label>min. Klassenstufe</label>
				<input type="number" class="form-control" placeholder="Bsp: 5" required min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="minKlasse" value="<?php echo empty($_POST["minKlasse"]) ? (empty($projekt["minKlasse"]) ? "" : $projekt["minKlasse"]) : $_POST["minKlasse"]; ?>">
      </div>

      <div class="col-md-3 col-sm-6">
  			<!-- Eingabefeld für die max. Klassenstufe -->
				<label>max. Klassenstufe</label>
				<input type="number" class="form-control" placeholder="Bsp: 12" required min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="maxKlasse" value="<?php echo empty($_POST["maxKlasse"]) ? (empty($projekt["maxKlasse"]) ? "" : $projekt["maxKlasse"]) : $_POST["maxKlasse"]; ?>">
      </div>

      <div class="col-md-3 col-sm-6">
  			<!-- Eingabefeld für die min. Teilnehmeranzahl -->
				<label>min. Anzahl Teilnehmer</label>
				<input type="number" class="form-control" placeholder="Bsp: 10" required min="1" max="50" name="minPlatz" value="<?php echo empty($_POST["minPlatz"]) ? (empty($projekt["minPlatz"]) ? "" : $projekt["minPlatz"]) : $_POST["minPlatz"]; ?>">
      </div>

      <div class="col-md-3 col-sm-6">
  			<!-- Eingabefeld für die max. Teilnehmeranzahl -->
				<label>max. Anzahl Teilnehmer</label>
				<input type="number" class="form-control" placeholder="Bsp: 30" required min="1" max="500" name="maxPlatz" value="<?php echo empty($_POST["maxPlatz"]) ? (empty($projekt["maxPlatz"]) ? "" : $projekt["maxPlatz"]) : $_POST["maxPlatz"]; ?>">
      </div>
		</div>
    <p>
      <small class="text-muted">
        Bitte die begrenzenden Rahmenbedingungen eintragen.
        Hier legen Sie fest für welche Klassenstufen sich <?php echo ($config["wahlTyp"] == "ag" ? "die AG" : "das Projekt"); ?> eignet, sowie ihre mindestens benötigte Teilnehmerzahl neben der maximal möglichen.
      </small>
    </p>

		<!-- vierte Zeile -->
		<div class="form-row">
      <!-- Eingabefeld für den Raumwunsch -->
      <label>Raumwunsch</label>
      <input type="text" class="form-control" placeholder="Beispielsweise: B101/Sporthalle" name="raum" value="<?php echo empty($_POST["raum"]) ? (empty($projekt["raum"]) ? "" : $projekt["raum"]) : $_POST["raum"]; ?>">
    </div>
    <p>
      <small class="text-muted">
        Falls besondere Räumlichkeiten erforderlich sind, bitte diese angeben damit diese von Orga.-Team organisiert werden kann.
      </small>
    </p>


		<!-- fünfte Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für Kosten/Sontiges-->
			<label>Kosten/Sonstiges</label>
			<input type="text" class="form-control" placeholder="Beispielsweise: Zugticketkosten von 10€" name="sonstiges" value="<?php echo empty($_POST["sonstiges"]) ? (empty($projekt["sonstiges"]) ? "" : $projekt["sonstiges"]) : $_POST["sonstiges"]; ?>">
		</div>
    <p>
      <small class="text-muted">
        Falls Kosten anfallen oder irgendetwas anderes mitzuteilen ist, bitte hier eintragen.
      </small>
    </p>


		<!-- sechste Zeile -->
		<div class="form-row">
			<label>Besondere Voraussetzungen</label>
			<input type="text" class="form-control" placeholder="Beispielsweise: Computerkenntnisse" name="vorraussetzungen" value="<?php echo empty($_POST["vorraussetzungen"]) ? (empty($projekt["vorraussetzungen"]) ? "" : $projekt["vorraussetzungen"]) : $_POST["vorraussetzungen"]; ?>">
		</div>
    <p>
      <small class="text-muted">
        Bei Bedingungen zur Teilnahme wie besondere Kenntnisse/Fähigkeiten, diese bitte hier eintragen.
      </small>
    </p>


		<!-- siebte Zeile -->
		<div class="form-row">
			<!-- Eingabefeld für Materialwünsche -->
			<label>Materialwünsche</label>
			<input type="text" class="form-control" placeholder="Beispielsweise: Beamer, Smart-board" name="material" value="<?php echo empty($_POST["material"]) ? (empty($projekt["material"]) ? "" : $projekt["material"]) : $_POST["material"]; ?>">
		</div>
    <p>
      <small class="text-muted">
        Falls besondere Materialien zusätzlich organisiert werden müssen, diese bitte hier eintragen.
      </small>
    </p>


    <?php if ($config["wahlTyp"] == "projektwoche") { ?>
		<!-- Überschrift für die Wochenplanung-->
		<h2 class="text-center">
			Projekt Tagesablaufplan
		</h2>

		<!-- Die einzelnen Tage -->
		<div class="form-row">
			<!-- Montag -->
			<div class="col-xl-4 col-lg-6 col-md-12">
        <h4>Montag</h4>
        <div class="form-group form-check">
          <label class="form-check-label">
            Mensaessen möglich
          </label>
          <input class="form-check-input" type="checkbox" name="moMensa"<?php echo !empty($projekt["moMensa"]) && $projekt["moMensa"] == "true" || empty($projekt) ? " checked" : ""; ?>>
        </div>

        <div class="form-row">
          <div class="form-group col-sm-6 col-xs-12">
  			    <label>Vormittag</label>
  			    <textarea class="form-control" placeholder="<?php echo $config["MontagVormittagHinweis"]; ?>" rows="3" name="moVor"<?php echo $config["MontagVormittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["moVor"]) ? (empty($projekt["moVor"]) ? "" : newlineBack($projekt["moVor"])) : $_POST["moVor"]; ?></textarea>
  			    <div class="invalid-feedback">
  			      Ungültige Eingabe
  			    </div>
  			  </div>

          <div class="form-group col-sm-6 col-xs-12">
  			    <label>Nachmittag</label>
  			    <textarea class="form-control" placeholder="<?php echo $config["MontagNachmittagHinweis"]; ?>" rows="3" name="moNach"<?php echo $config["MontagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["moNach"]) ? (empty($projekt["moNach"]) ? "" : newlineBack($projekt["moNach"])) : $_POST["moNach"]; ?></textarea>
  			  </div>
  			</div>
      </div>

      <!-- Dienstag -->
			<div class="col-xl-4 col-lg-6 col-md-12">
        <h4>Dienstag</h4>
        <div class="form-group form-check">
          <label class="form-check-label">
            Mensaessen möglich
          </label>
          <input class="form-check-input" type="checkbox" name="diMensa"<?php echo !empty($projekt["diMensa"]) && $projekt["diMensa"] == "true" || empty($projekt) ? " checked" : ""; ?>>
        </div>

        <div class="form-row">
          <div class="form-group col-sm-6 col-xs-12">
            <label>Vormittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["DienstagVormittagHinweis"]; ?>" rows="3" name="diVor"<?php echo $config["DienstagVormittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["diVor"]) ? (empty($projekt["diVor"]) ? "" : newlineBack($projekt["diVor"])) : $_POST["diVor"]; ?></textarea>
          </div>

          <div class="form-group col-sm-6 col-xs-12">
            <label>Nachmittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["DienstagNachmittagHinweis"]; ?>" rows="3" name="diNach"<?php echo $config["DienstagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["diNach"]) ? (empty($projekt["diNach"]) ? "" : newlineBack($projekt["diNach"])) : $_POST["diNach"]; ?></textarea>
          </div>
        </div>
      </div>

      <!-- Mittwoch -->
			<div class="col-xl-4 col-lg-6 col-md-12">
        <h4>Mittwoch</h4>
        <div class="form-group form-check">
          <label class="form-check-label">
            Mensaessen möglich
          </label>
          <input class="form-check-input" type="checkbox" name="miMensa"<?php echo !empty($projekt["miMensa"]) && $projekt["miMensa"] == "true" || empty($projekt) ? " checked" : ""; ?>>
        </div>

        <div class="form-row">
          <div class="form-group col-sm-6 col-xs-12">
            <label>Vormittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["MittwochVormittagHinweis"]; ?>" rows="3" name="miVor"<?php echo $config["MittwochVormittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["miVor"]) ? (empty($projekt["miVor"]) ? "" : newlineBack($projekt["miVor"])) : $_POST["miVor"]; ?></textarea>
            <div class="invalid-feedback">
              Ungültige Eingabe
            </div>
          </div>

          <div class="form-group col-sm-6 col-xs-12">
            <label>Nachmittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["MittwochNachmittagHinweis"]; ?>" rows="3" name="miNach"<?php echo $config["MittwochNachmittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["miNach"]) ? (empty($projekt["miNach"]) ? "" : newlineBack($projekt["miNach"])) : $_POST["miNach"]; ?></textarea>
          </div>
        </div>
      </div>

      <!-- Donnerstag -->
			<div class="col-xl-4 col-lg-6 col-md-12">
        <h4>Donnerstag</h4>
        <div class="form-group form-check">
          <label class="form-check-label">
            Mensaessen möglich
          </label>
          <input class="form-check-input" type="checkbox" name="doMensa"<?php echo !empty($projekt["doMensa"]) && $projekt["doMensa"] == "true" || empty($projekt) ? " checked" : ""; ?>>
        </div>

        <div class="form-row">
            <div class="form-group col-sm-6 col-xs-12">
            <label>Vormittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["DonnerstagVormittagHinweis"]; ?>" rows="3" name="doVor"<?php echo $config["DonnerstagVormittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["doVor"]) ? (empty($projekt["doVor"]) ? "" : newlineBack($projekt["doVor"])) : $_POST["doVor"]; ?></textarea>
          </div>

          <div class="form-group col-sm-6 col-xs-12">
            <label>Nachmittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["DonnerstagNachmittagHinweis"]; ?>" rows="3" name="doNach"<?php echo $config["DonnerstagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["doNach"]) ? (empty($projekt["doNach"]) ? "" : newlineBack($projekt["doNach"])) : $_POST["doNach"]; ?></textarea>
          </div>
        </div>
      </div>

      <!-- Freitag -->
			<div class="col-xl-4 col-lg-6 col-md-12">
        <h4>Freitag</h4>
        <div class="form-group form-check">
          <label class="form-check-label">
            Mensaessen möglich
          </label>
          <input class="form-check-input" type="checkbox" name="frMensa"<?php /*echo !empty($projekt["frMensa"]) && $projekt["frMensa"] == "true" || empty($projekt) ? " checked" : ""; */ echo "disabled";?>>
        </div>

        <div class="form-row">
          <div class="form-group col-sm-6 col-xs-12">
            <label>Vormittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["FreitagVormittagHinweis"]; ?>" rows="3" name="frVor"<?php echo $config["FreitagVormittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["frVor"]) ? (empty($projekt["frVor"]) ? "" : newlineBack($projekt["frVor"])) : $_POST["frVor"]; ?></textarea>
          </div>

          <div class="form-group col-sm-6 col-xs-12">
            <label>Nachmittag</label>
            <textarea class="form-control" placeholder="<?php echo $config["FreitagNachmittagHinweis"]; ?>" rows="3" name="frNach"<?php echo $config["FreitagNachmittag"] != "true" ? " disabled": ""; ?>><?php echo empty($_POST["frNach"]) ? (empty($projekt["frNach"]) ? "" : newlineBack($projekt["frNach"])) : $_POST["frNach"]; ?></textarea>
          </div>
        </div>
      </div>
		</div>
    <?php } else { ?>
		<h2 class="text-center">
			AG Termine
		</h2>

		<!-- Die einzelnen Termine -->
		<fieldset>
      <?php
      $count = 0;
      foreach ($config["agTermine"] as $termin) {
        if (empty($termin)) {
          continue;
        }
        ?>
      <div class="form-group">
        <div class="form-check">
          <label class="form-check-label">
            <?php echo $termin; ?>
          </label>
          <input class="form-check-input" type="checkbox">
        </div>
      </div>
      <?php
        $count++;
      }
      if ($count == 0) {
      ?>
      <span class="text-center text-danger">Es konnten keine Termine gefunden werden. Kontaktieren Sie bitte einen Verantwortlichen.</span>
      <?php
      }
      ?>
    </fieldset>
    <?php } ?>

		<!-- Einreichen Knopf -->
		<div class="text-left">
      <button type="button" class="btn btn-danger" onclick="logout()">
				Abmelden
			</button>
			<button type="button" class="btn btn-primary" onclick="window.location.href = '?';">
				Zurück
			</button>
			<button name="action" value="<?php echo !empty($_GET["projekt"]) ? "editProject" : "addProject"; ?>" class="btn btn-success float-right" type="submit">
				<?php echo !empty($_GET["projekt"]) ? "Änderungen speichern" : "Projekt einreichen"; ?>
			</button>
		</div>
	</form>
</div>

<script src="js/pingUpdateOnly.js"></script>
