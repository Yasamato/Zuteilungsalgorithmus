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
	elseif($_POST['user'] == "lehrer" && $_POST['pw'] == "lehrer"){
		$_SESSION['benutzer'] = [
			"uid" => "testeeeee",
			"typ" => "teachers",
			"klasse" => "",
			"stufe" => "",
			"vorname" => "L",
			"nachname" => "eherer"
		];
	}
	elseif($_POST['user'] == "schüler"){
		$_SESSION['benutzer'] = [
			"uid" => "testeeeee",
			"typ" => "students",
			"klasse" => $_POST['pw'] . "aaa",
			"stufe" => $_POST['pw'],
			"vorname" => "S",
			"nachname" => "chüler"
		];
	}
	else{
		die("Die Fake-Accounts...........");
		//login
		$loginResult = ldap_authenticate();
		if($loginResult){
			// extrahiere die Accountinformationen
			$path = explode("/", $loginResult['homedirectory'][0]);
			if($path[2] != "students"){
				$klasse = "";
				$stufe = "";
			}
			else{
				$klasse = $path[3];
				$stufe = "";
				for($i = 0; $i < count($path); $i++){
					if(is_numeric($path[3][$i])){
						if($path[3][$i] != "0"){
							$stufe .= $path[3][$i];
						}
					}
				}
			}
			//speichern in der session
			$_SESSION['benutzer'] = [
				"uid" => $loginResult['uid'][0],
				"typ" => $path[2],
				"klasse" => $klasse,
				"stufe" => $stufe,
				"vorname" => $loginResult['givenname'][0],
				"nachname" => $loginResult['sn'][0]
			];
		}
	}
?>
