<?php

	// erstellt eine neue Datei
	function dbCreateFile($path, $headers, $ignoreExistingFile = false) {
		if (file_exists($path)) {
			alert("Die Datei " . $path . " existiert bereits und wird ersetzt.");
			if ($ignoreExistingFile) {
				alert("Die ursprüngliche Datei " . $path . " wurde endgültig überschrieben");
			}
			else {
				rename($path, $path . ".old");
				alert("Die ursprüngliche Datei " . $path . " wurde sicherheitshalber nach " . $path . ".old verschoben");
			}
		}
		$result = dbWrite($path, null, $headers);
		chmod($path, CONFIG["dbFilesPermission"]);
		return $result;
	}

	// speichert die Daten in einer Datei ab
	function dbWrite($path, $data, $head = "") {
		if (($fh = fopen($path, "w")) !== false) {
			if ($head == "") {
				$head = [];
				foreach ((empty($data[0]) ? $data[1] : $data[0]) as $key => $value) {
					array_push($head, $key);
				}
			}
			fwrite($fh, implode(CONFIG["dbElementSeperator"], newlineRemove($head)));
			if (!empty($data)) {
				foreach ($data as $entry) {
					fwrite($fh, CONFIG["dbLineSeperator"] . "\n" . implode(CONFIG["dbElementSeperator"], newlineRemove($entry)));
				}
			}
			fclose($fh);
		}
		else {
			die("Datei: " . $file . " konnte nicht angelegt werden, kontaktiere einen Admin damit dieser die Zugriffsberechtigungen überprüfen kann");
		}
		return true;
	}

	// hängt einen Eintrag ans Ende der Datei
	function dbAdd($path, $data) {
		if (($fh = fopen($path, "a")) !== false) {
			fwrite($fh, CONFIG["dbLineSeperator"] . "\n" . implode(CONFIG["dbElementSeperator"], newlineRemove($data)));
		}
		else {
			alert("Die Datei " . $path . " konnte nicht geöffnet werden");
			die("Datei: " . $file . " konnte nicht geöffnet werden, kontaktiere einen Admin damit dieser die Zugriffsberechtigungen überprüfen kann");
		}
		fclose($fh);
	}

	// liest eine Datei ein und parsed diese
	function dbRead($path) {
		if (!file_exists($path)) {
			alert("Die Datei " . $path . " konnte nicht gefunden werden");
			die("Die Datei " . $path . " konnte nicht gefunden werden, kontaktiere einen Admin damit dieser das Dateisystem überprüfen kann.");
			return false;
		}

		if (($fh = fopen($path, "r")) === false) {
			alert("Die Datei " . $path . " konnte nicht geöffnet werden");
			die("Datei: " . $file . " konnte nicht geöffnet werden, kontaktiere einen Admin damit dieser die Zugriffsberechtigungen überprüfen kann");
		}

		$parsedData = [];
		$file = file_get_contents($path);
		while (substr($file, 0, 1) == "\n" || substr($file, 0, 1) == "\r") {
			$file = substr($file, 1, filesize($path));
		}
		while (substr($file, strlen($file) - 1, strlen($file)) == "\n" || substr($file, strlen($file) - 1, strlen($file)) == "\r") {
			$file = substr($file, 0, -1);
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
		foreach (explode(CONFIG["dbLineSeperator"] . "\n", $file) as $line) {
			// das auskommentierte führt zu Fehlern bei leerem Text, welcher bsp. nur optional ist
			/*if (empty($line)) {
				alert("Die Datei " . $path . " ist eventuell korrumpiert, enthält einen leeren Eintrag");
				continue;
			}*/

			if ($headLineNeeded) {
				$head = explode(CONFIG["dbElementSeperator"], $line);
				$headLineNeeded = false;
			}
			else {
				$line = explode(CONFIG["dbElementSeperator"], $line);
				if (count($line) != count($head)) {
					alert("Korrumpierte Zeile in der Datei " . $path . " gefunde. ignoriere...");
					continue;
				}
				$parsedEntry = [];
				$i = 0;
				foreach ($line as $element) {
					if (substr($element, 0, 1) == "\n" || substr($element, 0, 1) == "\r") {
						$element = substr($element, 1, strlen($element));
					}
					if (substr($element, 0, -1) == "\n" || substr($element, 0, -1) == "\r") {
						$element = substr($element, 0, strlen($element) - 1);
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
	function dbSearch($path, $search, $searchNeedle, $strict = false) {
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
				$data[$key][$index] = newlineRemove($replace);
			}
		}

		return dbWrite($path, $data);
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

		return dbWrite($path, $data);
	}

	// entfernt einen ganzen Eintrag
	function dbRemove($path, $search, $searchNeedle) {
		if (($data = dbRead($path)) === false) {
			return false;
		}

		$removed = false;
		$dataHeader = [];
		foreach ($data as $key => $entry) {
			if (empty($dataHeader)) {
				foreach ($entry as $index => $value) {
					array_push($dataHeader, $index);
				}
			}
			if ($entry[$search] == $searchNeedle) {
				unset($data[$key]);
				$removed = true;
				break;
			}
		}

		if ($removed) {
			return dbWrite($path, $data, $dataHeader);
		}
		alert("Versuche nicht vorhandenen Eintrag " . $search . " = " . $searchNeedle . " in " . $path . " zu entfernen");
		return false;
	}

	// löscht eine Datei
	function dbDrop($path, $verifyDeletionOfFile = false) {
		if (!file_exists($path)) {
			alert("Die Datei " . $path . " konnte nicht gefunden werden");
			return false;
		}

		if ($verifyDeletionOfFile) {
			return unlink($path);
		}
		alert("Zum unwiderruflichen Löschen der Datei " . $path . " muss das Argument verifyDeletionOfFile auf true gesetzt werden");
		return false;
	}
?>
