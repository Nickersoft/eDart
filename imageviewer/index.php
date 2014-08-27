<?php
/* 
 * Page Name: Image Viewer
 * Purpose: Displays an item image using specfic GET parameters
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Include core functionality

//If an item ID is not in the GET parameters or it's blank...
if((!isset($_GET["id"]))||(trim($_GET["id"])==""))
{
	echo 401; //Throw a 401 error code
	exit; //Exit
}
else //However, if there is...
{
	//Set a JPG image page header
	header("Content-type: image/jpg");

	//Get info given the item ID
	$item = new Item(array("action"=>"get","filter"=>array("id"=>$_GET["id"])));
	$retItem = $item->run(true);

	//If the item is found...
	if(count($retItem)!=0)
	{
		//Get the first item returned given the ID
		$topIndex = $retItem[0];

		//Get it's image
		$img_contents = $topIndex["image"];

		//If the image isn't set...
		if(trim($img_contents)=="")
		{
			//...use the default image
			$img_contents = file_get_contents($_SERVER["DOC_ROOT"]."/img/default.png");
		}

			//If a size is specified in the GET request...
		if(isset($_GET["size"]))
		{
			switch(strtolower(trim($_GET["size"])))
			{
				case "small": //If it's small...
					//...resize the image accordingly
					$img_contents = WideImage::load($img_contents)->resize(50)->asString('jpg');	
					break;
				case "thumbnail": //If it's a thumbnail...
					//...resize the image accordingly
					$img_contents = WideImage::load($img_contents)->resize(100)->asString('jpg');	
					break;
				case "medium": //If it's medium...
					//...resize the image accordingly
					$img_contents = WideImage::load($img_contents)->resize(500)->asString('jpg');
					break;
			}
		}
		//Print out the image
		echo $img_contents;		
	}
}
?>