<?php
	function read($file, $search = "", $value = ""){
		if(($fh = fopen($file, "r")) !== false) {
			$output = [];
			$meta = true;
			while(($line = fgets($fh)) !== false) {
				if($meta){
					$t = explode("#", $line);
					$meta = false;
					$head = [];
					foreach($t as $v){
						$v = str_replace("\n", "", $v);
						array_push($head, $v);
					}
				}
				else{
					$line = explode("#", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
						$v = str_replace("\n", "", $v);
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
			die("Datei: " . $file . " konnte nicht geöffnet werden");
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
	
	function add($file, $entry){
		if(($fh = fopen($file, "a")) !== false) {
			fwrite($fh, implode($entry, "#") . "\n");
		}
		else {
			die("Datei: " . $file . " konnte nicht beschrieben werden");
		}
		fclose($fh);
	}
	
	function set($file, $search, $searchNeedle, $index, $replace){
		if(($fh = fopen($file, "r+")) !== false) {
			$meta = true;
			while(($line = fgets($fh)) !== false) {
				if($meta){
					$head = explode("#", $line);
					$meta = false;
				}
				else{
					$line = explode("#", $line);
					$tmp = [];
					$i = 0;
					foreach($line as $v){
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
				fwrite($fh, implode($entry, "#") . "\n");
			}
			fclose($fh);
		}
		else{
			die("Datei: " . $file . " konnte nicht eingelesen werden");
		}
	}
?>
