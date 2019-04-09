<?php
  if (file_exists("../FinishedAlgorithm/prozentzahl")) {
    echo file_get_contents("../FinishedAlgorithm/prozentzahl");
  }
  elseif (file_exists("../data/algorithmus.pid")) {
    echo 1;
  }
  else {
    echo 0;
  }
?>
