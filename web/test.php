<?php
  require "../data/config.php";
  require "php/db.php";
  $file = "../data/test.csv";
  dbCreateFile($file, ["ID", "Testname"]);
  $name = [
    "max",
    "MAX",
    "min",
    "MIN",
    "HIHIHIH",
    "Leo",
    "Test",
    "REGGG",
    "RHB",
    "SInddd"
  ];
  for ($i = 1; $i <= 10; $i++) {
    dbAdd($file, [$i, $name[$i - 1]]);
  }
  //var_dump(dbRemove($file, "ID", 6));
  var_dump(dbSearch($file, "ID", 6));
  var_dump(dbRead($file));
?>
