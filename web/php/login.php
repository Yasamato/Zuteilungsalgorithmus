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
		if (file_exists("../data/admin.lock") && time() - time(explode(",", file_get_contents("../data/admin.lock"))[0]) / 20 < 1) {
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
	    fwrite($fh, time() . "," . session_id());
	    fclose($fh);
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
	// DEBUG fake-accounts
	elseif ($_POST['user'] == "lehrer" && $_POST['pw'] == "lehrer") {
		$_SESSION['benutzer'] = [
			"uid" => "test-" . uniqid(),
			"typ" => "teachers",
			"klasse" => "",
			"stufe" => "",
			"vorname" => "L",
			"nachname" => "eherer"
		];
	}
	elseif ($_POST['user'] == "schüler") {
		// or failsafe strlen($_POST["pw"])
		$a = strlen($_POST["pw"]);
		for ($i = 0; $i < $a; $i++) {
			if (!is_numeric($_POST["pw"][$i])) {
				$a = $i;
				break;
			}
		}
		$_SESSION['benutzer'] = [
			"uid" => "test-" . uniqid(),
			"typ" => "students",
			"klasse" => $_POST['pw'],
			"stufe" => substr($_POST['pw'], 0, $a),
			"vorname" => "S",
			"nachname" => "chüler"
		];
	}
	else {
		alert("Anmeldedaten ungültig");
	}

	if (!empty($_SESSION["benutzer"])) {
		alert("Erfolgreich angemeldet");
	}
?>
