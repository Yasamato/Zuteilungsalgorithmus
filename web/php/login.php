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
		error_log("Anmeldung des Accounts '" . $_POST['user'] . "' fehlgeschlagen.", 0, "../data/error.log");
	}
?>
