<?php
	// erstellt eine neue Datei
	function dbCreateFile($path, $headers, $ignoreExistingFile = false) {
		if (file_exists($path)) {
			error_log("Die Datei " . $path . " existiert bereits und wird ersetzt.");
			if ($ignoreExistingFile) {
				error_log("Die ursprüngliche Datei " . $path . " wurde endgültig überschrieben");
			}
			else {
				rename($path, $path . ".old");
				error_log("Die ursprüngliche Datei " . $path . " wurde sicherheitshalber nach " . $path . ".old verschoben");
			}
		}
		dbwrite($path, null, $headers);
		chmod($path, CONFIG["dbFilesPermission"]);
	}

	// speichert die Daten in einer Datei ab
	function dbWrite($path, $data, $head = "") {
		if (($fh = fopen($path, "w")) !== false) {
			if ($head == "") {
				$head = [];
				foreach ($data[0] as $key => $value) {
					array_push($head, $key);
				}
			}
			fwrite($fh, implode(CONFIG["dbElementSeperator"], $head));
			if (!empty($data)) {
				foreach ($data as $entry) {
					fwrite($fh, CONFIG["dbLineSeperator"] . "\n" . implode(CONFIG["dbElementSeperator"], $entry));
				}
			}
			fclose($fh);
		}
		else {
			error_log("Die Datei " . $path . " konnte nicht geöffnet werden");
			die("Datei: " . $path . " konnte nicht angelegt werden");
		}
	}

	// hängt einen Eintrag ans Ende der Datei
	function dbAdd($path, $data) {
		if (($fh = fopen($path, "a")) !== false) {
			fwrite($fh, CONFIG["dbLineSeperator"] . "\n" . implode(CONFIG["dbElementSeperator"], $data));
		}
		else {
			error_log("Die Datei " . $path . " konnte nicht geöffnet werden");
			die("Datei: " . $file . " konnte nicht geöffnet werden");
		}
		fclose($fh);
	}

	// liest eine Datei ein und parsed diese
	function dbRead($path) {
		if (!file_exists($path)) {
			error_log("Die Datei " . $path . " konnte nicht gefunden werden");
			return [];
		}

		if (($fh = fopen($path, "r")) === false) {
			error_log("Die Datei " . $path . " konnte nicht geöffnet werden");
			die("Datei: " . $path . " konnte nicht geöffnet werden");
			return [];
		}

		$parsedData = [];
		$file = fread($fh, filesize($path));
		if (substr($file, 0, -1) == "\n") {
			$file = substr($file, 0, filesize($path) - 1);
		}

		// parsen
		// Erstellt aus folgenden Beispieldaten:
		// ID, Name, Nachname, Alter
		// 1, Max, Mustermann, 18
		// 2, Maxine, Mustermann, 16
		//
		// folgendes Objekt:
		// [
		// 	0 => [
		// 		"ID" => 1,
		// 		"Name" => "Max",
		// 		"Nachname" => "Mustermann",
		// 		"Alter" => 18
		// 	],
		// 	1 => [
		// 		"ID" => 2,
		// 		"Name" => "Maxine",
		// 		"Nachname" => "Mustermann",
		// 		"Alter" => 16
		// 	]
		// ]
		$headLineNeeded = true;
		foreach (explode(CONFIG["dbLineSeperator"], $file) as $line) {
			if (empty($line)) {
				error_log("Die Datei " . $path . " ist eventuell korrumpiert, enthält einen leeren Eintrag");
				continue;
			}

			if ($headLineNeeded) {
				$head = explode(CONFIG["dbElementSeperator"], $line);
				$headLineNeeded = false;
			}
			else {
				$line = explode(CONFIG["dbElementSeperator"], $line);
				$parsedEntry = [];
				$i = 0;
				foreach ($line as $element) {
					if ($i == 0 || $i == count($line) - 1) {
						$element = str_replace("\n", "", $element);
					}
					$parsedEntry[$head[$i++]] = $element;
				}
				array_push($parsedData, $parsedEntry);
			}
		}

		fclose($fh);
		return $parsedData;
	}

	// durchsucht die Datei nach passenden Datensätze
	function dbSearch($path, $search, $searchNeedle, $strict = false){
		$data = dbRead($path);
		$found = [];
		foreach ($data as $entry) {
			if ($strict && $entry[$search] == $searchNeedle || !$strict && strpos(strtolower($entry[$search]), strtolower($searchNeedle)) !== false) {
				array_push($found, $entry);
			}
		}
		return $found;
	}

	// Einzelwert-Ersetzung
	function dbSet($path, $search, $searchNeedle, $index, $replace) {
		$data = dbRead($path);

		foreach ($data as $key => $entry) {
			if ($entry[$search] == $searchNeedle) {
				$data[$key][$index] = $replace;
			}
		}

		dbWrite($path, $data);
	}

	// ersetzt einen kompletten Eintrag
	function dbSetRow($path, $search, $searchNeedle, $newRow) {
		$data = dbRead($path);

		foreach ($data as $key => $entry) {
			if ($entry[$search] == $searchNeedle) {
				$i = 0;
				foreach ($data[$key] as $index => $oldValue) {
					$data[$key][$index] = $newRow[$i++];
				}
			}
		}

		dbWrite($path, $data);
	}

	// entfernt einen ganzen Eintrag
	function dbRemove($path, $search, $searchNeedle) {
		$data = dbRead($path);

		$removed = false;
		foreach ($data as $key => $entry) {
			if ($entry[$search] == $searchNeedle) {
				unset($data[$key]);
				$removed = true;
			}
		}

		if ($removed) {
			dbWrite($path, $data);
		}
		else {
			error_log("Versuche nicht vorhandenen Eintrag " . $search . " = " . $searchNeedle . " in " . $path . " zu entfernen");
		}
	}

	// löscht eine Datei
	function dbDrop($path, $verifyDeletionOfFile = false) {
		if (!file_exists($path)) {
			error_log("Die Datei " . $path . " konnte nicht gefunden werden");
		}

		if ($verifyDeletionOfFile) {
			unlink($path);
		}
		else {
			error_log("Zum unwiderruflichen Löschen der Datei " . $path . " muss das Argument verifyDeletionOfFile auf true gesetzt werden");
		}
	}
?>
