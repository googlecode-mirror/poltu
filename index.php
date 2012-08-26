<?php
//set development environment
define('ENV', 'dev'); // define('ENV', 'test'), define('ENV', 'prod');

if (defined('ENV'))
{
	switch (ENV)
	{
		case 'dev':
			error_reporting(E_ALL);
		break;
		case 'test':
		case 'prod':
			error_reporting(0);
		break;
		default:
			exit('please set an application environment....');
			
	}
}
/*
 * set system paths & vars
 */
define("BASE_URL","http://localhost:81/app_engine");

// DB Configuration

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "app_engine");

$eng_folder = "app_engine";
$core_path = "core";
$modules_path = "modules";
$controllers_path = "controllers";
$languages_path = "languages";
$cache_path = "cache";

define("TMPL_PATH", "/templates");
$notification_path = "notifications";
$data_source_type  = "mysql"; // mysql/file(not implemented yet)[if file, schedule job must be running for taking db snaps and writing to files] 

/*
 * load application engine
 */
require_once $core_path . '/app_loader.php';
?>