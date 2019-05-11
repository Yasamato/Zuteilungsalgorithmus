<?php
  if (!file_exists("../backup")) {
    if (!mkdir("../backup", CONFIG["dbFilesPermission"])) {
      alert("Backup-Verzeichnis konnte nicht erstellt werden... Überprüfe die Dateiberechtigungen");
      die("");
    }
  }

  if (!file_exists("../backup/" . date("Y-m-d", time() - 60 * 60 * 24) . ".tar.gz")) {
    $cmd = "tar -cvzf backup/" . date("Y-m-d", time() - 60 * 60 * 24) . ".tar.gz data/";
    $outputfile = "data/log/backup-" . date("Y-m-d", time() - 60 * 60 * 24) . ".log";
    $pidfile = "data/backup.pid";
    exec("cd ../; " . sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
  }
?>
