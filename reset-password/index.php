<?php
/*
 * Page Name: Reset Password
 * Purpose: Allows users to reset their password in case they forgot it
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Include core functionality

$complete = false; //Boolean denoting whether the form has been submitted

//If the form has been submitted...
if(isset($_POST["forgotbox"]))
{
		$to   = trim($_POST["forgotbox"]);  //Get the email address to send it to
		$rand = random_key(8);				//Get a random password to reset to

		$fname = "eDart";	//Default user first name
		$lname = "User";	//Default user last name
		$found = false;		//Boolean as to whether this user exists

		//Connect to MySQL
		$con = mysqli_connect(host(), username(), password(), mainDb());

		//Try to find the user by email (not possible via the API)
		$que = mysqli_query($con, "SELECT * FROM `usr` WHERE `email`='".mysqli_real_escape_string($con, $to)."'");

		//Loop through the results
		while($row = mysqli_fetch_array($que))
		{
			//If we found them...

			$fname = $row["fname"]; //...set the first name
			$lname = $row["lname"];	//...set the last name
			$found = true;			//...set the boolean to true
		}


		//If they were found...
		if($found)
		{
			//Reset their password to the random key
			mysqli_query($con, "UPDATE `usr` SET `password`='".mysqli_real_escape_string($con, hash_password($rand))."' WHERE `email`='".mysqli_real_escape_string($con, $to)."'");
		}
		else //If we didn't...
		{
			//Throw an error
			header("Location:./?error=401");
			exit; //Exit
		}

		//Send an email to them with their new password
		$subject 	= "Password Reset";
		$msg 		= "Your eDart password was recently reset to the following password:<br/><br/>$rand<br/><br/>You can use this to log in, then change your password by going to 'Options / Manage Account'.";
		$link		= "http://wewanttotrade.com/login.php";
		$btnTxt		= "Log In";

		sendMail($to, $fname, $lname, $subject, $msg, $link, $btnTxt);

		$complete = true; //Change the boolean
}

		HTML::begin();
		Head::begin("Forgot Password");
?>
		<style>

		.norm{
		color:black;
		text-align:center;
		font-size:50px;
		margin-top:20px;
		}

		p {
		text-indent:20px;
		color:black;
		margin:0px;
		margin-top:5px;
		margin-bottom:5px;
		text-align:left;
		padding:20px 50px 20px 50px;
		}

		#libtable {display:block; margin:0 auto;  margin-top:20px; }
		#libtable td { padding:10px; }

		a{text-decoration:underline;color:black;}

		h1 { border-top:1px solid green; font-family:TitilliumrRegular,Trebuchet MS, sans-serif;font-size:45px;color:green; padding:25px !important; text-align:center;}


		.align{width:800px;display:block;margin:0px auto;margin-bottom:50px;}

		</style>
<?php
		Head::end();
		Body::add_action("$('#error_display').modal()");
		Body::begin();
?>

		<?php

		//If there is an error call in the URL and it's 401, print out the error box
		if(isset($_GET["error"])&&$_GET["error"]=="401")
		{
			$error_box = <<<EOL
			<div id="error_display" class="modal fade">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title">Could Not Reset Password</h4>
			      </div>
			      <div id="error_body" class="modal-body">
			      		No user is registered with this email address.
			      </div>
			      <div class="modal-footer">
			        <button type="button" data-dismiss="modal" class="bbtn">Okay</button>
			      </div>
			    </div>
			  </div>
			</div>
EOL;

			echo $error_box;
		}

		?>
		<div id="mc_cont">
			<div id="mc">
				<div class="align">
					<div class="norm" >
						<?php
							//If the form was submitted, show a different header than usual
							if($complete)
							{
								echo "Password reset!";
							}
							else
							{
								echo "Forgot your password?";
							}
						?>
					</div>

					<p>

					<?php
						//If the form was submitted, show a different body than usual
						if($complete)
						{
							echo "We've reset your password. Check your email (spam inbox included!).";
						}
						else
						{
							echo "Don't worry. It happens to the best of us. Just enter your email below and we'll send you a temporary one to log in with. Then, once you log in, go up to \"Options / Manage Account\" to change your password.";

							//Print out the form
							$input = <<<EOF
								<form name="forgot_form" method="POST" action="./">
									<input type="text" class="inpt" placeholder="Email Address" id="forgotbox" name="forgotbox" />
									<input type="submit" class="button_primary blue" value="Reset Password" id="forgotbtn" />
								</form>
EOF;
							echo $input;
						}
					?>
					</p>
				</div>
			</div>
		</div>
		<?php
			Body::end();
			HTML::end();
		?>
