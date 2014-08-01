<?php
/*
 * Page Name: Email Validator
 * Purpose: Sends a validation email to a new user
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Import core functionality

//If the user is not logged in...
if(!isset($_SESSION["userid"]))
{
	header("Location: /");  //Send them home
	exit; 					//Exit
}

HTML::begin();
Head::make("Validate Email");
Body::begin();

			//Connect to MySQL
			$con = mysqli_connect(host(), username(), password(), mainDb());

			//Delete any previous validation keys from the server
			mysqli_query($con, "DELETE FROM validate WHERE `id`='".mysqli_real_escape_string($con, $_SESSION["userid"])."'");

			//Generate a 256 character validation key
			$ukey = random_key(256);

			//Put the key in the table with the user ID attached
			$set_key = "INSERT INTO validate(`id`, `key`) VALUES ('".mysqli_real_escape_string($con, $_SESSION["userid"])."', '".mysqli_real_escape_string($con, $ukey)."')";
			mysqli_query($con, $set_key);

			//Close the connection
			mysqli_close($con);

			//Get info about the current user
			$curuser = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
			$uinfo   = $curuser->run(true);
			$uinfo   = $uinfo[0];

			//Send an email to the user
			sendMail($uinfo["email"], $uinfo["fname"], $uinfo["lname"], "Validate Your Email", "Click the button below to validate your email.", "signup/continue.php?auth=".urlencode($ukey), "Validate Email");

			?>

			<div id="mc_cont" style="width:500px;margin-top:50px;margin-bottom:50px;">
				<div  id="mc" style="width:500px;">

					<?php

					//Print out the header
					$infotxt = <<<EOD
					<h1 style="text-align:center;margin-bottom:10px;margin-top:10px;">Validation Email Sent</h1>
					<div style="font-size:14px;text-align:center;">to %s </div>
EOD;
					echo sprintf($infotxt, $uinfo["email"]);

					//Print out the 'continue' button
					echo "<table id=\"loginbx\" style=\"height:auto;width:500px;margin-top:30px;\">
							<tr>
								<td>
							<input 	type=	\"button\"
								style=	\"width:100px;font-size:14px;display:block;margin:0 auto;\"
								class=	\"gbtn\"
								onclick=\"location.reload(true);\"
								value=	\"Resend\"
							/>
								</td>
							</tr>
						</table>";
					?>
				</div>
			</div>
		<?php
		Body::end();
		HTML::end();
		?>
