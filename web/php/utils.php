<?php
	function isLogin() {
		return isset($_SESSION['benutzer']);
	}

	function logout() {
		session_destroy();
		unset($_SESSION);
		session_start();
	}

	function alert($msg) {
		echo "<script>alert(`" . $msg . "`);</script>";
	}

	function newlineBack($txt) {
	  $txt = str_replace("<br>", "\n", $txt);
	  $txt = str_replace("<br/>", "\n", $txt);
	  return str_replace("<br />", "\n", $txt);
	}

	function getProjektInfo($id) {
		$result = dbSearch("../data/projekte.csv", "id", $id, true);
		if (!empty($result)) {
	  	return $result[0];
		}
		error_log("Projekt mit der ID: " . $id . " konnte nicht gefunden werden", 0, "../data/error.log");
		return null;
	}

	function checkBox($v) {
		return isset($_POST[$v]) && $_POST[$v] ? "true" : "false";
	}

	// htmlentities() ?
	function newlineRemove($txt) {
		return str_replace("\n", "<br>", $txt);
	}
?>
