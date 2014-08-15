<?php 
	/* 
	 * Page Name: Home
	 * Purpose: Either the splash page or the feed page, depending on if the user is logged in
	 * Last Updated: 6/5/2014
	 * Signature: Tyler Nickerson
	 * Copyright 2014 eDart
	 *
	 * [Do not remove this header. One MUST be included aut the start of every page/script]
	 *
	 */

	//MUST be at the top of every file
	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Include core functionality

	HTML::begin();
	Head::make("eDart Beta", false);

	//If the user is logged in...
	if(isset($_SESSION["userid"]))
	{
		//We run a collection of feed functions
		Body::add_action("pre_feed()");
		//Body::begin();
	}
	else //If not...
	{
		//We run some home functions
		Body::add_action("pre_home()");
		Body::begin(true, true);
	}


	//If we aren't logged in, print the home/splash page
	if(!isset($_SESSION["userid"]))
	{
		include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/home.php";

	}
	else //If the user IS logged in...
	{
		/* * * FEED * * */

		include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/feed.php"; //Print out the feed HTML
	}

	Body::end(); 
	HTML::end();
?>