# Zuteilungsalgorithmus

Dieses Softwareprojekt wurde speziell für das Lise-Meitner Gymnasium G8GTS in Maxdorf (LMG8) erstellt und nur für dessen Einsatz gedacht.

Dieses Projekt bietet eine vollständige Weboberfläche samt Back-End Serverprogrammierung zur Datenverwaltung und Verarbeitung. Einsatzzweck ist das Anlagen von Wahlen für die Projekttage, deren Verwaltung sowie einem Wahlinterface für die Wählenden via Webserver. Es existiert hierfür eine Administrations-Oberfläche, welche die Konfiguration sowie die Freigabe einzelnen Wahlphasen steuern kann.

Neben der mühseligen Aufgabe der Datensammlung, beherscht die Software eine automatische Datenanalyse und kann in allen Testfällen beweisbar optimale Lösungen für die gewählten Projekte ermitteln.

# Dev setup

Benötigt werden [docker](https://www.docker.com/products/docker-desktop/) und [bun](https://bun.sh/) vorinstalliert.

Zum Installieren aller dependencies für die web ui den folgenden befehl ausführen:

```bash
git config diff.lockb.textconv bun && git config diff.lockb.binary true && bun install --frozen-lockfile
```

Nun die [`.env.example`](.env.example) Datei kopieren mit

```bash
cp .env.example .env
```

Die `.env` Datei ggf. anpassen.

Zum Starten der DB die Anleitung befolgen:

```sh
# TO RUN ON WINDOWS:
# 1. Install WSL (Windows Subsystem for Linux) - https://learn.microsoft.com/en-us/windows/wsl/install
# 2. Install Docker Desktop for Windows - https://docs.docker.com/docker-for-windows/install/
# 3. Open WSL - `wsl`
# 4. Run this script - `./start-database.sh`

# On Linux and macOS you can run this script directly - `./start-database.sh`
```

Nun kann über `bun run dev` das Frontend getestet werden

> \*_*NOTE:*_ unter Windows muss erst wieder wsl verlassen werden mit `exit`

# Systemanforderungen

- Linux OS, Mac OS (getestet mit Ubuntu 18.04 LTS)
- php-fähiger Webserver (getestet mit Apache 2.4.29)
- php7.0 (getestet mit php7.2.15)
- Python <3.12 (getestet mit conda, Python 3.11.8) (3.12 ging zum Zeitpunkt des Tests tatsächlich nicht, w.g. MIP library)

# Ich will das Produkt benutzen!

Wenn Sie nach dem Testen unserer Software Interesse haben diese für beispielsweise ihre eigene Schule einzusetzen, kontaktieren Sie uns damit wir gemeinsam eine Vereinbarung treffen können.

# Installation

1. In das zu installierende Verzeichnis mit `cd /directory/to/work` wechseln.  
   Beachten Sie dabei, dass Sie _**niemals direkt in ein aus dem Web erreichbaren Verzeichnis installieren sollten**_ (Default bei Apache wäre hier `/var/www/html`).
2. Den Befehl `git clone https://github.com/Yasamato/Zuteilungsalgorithmus/` ausführen
3. Einen Alias oder neuen VirtualHost in der Webserver-Konfiguration einfügen, welche auf das Verzeichnis `Zuteilungsalgorithmus/web` verweist.
4. Führen sie anschließend die `setup.sh` mit `sudo bash setup.sh`
5. (Optional) Wechseln in das korrekte python environment via conda activate /ähnliches
6. pip install -r ip_solver/requirements.txt

# Anpassungen

Das Projekt ist für das Lise-Meitner Gymnasium G8GTS in Maxdorf (LMG8) ausgelegt worden, weshalb die Beschreibung, der Titel und das Hintergrundbild sowie das Logo von der Schule noch als Standard vorhanden ist.
Diese Inhalte können jedoch durch Eigene ersetzt werden.

- Ersetzen Sie die Datei `web/pictures/background.jpg` mit ihrem eigenen Hintergrundbild.
- Ersetzen Sie die Datei `web/pictures/logo.jpg` mit ihrem eigenem Logo.
  Hierbei muss beachtet werden, dass bei den Druckansichten das Logo nicht in der richtigen Größe erscheinen kann.
  Hierfür modifizieren sie die Datei `web/pictures/logo-position.json` und passen Sie den `x-Offset` sowie `y-Offset` und die Logo-Größe an.
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

## Libraries used in the rewrite

- [Next.js](https://nextjs.org)
- [NextAuth.js](https://next-auth.js.org)
- [Drizzle](https://orm.drizzle.team)
- [Tailwind CSS](https://tailwindcss.com)
- [tRPC](https://trpc.io)
