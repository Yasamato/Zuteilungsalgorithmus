<?php
if (isLogin() || $_SESSION["user"]["type"] != "admin") {
  // code...
}
?>
