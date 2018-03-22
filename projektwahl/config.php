<?php
//Konfiguration
define("config", [
	// Login-Server Konfiguration
	"ldapconfig" => [
		'host' => 'ldaps://10.16.1.1:636',
		'port' => '636',
		'basedn' => 'ou=accounts,dc=schule,dc=local',
		'authrealm' => 'My Realm'
	]
]);
?>
