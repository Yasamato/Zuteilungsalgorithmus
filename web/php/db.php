<?php
	function read($path, $search = "", $value = ""){
		if(!file_exists($path)){
			return [];
		}
		if(($fh = fopen($path, "r")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));
			if(substr($file, 0, -1) == "\n"){
				$file = substr($file, 0, filesize($path) - 1);
			}

			//parse
			foreach(explode("__;__", $file) as $line){
				if(empty($line)){
					continue;
				}
				if($meta){
					$head = explode("__#__", $line);
					$meta = false;
				}
				else{
					$line = explode("__#__", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						if($i == 0 || $i == count($line) - 1){
							$v = str_replace("\n", "", $v);
						}
						$tmp[$head[$i++]] = $v;
					}
					array_push($output, $tmp);
				}
			}
			
			fclose($fh);
			if(!empty($search)){
				$output = search($output, $search, $value);
			}
		}
		else {
			die("Datei: " . $path . " konnte nicht geöffnet werden");
		}
		return $output;
	}

	function search($data, $search, $value){
		$out = [];
		foreach($data as $d){
			if($d[$search] == $value){
				array_push($out, $d);
			}
		}
		return $out;
	}

	function add($path, $entry){
		if(($fh = fopen($path, "a")) !== false) {
			fwrite($fh, "__;__\n" . implode($entry, "__#__"));
		}
		else {
			die("Datei: " . $file . " konnte nicht beschrieben werden");
		}
		fclose($fh);
	}

	function createFile($path, $headers){
		if(($fh = fopen($path, "a")) !== false) {
			fwrite($fh, implode($headers, "__#__"));
		}
		else {
			die("Datei: " . $path . " konnte nicht angelegt werden");
		}
		fclose($fh);
	}

	// Einzelwert-Ersetzung
	function set($path, $search, $searchNeedle, $index, $replace){
		if(($fh = fopen($path, "r+")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));

			//parse
			foreach(explode("__;__", $file) as $line){
				if(empty($line)){
					continue;
				}
				if($meta){
					$head = explode("__#__", $line);
					$meta = false;
				}
				else{
					$line = explode("__#__", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						if($i == 0 || $i == count($line) - 1){
							$v = str_replace("\n", "", $v);
						}
						$tmp[$head[$i++]] = $v;
					}
					array_push($output, $tmp);
				}
			}

			//replace
			foreach($output as $entry){
				if($entry[$search] == $searchNeedle){
					$entry[$index] = $replace;
				}
			}

			//write
			fseek($fh, 0);
			fwrite($fh, implode($head, "__#__"));
			foreach($output as $entry){
				fwrite($fh, "__;__\n" . implode($entry, "__#__"));
			}
			fclose($fh);
		}
		else{
			die("Datei: " . $path . " konnte nicht eingelesen werden");
		}
	}

	// ganzer Eintrag-Ersetzung
	function setRowWhere($path, $search, $searchNeedle, $replace){
		if(($fh = fopen($path, "r+")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));

			//parse
			foreach(explode("__;__", $file) as $line){
				if(empty($line)){
					continue;
				}
				if($meta){
					$head = explode("__#__", $line);
					$meta = false;
				}
				else{
					$line = explode("__#__", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						if($i == 0 || $i == count($line) - 1){
							$v = str_replace("\n", "", $v);
						}
						$tmp[$head[$i++]] = $v;
					}
					array_push($output, $tmp);
				}
			}

			//replace
			for($i = 0; $i < count($output); $i++){
				if($output[$i][$search] == $searchNeedle){
					$output[$i] = $replace;
				}
			}

			//write
			fseek($fh, 0);
			fwrite($fh, implode($head, "__#__"));
			foreach($output as $entry){
				fwrite($fh, "__;__\n" . implode($entry, "__#__"));
			}
			fclose($fh);
		}
		else{
			die("Datei: " . $path . " konnte nicht eingelesen werden");
		}
	}

	// ganzer Eintrag-Ersetzung
	function setRow($path, $row, $replace){
		if(($fh = fopen($path, "r+")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));

			//parse
			foreach(explode("__;__", $file) as $line){
				if(empty($line)){
					continue;
				}
				if($meta){
					$head = explode("__#__", $line);
					$meta = false;
				}
				else{
					$line = explode("__#__", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						if($i == 0 || $i == count($line) - 1){
							$v = str_replace("\n", "", $v);
						}
						$tmp[$head[$i++]] = $v;
					}
					array_push($output, $tmp);
				}
			}

			// replace
			$output[$row] = $replace;

			//write
			fseek($fh, 0);
			fwrite($fh, implode($head, "__#__"));
			foreach($output as $entry){
				fwrite($fh, "__;__\n" . implode($entry, "__#__"));
			}
			fclose($fh);
		}
		else{
			die("Datei: " . $path . " konnte nicht eingelesen werden");
		}
	}
?>
