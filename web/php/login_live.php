<?php
	// login vom schulserver
	$ldapconfig['host'] = 'ldaps://10.16.1.1:636';
	$ldapconfig['port'] = '636';
	$ldapconfig['basedn'] = 'ou=accounts,dc=schule,dc=local';
	$ldapconfig['authrealm'] = 'My Realm';
	function ldap_authenticate() {
		global $ldapconfig;
		if (!empty($_POST['user']) && !empty($_POST["pw"])) {
			ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
			$ds = ldap_connect($ldapconfig['host']);
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			$r = ldap_search($ds, $ldapconfig['basedn'], 'uid='.$_POST['user']);
			if ($r) {
				$result = ldap_get_entries($ds, $r);
				if ($result[0]) {
					if (ldap_bind($ds, $result[0]['dn'], $_POST["pw"])) {
						return $result[0];
					}
				}
			}
		}
		return false;
	}


	if (!empty($_POST['user']) && !empty($_POST["pw"]) && $_POST["user"] == CONFIG["adminUser"] && $_POST["pw"] == CONFIG["adminPassword"]) {
		// nur 1 Admin darf zeitgleich angemeldet sein
		if (file_exists("../data/admin.lock") && time() - time(explode(",", file_get_contents("../data/admin.lock"))[0]) / 60 > 1) {
				alert("Aktuell ist berereits ein Admin eingelockt!");
		}
		else {
			if (file_exists("../data/admin.lock")) {
				// see https://www.php.net/manual/en/function.session-destroy.php#114709
				$session_id_to_destroy = explode(",", file_get_contents("../data/admin.lock"))[1];
				// 1. commit session if it's started.
				if (session_id()) {
				    session_commit();
				}

				// 2. store current session id
				session_start();
				$current_session_id = session_id();
				session_commit();

				// 3. hijack then destroy session specified.
				session_id($session_id_to_destroy);
				session_start();
				session_destroy();
				session_commit();

				// 4. restore current session id. If don't restore it, your current session will refer to the session you just destroyed!
				session_id($current_session_id);
				session_start();
			}

	    if (($fh = fopen("../data/admin.lock", "w")) === false) {
				die("Fehlende Berechtigung, bitte überprüfen Sie die Datei-Berechtigungen");
			}
			// admin ist vom login-System losgelöst
			// passwort und benutzername können in ../data/config.php festgelegt werden
			$_SESSION['benutzer'] = [
				"uid" => "admin",
				"typ" => "admin",
				"klasse" => "",
				"stufe" => "",
				"vorname" => "",
				"nachname" => ""
			];
		}
	}
	else {
		$loginResult = ldap_authenticate();
		if ($loginResult) {
			// extrahiere die Accountinformationen
			$path = explode("/", $loginResult['homedirectory'][0]);
			switch ($path[2]) {
				case "students":
					$klasse = $path[3];
					$stufe = "";
					for ($i = 0; $i < count($path); $i++) {
						if (is_numeric($path[3][$i])) {
							if ($path[3][$i] != "0") {
								$stufe .= $path[3][$i];
							}
						}
					}
					break;
				case "teachers":
					$klasse = "";
					$stufe = "";
					break;
				default:
					die("Unbekannter Login-typ: " . $path[2] . "\n<br>Falls dies doch ein gültiger Typ sein sollte, tragen sie dies als Admin in der Datei web/php/login_live.php als Fall ein");
					break;
			}

			// Speichern der Nutzerdaten in der Session
			$_SESSION['benutzer'] = [
				"uid" => $loginResult['uid'][0],
				"typ" => $path[2],
				"klasse" => $klasse,
				"stufe" => $stufe,
				"vorname" => $loginResult['givenname'][0],
				"nachname" => $loginResult['sn'][0]
			];
		}
		else {
			alert("Anmeldedaten ungültig");
			error_log("Anmeldung des Accounts '" . $_POST['user'] . "' fehlgeschlagen.", 0, "../data/error.log");
		}
	}
?>
