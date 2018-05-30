<?php 

        if($_SERVER['HTTP_HOST']=="localhost"){
            define("PUB", '/' . "citizen" . '/');
        } else {
            define("PUB", DIRECTORY_SEPARATOR);
        }
	define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
	define("APP", ROOT . 'application' . DIRECTORY_SEPARATOR);
	
	require APP . 'config/config.php';
    require APP . 'core/application.php';
    require APP . 'config/db_connect.php';
	$app = new Application();
?>
