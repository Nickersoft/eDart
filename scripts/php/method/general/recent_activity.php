<?php
/* 
 * Page Name: Recent Activity
 * Purpose: Function/AJAX script for returning recent activity given a user ID
 * Last Updated: 6/6/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

//Main function
function getRecentActivity($uid)
{
	//Declare the primary log for this user
	$log = array();

	$posse = "his/her"; //User possessive
	$prono = "he/she";	//User pronoun
	
	/* * * * * * * * * * * * * * * * *
	 *  		 CHECK #1            *
 	 * 		 Join date of user 		 *
 	 * * * * * * * * * * * * * * * * */

	// 1) Pull the user info from the database

	$curUser  = new User(array("action"=>"get", "id"=>$uid));
	$userInfo = $curUser->run(true); //The 'true' gives us extra permissions

	// 2) If the user doesn't exists...
	if(count($userInfo)==0)
	{	
		return array(); //Exit
	}
	
	// 3) But if they do, add them to the log

	//Get the first and last name of the user
	$fname = $userInfo[0]["fname"];
	$lname = $userInfo[0]["lname"];

	$ulog_str = "$fname joined eDart"; 		//User log string
	$ulog_dte = $userInfo[0]["join_date"]; 	//User log date
	$ulog_lnk = "/profile.php?id=" . $uid;	//User log link

	//Create a log to append
	$user_log = array("id"=>$uid, "name"=>"$fname $lname", "string"=>$ulog_str, "date"=>$ulog_dte, "link"=>$ulog_lnk);

	//Append the log
	array_push($log, $user_log);	
	

	//Set the pronoun different if it's a girl
	if(intval($userInfo[0]["gender"])==2)
	{
		$posse = "her";
		$prono = "she";
	}
	else if(intval($userInfo[0]["gender"])==1)
	{
		$posse = "his";
		$prono = "he";
	}

	 
	/* * * * * * * * * * * * * * * * *
	 *  		 CHECK #2            *
 	 * 		 Any added items 		 *
 	 * * * * * * * * * * * * * * * * */

	// 1) Return an array of every item in the database
	$itemsCall = new Item(array("action"=>"get"));
	$allItems  = $itemsCall->run(true);

	foreach($allItems as $item)
	{
		// 2) Check to see if the user posted the item 
		if($item["usr"]==$uid)
		{
			//If they did, add it to the log 

			$plog_str = "$fname posted $posse item: " . $item["name"];
			$plog_dte = $item["adddate"];
			$plog_lnk = "/view.php?itemid=".$item["id"]."&userid=".$item["usr"];

			$post_log = array("id"=>$uid, "name"=>"$fname $lname", "string"=>$plog_str, "date"=>$plog_dte, "link"=>$plog_lnk);
			array_push($log, $post_log);

		}
		else
		{
			//If they didn't, see if they made an offer on it

			$offers = json_decode($item["offers"], true);
			if(is_array($offers))
			{
				foreach($offers as $user=>$offer) 
				{
					if(trim($user) == trim($uid))
					{
						//Turns 'a' to 'an' if item starts with a vowel
						$vowarr   		= array('a','e','i','o','u');
						$name 		= $item["name"];
						$itemname_start = $name[0];
						$a = "a";
						if(in_array(strtolower($itemname_start), $vowarr))
						{
							$a .= "n";
						}

						//Get info about the item
						$offer_item = new Item(array("action"=>"get", "filter"=>array("id"=>$offer[0])));
						$offer_info = $offer_item->run(true);

						//Add it to the log
						$olog_str = "$fname offered $posse {$offer_info[0]["name"]} for $a $name";
						$olog_dte = $offer[1];
						$olog_lnk = "/view.php?itemid=".$item["id"]."&userid=".$item["usr"];

						$offer_log = array("id"=>$uid, "name"=>"$fname $lname", "string"=>$olog_str, "date"=>$olog_dte, "link"=>$olog_lnk);
						array_push($log, $offer_log);
					}
				}
			}
		}
	}

	//Now sort it
	usort($log, "syncedSort");

	//Return it
	return $log;
}

//Sorts an array based on a given key
function syncedSort($s1, $s2)
{   
	//Sorts based on date. Shouldn't need to be modified.
	if(intval($s1["date"]) > intval($s2["date"]))
	{
		return -1;
	}
	else
	{
		return 1;
	}
}

//If a POST request was made from the feed
if(isset($_POST["ref"]) && trim(strtolower($_POST["ref"])) == "feed")
{
	//Connect to MySQL
	$con = mysqli_connect(host(), username(), password(), mainDb());

	//Get the IDs of all validated users...
	$user_query = mysqli_query($con, "SELECT * FROM `usr`");

	//This will store all the values
	$master_log = array();

	//Loop through each user
	while($row = mysqli_fetch_array($user_query))
	{
		//and add their activity to the array
		$master_log = array_merge($master_log, getRecentActivity($row["id"]));
	}

	//Sort the array
	usort($master_log, "syncedSort");

	//Print it
	echo json_encode($master_log);
}

?>