#!/bin/bash
# run this with sudo before you do anything

echo "Zuteilungsalgorithmus für Projektwahlen"
echo "Erstellt für das LMG8 Maxdorf"
echo
echo
echo "(c) 2018-19 Projektgruppe"
echo "Leiter des Projekts"
echo "\t- Fabian von der Warth"
echo "\t- Leo Jung"
echo
echo "Unterstützung/Managment"
echo "\t- Sören Wilhelm"
echo
echo "Algorithmus"
echo "\t- Fabian von der Warth"
echo "\t- Leon Selig"
echo "\t- Jonas Dalchow"
echo "\t- Leo Jung"
echo
echo "Webdesign"
echo "\t- Leo Jung"
echo "\t- Jan Pfenning"
echo "\t- Lukas Fausten"
echo "\t- Tim Schneider"
echo "\t- Tobias Palzer"
echo
echo "Serverprogrammierung"
echo "\t- Leo Jung"
echo "\t- Tim Schneider"
echo "\t- Lukas Fausten"
echo
if [ $(id -u) -ne "0" ]; then
  echo "Dieses Skript benötigt sudo-Rechte"
  exit
fi
echo
echo
echo
echo "Konfiguration des Webservers"
echo
echo "Wählen sie den Namen des Adminaccounts, welcher zum Login der Administrationsseite benötigt wird. Der Name kann jederzeit in data/config.php eingesehen sowie editiert werden"
read -p "Admin-Benutzername: " adminUser
echo
echo "Das Passwort kann gleichfalls jederzeit geändert werden"
read -sp "Geben sie ein Admin-Passwort an: " adminPassword
echo
echo
echo "Mit welchem Benutzer wird der Webserver ausgeführt"
echo "Apache benutzt beispielsweise www-data"
read -p "Benutzer: " user
echo
echo "Mit welchen Zugriffsrechten sollen die gesamten Daten abgespeichert werden?"
echo "Schema siehe: https://www.w3schools.com/php/func_filesystem_chmod.asp"
read -p "Berechtigung: " level
echo
echo "Speichere Ergebnisse ab in data/config.php"
cat > data/config.php <<EOF
<?php
  define("CONFIG", [
    "dbLineSeperator" => "__;__",
    "dbElementSeperator" => "__#__",
    "dbFilesPermission" => $level, // see https://www.w3schools.com/php/func_filesystem_chmod.asp
    "adminUser" => "$adminUser",
    "adminPassword" => "$adminPassword",
    "minStufe" => 5,
    "maxStufe" => 12
  ]);
?>
EOF

sudo chown -R $user:$user data/
sudo chmod -R $level data/
sudo chown -R $user:$user FinishedAlgorithm/
sudo chmod -R $level FinishedAlgorithm/

echo "Fertig"
echo
exit
