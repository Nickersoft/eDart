<?php
/*
 * Page Name: Validation Landing
 * Purpose: Validates email and allows user to continue
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Import core functionality

$title = "Invalid Validation";  	//Default title
$head  = "Invalid Validation Key";			//Default header
$contfunc = "window.location=/validate.php";//Redirect script

$uid = "";				//Empty user ID
$key = $_GET["auth"];	//Get the authorization token

//Connect to MySQL
$con = mysqli_connect(host(), username(), password(), mainDb());

//Check to see if the key is in the validation
$q = "SELECT * FROM validate WHERE `key`='".mysqli_real_escape_string($con, $key)."'";

//Run the MySQL query
$qr = mysqli_query($con, $q);

//Boolean denoting whether the key validation succeeded
$success = false;

//Check to see if the query was successful
while($r = mysqli_fetch_array($qr))
{
	//If it was...

	$uid  = $r["id"];					//Get the user ID associated with the key
	$head = "Email Validated!";			//Change the header
	$success = true;					//Change the boolean
	$title= "Email Validated";	//Change the title

	//Delete the validation key from the table
	mysqli_query($con, "DELETE FROM validate WHERE `key`='".mysqli_real_escape_string($con, $key)."'");
}

//Select the current user in the database
$user_q = mysqli_query($con, "SELECT * FROM usr WHERE id='".mysqli_real_escape_string($con, $uid)."'");

//If they are not fully validated, increase the validation
while($r = mysqli_fetch_array($user_q))
{
	if($r["status"]<2)
	{
		mysqli_query($con, "UPDATE usr SET status='1' WHERE id='".mysqli_real_escape_string($con, $uid)."'");
	}
}

//Close the MySQL connection
mysqli_close($con);

HTML::begin();
Head::make($title);
Body::begin();
?>
			<div id="mc_cont" style="width:500px;margin-top:50px;margin-bottom:50px;">
				<div id="mc" style="width:500px;">
					<h1 style="text-align:center;margin-bottom:10px;margin-top:10px;"><?php echo $head; ?></h1>

					<?php

						//If the validation was successful...
						if($success)
						{
							//...create the continue button
							echo "<table id=\"loginbx\" style=\"height:auto;width:500px;margin-top:30px;\">
									<tr>
										<td>
									<input 	type=	\"button\"
										style=	\"width:100px;font-size:14px;display:block;margin:0 auto;text-align:center;\"
										class=	\"gbtn\"
										onclick=\"window.location='/terms.php';\"
										value=	\"Continue\"
									/>
										</td>
									</tr>
								</table>";
						}
					?>
				</div>
			</div>

	<?php
	Body::end();
	HTML::end(); ?>
