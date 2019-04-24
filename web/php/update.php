<?php
if (!isLogin() || $_SESSION['benutzer']['typ'] != "admin") {
  die("Fehlende Berechtigung");
}
if (file_exists("../data/algorithmus.pid")) {
  alert("Der Zuteilungsalgorithmus läuft momentan, daher kann gerade kein Update durchgeführt werden.");
}
elseif (file_exists("../data/update.pid")) {
  alert("Aktuell wird bereits ein Update durchgeführt.");
}
else {
  $cmd = "git pull --all";
  $outputfile = "../data/update.log";
  $pidfile = "../data/update.pid";
  exec("cd ../; " . sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
  alert("Das Update wurde gestartet.");
}
?>
