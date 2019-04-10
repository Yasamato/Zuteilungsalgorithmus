<?php
  if (file_exists("../FinishedAlgorithm/prozentzahl")) {
    echo file_get_contents("../FinishedAlgorithm/prozentzahl");
  }
  else {
    echo 1;
  }
?>
