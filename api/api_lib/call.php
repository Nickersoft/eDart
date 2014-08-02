<?php

date_default_timezone_set("America/New_York");

define("ACTIVITY", 'a');
define("USER", 'u');
define("ITEM", 'i');
define("CONVERSATION", 'c');
define("NOTIFICATIONS", 'n');
define("SALT_LEN", 256);

define("NEW_USER", "newuser");

include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/mysql.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/shared.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/item.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/user.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/login.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/crypt.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/listener.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/messenger.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/exchange.php";
include_once $_SERVER ["DOC_ROOT"] . "/api/api_lib/feed.php";

$available_category  = array("Apparel", "Athletic Clothing", "Books", "Computers", "Electronics", "Furniture/Decor", "Games", "Jewellery", "Linens", "Movies/Videos", "Music", "School/Office Supplies", "Sport Accessories");
$available_condition = array("Almost New", "Subtly Used", "Noticably Used", "Extremely Used", "Hardcore Usage");

if(!isset($_SESSION))
{
	try
	{
		session_start();
	}
	catch(Exception $e) { }
}

?>