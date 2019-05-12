#!/bin/bash
# run this with sudo before you do anything

echo "Zuteilungsalgorithmus für AG- sowie Projektwahlen"
echo "Erstellt für das LMG8 Maxdorf"
echo
if [ $(id -u) -ne "0" ]; then
  echo "Dieses Skript benötigt sudo-Rechte"
  exit
fi
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
echo
echo
echo
echo "Konfiguration des Webservers"
echo
echo "Wählen sie den Namen des Adminaccounts, welcher zum Login der Administrationsseite benötigt wird. Der Name kann jederzeit in data/config.php eingesehen sowie editiert werden"
read -p "Admin-Benutzername: " adminUser
read -sp "Geben sie ein Admin-Passwort an: " adminPassword
echo
echo "Mit welchem Benutzer wird der Webserver ausgeführt"
echo "Apache benutzt beispielsweise per default www-data"
read -p "Benutzer: " user
echo
mkdir -p data
echo "Datenverzeichnis data/ erstellt"
echo "Speichere Ergebnisse ab in data/config.php"
cat > data/config.php <<EOF
<?php
  define("CONFIG", [
    "dbLineSeperator" => "__;__",
    "dbElementSeperator" => "__#__",
    "dbFilesPermission" => 0750, // siehe https://www.w3schools.com/php/func_filesystem_chmod.asp
    "adminUser" => "$adminUser",
    "adminPassword" => "$adminPassword",
    "anzahlWahlen" => 5, // Anzahl der Wahlfelder für die Schüler
    "minStufe" => 5,
    "maxStufe" => 12
  ]);
?>
EOF

cp default/background.jpg web/pictures/background.jpg
echo "Default-Hintergrundbild in web/pictures/background.jpg abgelegt"

mkdir -p web/pictures/favicon
tar -xvzf default/favicon.tar.gz -C web/pictures/favicon/
echo "Default-Favicon wurde im Verzeichnis web/pictures/favicon abgelegt"

cp default/head.html web/html/head.html
echo "HTML-Headerdatei in web/html/head.html abgelegt"

cp default/logo.jpg web/pictures/logo.jpg
echo "Default-Logo in web/pictures/logo.jpg abgelegt"

cp default/logo-position.jpg web/pictures/logo-position.json
echo "Default-Logoposition in web/pictures/logo-position.json abgelegt"

echo
echo "Setze die Datei-Berechtigungen"
sudo chown -R $user:$user ../Zuteilungsalgorithmus
sudo chmod -R 750 ../Zuteilungsalgorithmus

echo "Fertig"
echo
exit
