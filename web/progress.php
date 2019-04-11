<?php
  require("php/utils.php");
  if (file_exists("../FinishedAlgorithm/prozentzahl") && isRunning(file_get_contents("../data/algorithmus.pid"))) {
    echo file_get_contents("../FinishedAlgorithm/prozentzahl");
  }
  else {
    echo 1;
  }
?>
