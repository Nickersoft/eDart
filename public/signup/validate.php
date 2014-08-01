<?php
/*
 * Page Name: Validate
 * Purpose: Fully validates a user (sets their status to 2)
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Core functionality

//If the user is not logged in,
if(!isset($_SESSION["userid"]))
{
	header("Location:./"); //redirect
}
else //If they are logged in
{
	//Connect to MySQL
	$con = mysqli_connect(host(), username(), password(), mainDb());

	//Change the user's status
	$q   = "UPDATE usr SET `status`='2' WHERE `id`='".mysqli_real_escape_string($con, $_SESSION["userid"])."'";
	mysqli_query($con, $q);

	//Close the connection
	mysqli_close($con);

	header('Location:/'); //Redirect to their control panel
}

?>
<html>
	<head>
		<title>Validating...</title>
	</head>
	<body>
		<main>
			Please wait...
		</main>
	</body>
</html>
