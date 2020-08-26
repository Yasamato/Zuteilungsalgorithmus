# Zuteilungsalgorithmus
Dieses Softwareprojekt wurde speziell für das Lise-Meitner Gymnasium G8GTS in Maxdorf (LMG8) erstellt und nur für dessen Einsatz gedacht.

Dieses Projekt bietet eine vollständige Weboberfläche samt Back-End Serverprogrammierung zur Datenverwaltung und Verarbeitung. Einsatzzweck ist das Anlagen von Wahlen für die Projekttage, deren Verwaltung sowie einem Wahlinterface für die Wählenden via Webserver. Es existiert hierfür eine Administrations-Oberfläche, welche die Konfiguration sowie die Freigabe einzelnen Wahlphasen steuern kann.

Neben der mühseligen Aufgabe der Datensammlung, beherscht die Software eine automatische Datenanalyse und kann ein sehr gutes Optimum für die gewählten Projekte ermitteln, welches nahezu allen manuellen Auswertungen überlegen ist.

# Systemanforderungen
- Linux OS, Mac OS (getestet mit Ubuntu 18.04 LTS)
- php-fähiger Webserver (getestet mit Apache 2.4.29)
- php7.0 (getestet mit php7.2.15)
- java Runtime Environment (getestet mit java 7 und 8)

# Ich will das Produkt benutzen!
Wenn Sie nach dem Testen unserer Software Interesse haben diese für beispielsweise ihre eigene Schule einzusetzen, kontaktieren Sie uns damit wir gemeinsam eine Vereinbarung treffen können.

# Installation
1. In das zu installierende Verzeichnis mit `cd /directory/to/work` wechseln.  
Beachten Sie dabei, dass Sie _**niemals direkt in ein aus dem Web erreichbaren Verzeichnis installieren sollten**_ (Default bei Apache wäre hier `/var/www/html`).
2. Den Befehl `git clone https://github.com/Yasamato/Zuteilungsalgorithmus/` ausführen
3. Einen Alias oder neuen VirtualHost in der Webserver-Konfiguration einfügen, welche auf das Verzeichnis `Zuteilungsalgorithmus/web` verweist.
4. Führen sie anschließend die `setup.sh` mit `sudo bash setup.sh` aus und die Software ist somit fertig installiert.

# Anpassungen
Das Projekt ist für das Lise-Meitner Gymnasium G8GTS in Maxdorf (LMG8) ausgelegt worden, weshalb die Beschreibung, der Titel und das Hintergrundbild sowie das Logo von der Schule noch als Standard vorhanden ist.
Diese Inhalte können jedoch durch Eigene ersetzt werden.
- Ersetzen Sie die Datei `web/pictures/background.jpg` mit ihrem eigenen Hintergrundbild.
- Ersetzen Sie die Datei `web/pictures/logo.jpg` mit ihrem eigenem Logo.
Hierbei muss beachtet werden, dass bei den Druckansichten das Logo nicht in der richtigen Größe erscheinen kann.
Hierfür modifizieren sie die Datei  `web/pictures/logo-position.json` und passen Sie den `x-Offset` sowie `y-Offset` und die Logo-Größe an.  
- In der Datei `web/html/head.html` passen Sie die Seitenbeschreibung sowie den Titel an.
- Fügen Sie ihr Favicon in `web/pictures/favicon` ein und binden dieses in `web/html/head.html` ein.  
Für die Favicons empfehlen wir den [Favicon Generator. For real.](https://realfavicongenerator.net/).
Beachten Sie beim erstellen, dass die Icons im Pfad `pictures/favicon` abgelegt werden müssen.

# Backups
Das System erstellt automatisch beim ersten Seitenaufruf des Tages für den Vortag ein Backup des Ordners `data/`.
Dieses wird als Archive im Ordner `backup/` als `.tar.gz` gespeichert.
Es ist empfehlenswert die Daten auch auf anderen Medien zu sichern im Falle des Falles.  
**Hinweis:** _Es werden nicht die manuelle Anpassungen im oben erwähnten Abschnitt gesichert_  
Um den Datenstand des Backups wiederherzustellen, muss das Archive lediglich im Root-Ordner der Software mit sudo-Rechten entpackt werden (Unix-Befehl: `tar -xvzf <<archive>>.tar.gz`).
Dadurch wird der `data/`-Ordner unweigerlich überschrieben und alle vorherigen nicht gesicherten Daten gehen verloren.  
Im Falle, dass die Daten auf einem DOS-System modifiziert wurden, kann es zu Problemen mit der Dateiberechtigungen kommen.
Um dies zu lösen, ist eine Korrektur der Dateiberechtigung notwendig.
Führen Sie hierzu den Befehl `sudo chown -R <<webserver-user>>:<<webserver-user>> data/` aus.

# Verwendete externe Bibliotheken (sind bereits eingebunden)
- Bootstrap v.4.3.1 (CSS und JS-Framework) [offizielle Webseite](https://getbootstrap.com/)
- jQuery v.3.3.1 (JS-Framework) [offizielle Webseite](https://jquery.com/)
- TCPDF v.6.2.26 (PHP-Libary) [offizielle Webseite](https://tcpdf.org/)
- interact.js v.1.4.0-beta.4 (JS-Libary) [offizielle Webseite](http://interactjs.io/)
