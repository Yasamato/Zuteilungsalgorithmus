<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Zugriff verweigert");
}
?>

<!-- Fehldermeldungs-Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h4 class="modal-title">Warnmeldungen und Hinweise</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
    </div>
  </div>
</div>

<!-- Fehldermeldungen -->
<div class="container">
  <div class="alert alert-danger d-none" role="alert" id="updateAlert">
    <div class="spinner-border text-primary m-2" role="status"></div>
  	Momentan wird ein Update durchgeführt, daher kann es zur Einschränkung aller Funktionen kommen.<br>
  	Die Seite wird automatisch aktualisiert, sobald das Update abgeschlossen ist, um die Änderungen zu übernehmen.<br>
  	Alle nicht gespeicherten Änderungen gehen dabei verloren!
    <details>
      <summary>Update-Log</summary>
      <p></p>
    </details>
  </div>

  <div class="alert alert-danger d-none" role="alert" id="alertErrorModal"></div>

  <div class="alert alert-warning d-none" role="alert" id="alertAlgorithmus"></div>

  <div class="d-none" id="alertAlgorithmusResult"></div>
</div>


<!-- Einstellungs-Modal -->
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title" id="configModalLabel">Projektwahlkonfiguration</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="configForm" method="post">
          <input type="hidden" name="action" value="updateConfiguration">
          <h5>Allgemeine Einstellungen</h5>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Aktuelle Phase:</label>
            <div class="col-sm-10">
              <select class="form-control" name="stage" id="stageSelect" aria-describedby="stageHelper">
                <?php
              	$stages = [
              		'<option value="0">#1 Nicht veröffentlicht</option>',
              		'<option value="1">#2 Projekte können eingereicht werden</option>',
              		'<option value="2">#3 Projekt-Einreichung geschlossen</option>',
              		'<option value="3">#4 Wahl-Interface geöffnet</option>',
              		'<option value="4">#5 Wahlen abgeschlossen</option>',
              		'<option value="5">#6 Auswertung abgeschlossen</option>'
              	];
              	echo $stages[$config["Stage"]];
              	for ($i = 0; $i < 5; $i++) {
              		if ($i == $config["Stage"]) {
              			continue;
              		}
              		echo $stages[$i];
              	}
                ?>
              </select>
            </div>
          </div>
          <small id="stageHelper" class="form-text text-muted">
            <ul>
              <li>
                "Nicht veröffentlicht": Keiner hat Zugriff außer der Admin
              </li>
              <li>
                "Projekte können eingereicht werden": Änderungen an den Einstellungen zu den Projekten können nicht mehr getätigt werden, jedoch die Hinweise vom Admin verändert werden sowie über das Lehrer-Interface Projekte eingesehen, bearbeitet und eingereicht werden
              </li>
              <li>
                "Projekt-Einreichung geschlossen": Es können keine weiteren Projekte mehr von den Lehrern eingereicht werden. Editierungen bereits bestehender Projekte ist weiterhin möglich durch den Admin sowie die Lehrer
              </li>
              <li>
                "Wahl-Interface geöffnet": Die Schüler können sich nun mit ihren Login-Daten anmelden und aus ihrem Projekt-Pool ihre Wahl wählen. Die Lehrerschaft hat nun nur noch Zugriff auf die Liste mit den Projekten, kann diese noch bearbeiten, jedoch nicht mehr einreichen (nur der Admin)
              </li>
              <li>
                "Wahl abgeschlossen": Der Schüler-Zugriff wird geschlossen, Lehrer können die Liste ansehen. Änderungen können nur noch händisch von einem Admin getätigt werden. Die Auswertung wird durch einen Admin durchgeführt
              </li>
              <li>
                "Auswertung abgeschlossen": Diese Phase kann nicht ausgewählt werden, sondern wird automatisch erreicht mit der fertigen Zuteilung durch den Zuteilungsalgorithmus, welcher durch den Admin ausgelöst werden muss
              </li>
            </ul>
          </small>
          <h5>Klassendatensätze</h5>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#klassenlisteModal">
            Einträge bearbeiten
          </button>
          <small class="form-text text-muted">
            Tragen Sie alle Klassen bitte in dieses Formular ein und speichern Sie dieses, damit Ihnen eine Übersicht zur Verfügung steht welche Klassen noch nicht vollständig gewählt haben.
          </small>

          <hr class="my-4">
          <h5>Projekt-Einstellungen</h5>
          <small class="form-text text-muted">
            Stellen sie die Dauer der Projektwoche ein sowie ggf. Hinweise für die Lehrer zur Projekterstellung.
            Diese Anmerkungen werden als zusätzliche Information beim Einreichen von Projekten beim entsprechenden Feld angezeigt.
            Durch das Auswählen der Checkboxen wird festgelegt, ob Projekte an dem jeweiligem Vor-/Nachmittag statt finden.
          </small>
          <br>
          <table class="table table-dark table-striped">
            <thead class="thead-dark">
              <tr>
                <th>
                  Wochentag
                </th>
                <th>
                  <label class="form-check-label">Montag</label>
                </th>
                <th>
                  <label class="form-check-label">Dienstag</label>
                </th>
                <th>
                  <label class="form-check-label">Mittwoch</label>
                </th>
                <th>
                  <label class="form-check-label">Donnerstag</label>
                </th>
                <th>
                  <label class="form-check-label">Freitag</label>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>Vormittags</th>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[montag][vormittag]" <?php echo $config["MontagVormittag"] == "true" ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[montag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MontagVormittagHinweis"]) ? $config["MontagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[dienstag][vormittag]" <?php echo $config["DienstagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[dienstag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DienstagVormittagHinweis"]) ? $config["DienstagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[mittwoch][vormittag]" <?php echo $config["MittwochVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[mittwoch][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MittwochVormittagHinweis"]) ? $config["MittwochVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[donnerstag][vormittag]" <?php echo $config["DonnerstagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[donnerstag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DonnerstagVormittagHinweis"]) ? $config["DonnerstagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[freitag][vormittag]" <?php echo $config["FreitagVormittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[freitag][vormittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["FreitagVormittagHinweis"]) ? $config["FreitagVormittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
              </tr>

              <tr>
                <th>Nachmittags</th>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[montag][nachmittag]" <?php echo $config["MontagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[montag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MontagNachmittagHinweis"]) ? $config["MontagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[dienstag][nachmittag]" <?php echo $config["DienstagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[dienstag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DienstagNachmittagHinweis"]) ? $config["DienstagNachmittagHinweis"] : "");

                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[mittwoch][nachmittag]" <?php echo $config["MittwochNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[mittwoch][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["MittwochNachmittagHinweis"]) ? $config["MittwochNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[donnerstag][nachmittag]" <?php echo $config["DonnerstagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[donnerstag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["DonnerstagNachmittagHinweis"]) ? $config["DonnerstagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="dauer[freitag][nachmittag]" <?php echo $config["FreitagNachmittag"] == "true"  ? "checked" : ""; echo $config["Stage"] > 0  ? " disabled" : ""; ?>>Findet statt
                  </div>
                  <div class="form-group">
                    <textarea class="form-control" name="dauer[freitag][nachmittagHinweis]" placeholder="Anmerkungen"><?php
                      echo (!empty($config["FreitagNachmittagHinweis"]) ? $config["FreitagNachmittagHinweis"] : "");
                    ?></textarea>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
        <hr class="my-4">
        <h5>Informationen</h5>
        <small class="form-text text-muted">
          Version: <?php
          echo $version . "<br>";
          if ($newest != $version) {
            if ($newest < $version) {
            ?>
            <span class="text-warning">
              Sie verwenden eine experimentelle Version. Es ist eine stabile Version verfügbar: <?php echo $newest; ?>
            <span>
            <form method="post">
              <button type="submit" class="btn btn-success" name="action" value="update">
                Wechseln
              </button>
            </form>
            <?php
            }
            else {
            ?>
            <span class="text-warning">
              Es ist eine neuere Version verfügbar: <?php echo $newest; ?>
            <span>
            <form method="post">
              <button type="submit" class="btn btn-success" name="action" value="update">
                Update
              </button>
            </form>
            <?php
            }
          }
          else {
            echo "<span class='text-success'>Die neueste Version ist installiert</span>";
          }
          ?>
        </small>
        <?php
        if (file_exists("../data/update.log")) {
          ?>
        <details id="updateLog">
          <summary>Update-log</summary>
          <p></p>
        </details><?php
        }
        ?>
        <br>
        <h5>Lizenz</h5>
        <small class="form-text text-muted">
          <?php echo file_get_contents("../LICENSE"); ?>
        </small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        <button type="button" onclick="javascript: $('#configForm').submit();" class="btn btn-primary">Speichere Änderungen</button>
      </div>
    </div>
  </div>
</div>

<!-- Projekte-Modal -->
<div class="modal fade" id="projekteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projekte</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <div id="projekteTable"></div>
      </div>

      <div class="modal-footer">
        <div class="input-group col-8 col-md-6 col-lg-4">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="projekteTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Schüler-Modal -->
<div class="modal fade" id="schuelerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüler</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#zwangszuteilungModal">
          Zwangszuteilungen
        </button>
        <br>
        <small class="text-muted">
          Das Ergebnis der Auswertung ist erst verfügbar sobald die Auswertung durch den Admin durchgeführt wurde. Die Auswertung kann erst im Admin-Panel durchgeführt werden, sobald die Wahlen geschlossen sind.
        </small>
        <div id="studentTable"></div>
        <script>
          function editStudentModal(student) {
            // button -> td -> tr
            var student = student.parentNode.parentNode;
            $("#schuelerEditForm").children("div.form-group")[0].children[1].value = $(student).attr("uid"); //uid
            $("#schuelerEditForm").children("div.form-group")[1].children[1].value = $(student).children()[0].innerHTML; //stufe
            $("#schuelerEditForm").children("div.form-group")[2].children[1].value = $(student).children()[1].innerHTML; //klasse
            $("#schuelerEditForm").children("div.form-group")[3].children[1].value = $(student).children()[2].innerHTML; //vorname
            $("#schuelerEditForm").children("div.form-group")[4].children[1].value = $(student).children()[3].innerHTML; //nachname
            $("#schuelerDeleteForm").children()[2].value = $(student).children()[2].innerHTML + " " + $(student).children()[3].innerHTML; //name
            if (window.config.Stage > 4) {
              if ($(student).children()[5].innerHTML == "Konnte nicht zugeteilt werden") {
                $("#schuelerEditForm .projekt-input").html(`
                  <input type="hidden" name="ergebnis">
                  <button type="button" onclick="javascript: changeProjektzuteilung(this);" class="btn btn-warning">
                    Projekt zuteilen
                  </button>`);
              }
              else {
                console.log($(student).children()[5]);
                $("#schuelerEditForm .projekt-input").html(`
                  <input type="hidden" name="ergebnis" required value="` + $(student).children()[5].children[0].value + `" />
                  Zuteilung
                  <button type="button" onclick="javascript: changeProjektzuteilung(this);" class="btn btn-success">
                    Ändern
                  </button>
                  <button type="button" onclick="javascript: confirmDeleteProjektzuteilung();" class="btn btn-danger">
                    Löschen
                  </button>`);
              }
            }
            else {
              $("#schuelerEditForm .projekt-input").parent().addClass("d-none");
            }
            $("#schuelerDeleteForm").children()[1].value = $(student).attr("uid");
            $("#schuelerProjektzuteilungDeleteForm").children()[1].value = $(student).attr("uid");
            $("#schuelerEditModal").modal("show");
          }
        </script>
      </div>

      <div class="modal-footer">
        <div class="input-group col-8 col-md-6 col-lg-4">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="studentTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <button onclick="javascript: window.open('printPDF.php?print=students&klasse=all');" type="button" class="btn btn-secondary">Liste drucken</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Schüler-Edit-Modal -->
<div class="modal fade" id="schuelerEditModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schülereintrag bearbeiten</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
        <br>
        <small class="text-muted">
          Schülereinträge können hier editiert werden, wobei die Wahl selbst nicht beeinflusst werden kann.
          Um die Wahl zu ändern, lassen sie den Schüler bitte erneut wählen oder tragen diesen direkt in der Zwangszuteilungs-Tabelle ein.
        </small>
        <form method="post" id="schuelerEditForm">
          <div class="form-group">
            <label>U-ID</label>
            <input type="text" class="form-control" name="uid" placeholder="U-ID" required>
          </div>
          <div class="form-group">
            <label>Stufe</label>
            <input type="number" class="form-control" min="<?php echo CONFIG["minStufe"]; ?>" max="<?php echo CONFIG["maxStufe"]; ?>" name="stufe" placeholder="Stufe" required>
          </div>
          <div class="form-group">
            <label>Klasse</label>
            <input type="text" class="form-control" name="klasse" placeholder="Klasse" required>
          </div>
          <div class="form-group">
            <label>Vorname</label>
            <input type="text" class="form-control" name="vorname" placeholder="Vorname" required>
          </div>
          <div class="form-group">
            <label>Nachname</label>
            <input type="text" class="form-control" name="nachname" placeholder="Nachname" required>
          </div>
          <div class="form-group">
            <label>Ergebnis</label>
            <div class="projekt-input flex"></div>
          </div>
          <input type="hidden" name="action" value="editWahleintrag">
        </form>
      </div>

      <form method="post" id="schuelerDeleteForm">
        <input type="hidden" name="action" value="deleteWahleintrag">
        <input type="hidden" name="uid">
        <input type="hidden" name="name">
      </form>
      <form method="post" id="schuelerProjektzuteilungDeleteForm">
        <input type="hidden" name="action" value="deleteProjektzuteilung">
        <input type="hidden" name="uid">
      </form>
      <script>
        function confirmDeleteWahl() {
        	if (confirm("Wollen sie wirklich den Wahleintrag des Schülers '" + $("#schuelerDeleteForm").children()[2].value + "' löschen?")) {
        		$('#schuelerDeleteForm').submit();
        	}
        	else {
        		alert("Löschvorgang abgebrochen");
        	}
        }
        function confirmDeleteProjektzuteilung() {
        	if (confirm("Wollen sie wirklich die Projektzuteilung des Schülers '" + $("#schuelerDeleteForm").children()[2].value + "' löschen?")) {
        		$('#schuelerProjektzuteilungDeleteForm').submit();
        	}
        	else {
        		alert("Löschvorgang abgebrochen");
        	}
        }
      </script>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="javascript: confirmDeleteWahl();">Löschen</button>
        <button type="button" class="btn btn-success" onclick="javascript: $('#schuelerEditForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Zwangszuteilungs-Modal -->
<div class="modal fade" id="zwangszuteilungModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Zwangszuteilungen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form method="post" id="zwangszuteilungForm">
          <input type="hidden" name="action" value="updateZwangszuteilung">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" class="btn btn-primary">Änderung speichern</button>
          <br>
          <small class="text-muted">
            Um Schüler vorab fest einem Projekt zuzuteilen, tragen Sie bitte die U-ID (Login-Name des Schülers) korrekt ein und wählen sie das entsprechende Projekt aus.
            Falls sie bereits ein Projekt ausgewählt haben, färbt sich der Knopf zur Projektauswahl grün und die Beschriftung ändert sich zu "Ändern".
            Es kann jedoch weiterhin jederzeit das ausgewählte Projekt geändert werden.
            Unten sehen sie einen beispielhaften Eintrag.
            Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Schüler hinzufügen &#10010;".
            Um einen Eintrag zu entfernen, betätigen sie das rote Kreuz rechts vom Eintrag.
            Bitte beachten Sie, dass unvollständige Einträge beim Speichern gelöscht werden und Änderungen nur übernommen werden bei Betätigung des "Änderungen speichern"-Knopfes.
          </small>

          <table class="table table-dark">
            <tbody>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" value="mustmax" readonly>
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="5" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="5a" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="Max" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="Mustermann" readonly>
                </td>
                <td>
                  <button type="button" class="btn btn-success" disabled>Ändern</button>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" disabled>
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <small class="text-muted">
            Tragen Sie die echten Werte bitte in der nachfolgenden Tabelle ein.
          </small>
          <table class="table table-dark table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th class="sticky-top">U-ID</th>
                <th class="sticky-top">Stufe</th>
                <th class="sticky-top">Klasse</th>
                <th class="sticky-top">Vorname</th>
                <th class="sticky-top">Nachname</th>
                <th class="sticky-top">Projekt</th>
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody id="zwangszuteilungTable"></tbody>
          </table>
        </form>
        <script>
          function addStudentsInZwangszuteilungInput() {
            $("#zwangszuteilungTable").append(`
            <tr>
              <td>
                <input type="text" class="form-control" aria-label="U-ID" name="uid[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Stufe" name="stufe[]" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Klasse" name="klasse[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Vorname" name="vorname[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Nachname" name="nachname[]" oninput="javascript: autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);">
              </td>
              <td>
                <input type="hidden" class="form-control" name="projekt[]">
                <button type="button" class="btn btn-warning" onclick="javascript: changeProjektzuteilung(this);">Projekt festlegen</button>
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>`);
          }

          function setProjektzuteilung(projekt) {
            console.log("Changing Projekt");
            var button = $(".current-open");
            // btn -> td -> tr -> td -> input
            projekt = projekt.parentNode.parentNode.children[0].children[0].value;
            $("#editStudentProjektzuteilungModal").modal("hide");
            button.children("input").val(projekt);
            button.children("button").first().html("Ändern");
            button.children("button").first().removeClass("btn-warning");
            button.children("button").first().addClass("btn-success");
            $(".current-open").removeClass("current-open");
            $("#editStudentProjektzuteilungModal tbody").html("");
            autoAppendTable('#zwangszuteilungTable', addStudentsInZwangszuteilungInput);
          }

          function changeProjektzuteilung(student) {
            student.parentNode.classList.add("current-open");
            var input = student.parentNode.children[0];
            $("#editStudentProjektzuteilungModal tbody").html("");
            for (var i = 0; i < projekte.length; i++) {
              $("#editStudentProjektzuteilungModal tbody").append(`
              <tr` + (projekte[i]["id"] == input.value ? " class='bg-success'" : "") + `>
                <td>
                  <input type="hidden" value="` + projekte[i]["id"] + `">
                  <a href="javascript: ;" onclick="javascript: showProjektInfoModal('` + projekte[i]["id"] + `');">` + projekte[i]["name"] + `</a>
                </td>
                <td>` + projekte[i]["betreuer"] + `</td>
                <td>
                  <button type="button" onclick="javascript: setProjektzuteilung(this);" class="btn btn-`
                  + (projekte[i]["id"] == input.value ? `primary">
                    OK` : `success">
                    Setzen`) + `
                  </button>
                </td>
              </tr>`);
            }
            $("#editStudentProjektzuteilungModal").modal("show");
          }
        </script>
        <button onclick="javascript: addStudentsInZwangszuteilungInput();" type="button" class="btn btn-success">Schüler hinzufügen &#10010;</button>
      </div>

      <div class="modal-footer">
        <div class="input-group col-8 col-md-6 col-lg-4">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="zwangszuteilungTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <button type="button" class="btn btn-primary" onclick="javascript: $('#zwangszuteilungForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Projektzuteilungs-Modal -->
<div class="modal fade" id="editStudentProjektzuteilungModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Projektwahl für eine Zwangszuteilung</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        <table class="table table-striped table-hover table-dark">
          <thead class="thead-dark">
            <tr>
              <th class="sticky-top">Projektname</th>
              <th class="sticky-top">Betreuer</th>
              <th class="sticky-top"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>

<!-- Klassenauflistung-Modal -->
<div class="modal fade" id="klassenlisteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüleranzahl in den Klassen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
        <button type="button" class="btn btn-primary" onclick="javascript: $('#klassenlisteForm').submit();">Änderung speichern</button>
        <br>
        <small>
          Hier können Sie die ganzen verschiedenen Klassen eintragen mit ihrer Schüleranzahl.
          Dadurch kann eine Überprüfung der Vollständigkeit durchgeführt werden.
          Um einen weiteres Eingabefeld hinzuzufügen, klicken Sie auf den grünen Knopf links unten mit der Beschriftung "Klasse hinzufügen &#10010;".
          Um einen Eintrag zu entfernen betätigen sie das rote Kreuz rechts vom Eintrag.
          Bitte beachten Sie, dass unvollständige Einträge beim Speichern gelöscht werden.
          Im Nachfolgenden sehen Sie einen beispielhaften Eintrag einer 5. Klasse mit 28 Schülern.
        </small>

        <table class="table table-dark">
          <tbody>
            <tr>
              <td>
                <input type="text" class="form-control" aria-label="Stufe" value="5" readonly>
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Klasse" value="5a" readonly>
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Schüleranzahl" value="28" readonly>
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" disabled>
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <small class="text-muted">
          Tragen Sie die echten Werte bitte in der nachfolgenden Tabelle ein.
        </small>
        <form id="klassenlisteForm" method="post">
          <input type="hidden" name="action" value="updateKlassenliste">
          <table class="table table-striped">
            <thead class="thead-dark">
              <tr>
                <th class="sticky-top">Stufe</th>
                <th class="sticky-top">Klasse</th>
                <th class="sticky-top">Schüleranzahl</th>
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody id="klassenlisteTable"></tbody>
          </table>
        </form>

        <script>
          function addKlassenlisteInput() {
            //var node = document.querySelector('#klassenliste tbody');
            $("#klassenlisteTable").append(`
            <tr>
              <td>
                <input type="text" class="form-control" placeholder="Bsp: 5" aria-label="Stufe" name="stufe[]" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
              </td>
              <td>
                <input type="text" class="form-control" placeholder="Bsp: 5a" aria-label="Klasse" name="klasse[]" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Schüleranzahl" name="anzahl[]" oninput="javascript: autoAppendTable('#klassenlisteTable', addKlassenlisteInput);">
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>`);
          }
        </script>
        <button onclick="javascript: addKlassenlisteInput();" type="button" class="btn btn-success">Klasse hinzufügen &#10010;</button>
      </div>

      <div class="modal-footer">
        <div class="input-group col-6">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="klassenlisteTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <button type="button" class="btn btn-primary" onclick="javascript: $('#klassenlisteForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>

    </div>
  </div>
</div>

<!-- Keine Wahl-Modal -->
<div class="modal fade" id="keineWahlModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content bg-dark">

      <div class="modal-header">
        <h4 class="modal-title">Schüler ohne Wahlen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="closebutton" aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form method="post" id="keineWahlForm">
          <input type="hidden" name="action" value="updateKeineWahl">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
          <button type="submit" class="btn btn-primary">Änderung speichern</button>
          <br>
          <small class="text-muted">
            Bitte tragen Sie hier alle Schüler ein, welche es versäumt haben eine Wahl zu tätigen und dementsprechend irgendein Projekt Ihrer Jahrgangsstufe zugewiesen bekommen.
            Durch den Einsatz der Schüler als "Wildcards" kann es möglich werden Projekte stattfinden zu lassen, die vorher nicht zustande kamen.
            Im Folgenden ist ein Beispieldatensatz aufgeführt.
          </small>

          <table class="table table-dark">
            <tbody>
              <tr>
                <td>
                  <input type="text" class="form-control" aria-label="U-ID" value="mustmax" readonly>
                </td>
                <td>
                  <input type="number" class="form-control" aria-label="Stufe" value="5" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Klasse" value="5a" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Vorname" value="Max" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" aria-label="Nachname" value="Mustermann" readonly>
                </td>
                <td>
                  <button type="button" class="close text-danger" aria-label="Close" disabled>
                    <span class="closebutton" aria-hidden="true">&times;</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <small class="text-muted">
            Tragen Sie die echten Werte bitte in der nachfolgenden Tabelle ein.
          </small>
          <table class="table table-dark table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th class="sticky-top">U-ID</th>
                <th class="sticky-top">Stufe</th>
                <th class="sticky-top">Klasse</th>
                <th class="sticky-top">Vorname</th>
                <th class="sticky-top">Nachname</th>
                <th class="sticky-top"></th>
              </tr>
            </thead>
            <tbody id="keineWahlTable"></tbody>
          </table>
        </form>
        <script>
          function addStudentsInKeineWahlInput() {
            $("#keineWahlTable").append(`
            <tr>
              <td>
                <input type="text" class="form-control" aria-label="U-ID" name="uid[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
              </td>
              <td>
                <input type="number" class="form-control" aria-label="Stufe" name="stufe[]" min="` + window.config["minStufe"] + `" max="` + window.config["maxStufe"] + `" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Klasse" name="klasse[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Vorname" name="vorname[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
              </td>
              <td>
                <input type="text" class="form-control" aria-label="Nachname" name="nachname[]" oninput="javascript: autoAppendTable('#keineWahlTable', addStudentsInKeineWahlInput);">
              </td>
              <td>
                <button type="button" class="close text-danger" aria-label="Close" onclick="javascript: removeLine(this);">
                  <span class="closebutton" aria-hidden="true">&times;</span>
                </button>
              </td>
            </tr>`);
          }
        </script>
        <button onclick="javascript: addStudentsInKeineWahlInput();" type="button" class="btn btn-success">Schüler hinzufügen &#10010;</button>
      </div>

      <div class="modal-footer">
        <div class="input-group col-8 col-md-6 col-lg-4">
          <div class="input-group-prepend">
            <span class="input-group-text">Suche</span>
          </div>
          <input id="keineWahlTableSearch" type="text" class="form-control" placeholder="Table durchsuchen">
        </div>
        <button type="button" class="btn btn-primary" onclick="javascript: $('#keineWahlForm').submit();">Änderung speichern</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück</button>
      </div>
    </div>
  </div>
</div>



<!-- eigentlicher Seiteninhalt -->
<div class="container-fluid">
  <div class="row">
    <!-- Spalte 1 -->
    <div class="col-12 col-lg-4">
      <div class="row flex d-flex justify-content-center">

        <div class="col-xs-12 col-sm-6 col-lg-12">
      		<div class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title">Dashboard Projektwahl</h5>
      				<p class="card-text">Übersicht über die Projektwahl-Datenbank</p>

          		<div class="btn-group btn-group-toggle" data-toggle="buttons">
                <button type="button" class="btn btn-danger" onclick="logout()">
                  Abmelden
                </button>
            		<!-- Button trigger modal -->
            		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#configModal">
            			Konfiguration
            		</button>
              </div>
      			</div>
      		</div>
        </div>

        <div class="col-xs-12 col-sm-6 col-lg-12">
      		<div id="projekteCard" class="card w-100 text-white bg-dark p-3">
      			<div class="card-body">
      				<h5 class="card-title"></h5>
      				<p class="card-text"></p>
              <!-- Button trigger modal -->
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <button onclick="javascript: window.open('printPDF.php?print=projekt&projekt=all');" type="button" class="btn btn-secondary">Drucken</button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#projekteModal">
                  Auflisten
                </button>
              </div>
              <button type="button" class="btn btn-success" onclick="window.location.href = '?site=create';">
                Neues Projekt erstellen
              </button>
      			</div>
      		</div>
        </div>

      </div>
    </div>

    <!-- Spalte 2 -->
    <div class="col-12 col-lg-8">
      <div id="projektPlatzCard" class="row flex d-flex justify-content-center"></div>
    </div>
  </div>


  <!-- Klassenauflistung -->
  <div class="row flex d-flex justify-content-center">

    <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-none">
      <div id="keineWahlCard" class="card shadow bg-dark w-100 p-3 border" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"></h5>
          <p class="card-text"></p>
          <div class="btn-group btn-group-toggle d-none" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#keineWahlModal">
              Einträge bearbeiten
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div id="kompletteKlassenCard" class="card shadow bg-dark w-100 p-3 border" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"></h5>
          <p class="card-text"></p>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#klassenlisteModal">
              Einträge bearbeiten
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div id="wahlenCard" class="card shadow bg-dark p-3 w-100 border" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title">0</h5>
          <p class="card-text">Schüler haben gewählt</p>
          <!-- Button trigger modal -->
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#schuelerModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div id="zwangszuteilungCard" class="card shadow bg-dark w-100 p-3 border border-warning text-warning" style="border: 4px solid !important;">
        <div class="card-body">
          <h5 class="card-title"></h5>
          <p class="card-text"></p>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#zwangszuteilungModal">
              Auflisten
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php if ($config["Stage"] > 2) { ?>
  <hr class="my-4">
  <div id="klassenProgressCards" class="row flex d-flex justify-content-center"></div>
  <?php } ?>

</div>

<script src="js/dashboard.js?hash=<?php echo sha1_file("js/dashboard.js"); ?>"></script>
