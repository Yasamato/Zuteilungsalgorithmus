<?php
	function isLogin() {
		return isset($_SESSION['benutzer']);
	}

	function logout() {
		alert("Erfolgreich abgemeldet");
		if ($_SESSION["benutzer"]["typ"] == "admin") {
			unlink("../data/admin.lock");
		}
		session_destroy();
		unset($_SESSION);
		session_start();
	}

	function alert($msg) {
		echo "<script>alert(`" . $msg . "`);</script>";
		if (!file_exists("../data/log/")) {
			if (!mkdir("../data/log", CONFIG["dbFilesPermission"])) {
				die("Es konnte kein log-Ordner angelegt werden, bitte kontaktieren sie einen Admin.");
			}
		}
		if (($fh = fopen("../data/log/" . date("Y-m-dl") . ".log", "a+")) === false) {
			die("Es konnte keine log-Datei angelegt werden, bitte kontaktieren sie einen Admin.");
		}
		fwrite($fh, date("H:i:s e") . "\t" . (!empty($_SESSION["benutzer"]) ? $_SESSION["benutzer"]["typ"] . " " . $_SESSION["benutzer"]["uid"] : "Gast") . "\t" . $msg);
		fclose($fh);
	}

	function newlineBack($txt) {
	  $txt = str_replace("<br>", "\n", $txt);
	  $txt = str_replace("<br/>", "\n", $txt);
	  return str_replace("<br />", "\n", $txt);
	}

	function getProjektInfo($projekte, $id) {
	/*
		$result = dbSearch("../data/projekte.csv", "id", $id, true);
		if (!empty($result)) {
	  	return $result[0];
		}
		error_log("Projekt mit der ID: " . $id . " konnte nicht gefunden werden", 0, "../data/error.log");
		return null;
		*/
		foreach ($projekte as $projekt) {
			if ($id == $projekt["id"]) {
				return $projekt;
			}
		}
		return [];
	}

	function checkBox($v) {
		return isset($_POST[$v]) && $_POST[$v] ? "true" : "false";
	}

	// htmlentities() ?
	function newlineRemove($txt) {
		return str_replace("\n", "<br>", $txt);
	}

  // only linux.... drop win support
  function isRunning($pid) {
      try {
          $result = shell_exec(sprintf("ps %d", $pid));
          return count(preg_split("/\n/", $result)) > 2;
      } catch(Exception $e) {
        var_dump($e);
      }
      return false;
  }
?>
