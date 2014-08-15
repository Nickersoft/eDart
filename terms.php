<?php
/*
 * Page Name: Terms of Service
 * Purpose: Allows users to approve terms of service
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Core functionality

//If the user is not logged in...
if(!isset($_SESSION["userid"]))
{
	header("Location:./"); //...redirect
	exit;
}


//Connect to MySQL
$con   = mysqli_connect(host(), username(), password(), mainDb());

//Get the user
$query = mysqli_query($con, "SELECT * FROM usr WHERE id='".mysqli_real_escape_string($con, trim($_SESSION["userid"]))."'");

//Look at their status
while($r = mysqli_fetch_array($query))
{
	switch($r["status"])
	{
		//If they haven't validated their email yet (0)
		case 0:
			//Redirect
			header("Location:/signup/email_sent.php");
			exit;
			break;

			//If they are already validated
		case 2:
			//Redirect
			header("Location:/me");
			exit;
			break;
	}
}

//Close the connection
mysqli_close($con);

HTML::begin();
Head::make("Terms and Conditions");
Body::begin();

			?>

			<div id="trmcnt" style="margin-top:20px;margin-bottom:20px;">
				<h1 class="uk-text-center" style="padding-bottom:1em;">Agreeing to the Terms of Service</h1>
				<div id="trmbx" style="white-space:normal;">
					<?php

						//Get the terms out of the HTM file
						echo file_get_contents("terms.htm");

					?>
				</div>

				<div class="text_medium uk-text-center" style="padding-top:10px;padding-bottom:10px;">You must agree to the above terms before you can create your account.</div>
				<div class="uk-text-center">
					<a href="/signup/validate.php" class="button_primary green">I Agree</a>
					<a class="button_primary red" href="javascript:void(0);" onclick="logout();">I Refuse</a>
				</div>
<?php
Body::end();
HTML::end();
?>
