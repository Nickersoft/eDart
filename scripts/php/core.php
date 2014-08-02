<?php

//Define some constants
define("EXPIRED", "expiration");
define("TRANSACTION", "transactions");

//Include the classes from the API
include_once "/public/api/api_lib/call.php";

//Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

//Array of vowels
$vowels	= explode(" ", "a e i o u");

/* * * INCLUDE ALL SCRIPTS * * */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/method/general/notify.php";			//Notification/Mailing script
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/method/general/print.php";			//Prints out stuff (header, footer, etc)
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/method/general/recent_activity.php";	//Get the recent activity of a user
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/method/general/relative_date.php";	//Get the relative date
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/method/general/geo.php";				//Gets the user's location

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/class/abstract/head.php";		//HTML head tag
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/class/abstract/body.php";		//HTML body tag
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/class/abstract/html.php";		//HTML
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/class/abstract/lookup.php";	//Lookup

include_once $_SERVER["DOC_ROOT"] . "/lib/wideimage/WideImage.php";
include_once $_SERVER["DOC_ROOT"] . "/lib/mailer/PHPMailerAutoload.php";
include_once $_SERVER["DOC_ROOT"] . "/lib/geocoder/autoload.php";

?>
