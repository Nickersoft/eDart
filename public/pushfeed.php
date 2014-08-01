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

//Sorts an array based on a given key
function syncedSort2($s1, $s2)
{   
	//Sorts based on date. Shouldn't need to be modified.
	if(intval($s1["date"]) < intval($s2["date"]))
	{
		return -1;
	}
	else
	{
		return 1;
	}
}

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
	usort($master_log, "syncedSort2");
	
	foreach($master_log as $log)
	{
		$feed = new Feed(array());
		$feed->add($log["id"], $log["string"], $log["date"], $log["link"]);
	}

	echo "Done";

	//$feed = new Feed();
			//$feed->add($_SESSION["userid"], "posted his/her item: wat", time(), "/view.php?id=sfd&userid={$_SESSION["userid"]}");

?>