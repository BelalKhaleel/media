<?php
session_start();
session_destroy();
setcookie('user', "", 1, '/');
header("location:./index.php");
exit();
