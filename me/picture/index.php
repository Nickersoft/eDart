<?php
/* 
 * Page Name: Picture
 * Purpose: Print current user's profile picture
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */
header("Content-type: image/jpg");

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Import core functionality

//Get current user's info
$thisUser = new User(array("action"=>"get","id"=>$_SESSION["userid"]));
$userInfo = $thisUser->run(true);

//Get the current user's profile picture
$contents = $userInfo[0]["profile_pic"];

//If it isn't set...
if(trim($contents)=="")
{
	//...use the default image
	$contents = file_get_contents($_SERVER["DOC_ROOT"]."/img/user_icon_200.png");
}

	//If a size is specified...
	if(isset($_GET["size"]))
	{
		switch(strtolower($_GET["size"]))
		{
			//Change the image accordingly
			case "small":
				$contents = WideImage::loadFromString($contents)->resize(59)->asString('jpg');
				break;
		}	
	}

echo $contents; //Print the image

?>