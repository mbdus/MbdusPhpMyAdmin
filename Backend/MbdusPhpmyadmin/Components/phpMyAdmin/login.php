<?php
$apiKey = $_GET['apiKey'];
$includeDir = realpath(getcwd() . '/../../../../../../../../');
$dbConfig = include($includeDir . '/config.php');
set_include_path($includeDir . '/engine/Library/');
include($includeDir . '/engine/Library/Zend/Db.php');
$db = Zend_Db::factory('Pdo_Mysql', $dbConfig['db']);
$data = $db->fetchOne('SELECT id FROM s_core_auth WHERE apiKey = ? AND active = ? AND roleID = ?', array($apiKey, 1, 1));
// CHANGE THE FOLLOWING FOR PROPER AUTHENTICATION BEHAVIOUR */
if (isset($data) && !empty($apiKey)) {
  	session_set_cookie_params(0, '/', '', 0);
 	// same as in config.inc.php :
   	session_name('SignonSession');
   	session_start();
   	$_SESSION['PMA_single_signon_user'] = $dbConfig['db']['username'];
   	$_SESSION['PMA_single_signon_password'] = $dbConfig['db']['password'];
   	$_SESSION['PMA_single_signon_host'] = $dbConfig['db']['host']; // pma >= 2.11
  	// save changes :
  	session_write_close();
    header('Location: ../phpMyAdmin/index.php?server=1');
    exit;
}
if(!empty($_POST['user']) && !empty($_POST['password'])){
   	session_set_cookie_params(0, '/', '', 0);
  	// same as in config.inc.php :
   	session_name('SignonSession');
  	session_start();
   	$_SESSION['PMA_single_signon_user'] = $_POST['user'];
   	$_SESSION['PMA_single_signon_password'] = $_POST['password'];
   	$_SESSION['PMA_single_signon_host'] = $_POST['host']; // pma >= 2.11
   	// save changes :
   	session_write_close();
   	header('Location: ../phpMyAdmin/index.php?server=1');
   	exit;
}
?>
<form action="login.php" method="post">
	Username: <input type="text" name="user" />
	Password: <input type="password" name="password" />
	Host: (will use the one from config.inc.php by default) 
	<input type="text" name="host" />
	<input type="submit" />
</form>