<?php
session_start();
session_unset();
session_destroy();

// clear cookie
setcookie("username", "", time() - 3600, "/");

header("Location: login.html");
exit();
?>
