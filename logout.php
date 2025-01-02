<?php
session_start();
session_unset();
session_destroy();
header("location: procurer/login.php"); // Redirect to login page
exit;
?>