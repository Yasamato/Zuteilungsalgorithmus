<?php
	function read($path, $search = "", $value = ""){
		if(($fh = fopen($path, "r")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));
			if(substr($file, 0, -1) == "\n"){
				$file = substr($file, 0, filesize($path) - 1);
			}
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
						if($i == 0){
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

	function set($path, $search, $searchNeedle, $index, $replace){
		if(($fh = fopen($path, "r+")) !== false) {
			$output = [];
			$meta = true;
			$file = fread($fh, filesize($path));
			foreach(explode("__;__", $file) as $line){
				if($meta){
					$head = explode("__#__", $line);
					$meta = false;
				}
				else{
					$line = explode("#", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						if($i == 0){
							$v = str_replace("\n", "", $v);
						}
						$tmp[$head[$i++]] = $v;
					}
					array_push($output, $tmp);
				}
			}
			foreach($output as $entry){
				if($entry[$search] == $searchNeedle){
					$entry[$index] = $replace;
				}
			}
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
