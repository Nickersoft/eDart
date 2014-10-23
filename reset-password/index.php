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
if(isset($_GET["auth"]))
{
	//Connect to MySQL
	$con = mysqli_connect(host(), username(), password(), mainDb());
	
	$query = mysqli_query($con, "SELECT `usr` FROM `pass_reset` WHERE `key`='".mysqli_real_escape_string($con, $_GET["auth"]) . "'");
	$uid   = mysqli_fetch_array($query);
	$uid   = $uid[0];
	
	$name  = "User";
	if($uid){
		 $user_info  = new User(array("action"=>"get", "id"=>$uid));
		 $user_array = $user_info->run(true);
		 $user_array = $user_array[0];
		 $name		 = $user_array["fname"];
		  
		// mysqli_query($con, "DELETE FROM `pass_reset` WHERE `key`='".mysqli_real_escape_string($con, $_GET["auth"]) . "'");
	}
	else 
	{
		header("Location:/");
	}
}
else {
	header("Location:/");
}

		HTML::begin();
		Head::begin("Reset Password");
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
						Hey there, <?php echo $name; ?>
					</div>

					<p>

					<?php if($complete): ?>
						We've reset your password. Check your email (spam inbox included!).
					<?php else: ?>
						Don't worry. It happens to the best of us. Just enter your email below and we'll send you a temporary one to log in with. Then, once you log in, go up to "Options / Manage Account" to change your password.

						<form name="reset_form" method="POST" action="./">
							<input type="password" class="uk-align-center uk-width-1-3" placeholder="New Password" id="npwd" name="npwd" />
							<input type="password" class="uk-align-center uk-width-1-3" placeholder="Retype Password" id="nrpwd" name="rnpwd" />
							<input type="submit" class="button_primary blue" value="Reset Password" id="forgotbtn" />
						</form>
						
					<?php endif; ?>
					
					</p>
				</div>
			</div>
		</div>
		<?php
			Body::end();
			HTML::end();
		?>
