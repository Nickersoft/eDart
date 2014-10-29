<?php
/*
 * Page Name: User Signup
 * Purpose: Signs up a new user
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Import core functionality

//Default required fields
$required_fields = array("fname","lname","eaddr","pword", "rpword");

//Default "success" code
$error = 200;

//Go through each required field and make sure it was passed to the form
foreach($required_fields as $f)
{
	//If it wasn't...
	if((trim($_POST[$f])=="")||(!isset($_POST[$f])))
	{
		//Throw an error
		header("Location:/signup/?error=401");
		exit;
	}
}

//If the passwords don't match...
if($_POST["pword"]!=$_POST["rpword"])
{
	//Throw an error
	header("Location:/signup/?error=105");
	exit;
}

//If everything is going as planned, used the API to create a new user
$new_user = new User(array( "action"=>"create",
							"fields"=>array("fname"=>$_POST["fname"],
											"lname"=>$_POST["lname"],
											"email"=>$_POST["eaddr"],
											"password"=>$_POST["pword"])));

//Get the response from the server
$response = $new_user->run(true);

//If the signup STILL wasn't successful...
if($response!=200)
{
	//Throw a custom error
	header("Location:/signup/?error=".$response);
}
else //If the signup was successful...
{
	//...log them in with their new credentials
	$login = new Login(array("action"=>"login", "email"=>$_POST["eaddr"], "password"=>$_POST["pword"]));
	$login->run();

	//Navigate to the email validator
	header("Location:/signup/email_sent.php");
	exit; //Exit
}

/* * * * * * * * * * * * * * * * *
 *    Available for debugging:   *
 *        echo $response;        *
 * * * * * * * * * * * * * * * * */

?>
