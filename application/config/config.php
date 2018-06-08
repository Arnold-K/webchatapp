<?php
	define("ENVIRONMENT", "development");
	if (ENVIRONMENT == 'development'){
		error_reporting(E_ALL);
		ini_set("display_errors", 1);		
	}
	
	
	define('URL_PUBLIC_FOLDER', 'public');
	define('URL_PROTOCOL', '//');
	define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
	define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
	define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);
	define('PUBLIC_FOLDER', str_replace(URL_DOMAIN, '', (URL_DOMAIN . URL_SUB_FOLDER)));
    


    // database connection settings
    define('DB_HOST', "localhost");
    define('DB_NAME', "webchatapp");
    define('DB_USER', "root");
    define('DB_PASS', "");

class config {
    public function prepareDatabase(){
        
    }
}
