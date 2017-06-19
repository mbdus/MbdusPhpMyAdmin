<?php
session_destroy();
setcookie("WPCPSESSID", $_COOKIE["SignonSession"],time()-100, '/engine/Shopware/Plugins/Community/Backend/MbdusPhpmyadmin/Components/phpMyAdmin/');
header('Location: ../phpMyAdmin/login.php');
exit;
?>