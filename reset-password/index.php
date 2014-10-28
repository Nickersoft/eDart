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
if(isset($_GET["auth"])&&!empty($_GET["auth"]))
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
		$name		= $user_array["fname"];
	
		if(isset($_POST["npwd"])&&isset($_POST["rnpwd"]))
		{
			if(empty($_POST["npwd"])||empty($_POST["rnpwd"])) {
				header("Location:./?auth={$_GET["auth"]}&error=107");
			}
			else if($_POST["npwd"]==$_POST["rnpwd"]) {
				$_SESSION["userid"] = $uid; //Set the user temporarily logged in
				$change_user = new User(array("action"=>"update", "fields"=>array("password" => $_POST["npwd"])));
				$back = $change_user->run(true); //Make the password change
				unset($_SESSION["userid"]); //Log them out
				mysqli_query($con, "DELETE FROM `pass_reset` WHERE `key`='".mysqli_real_escape_string($con, $_GET["auth"]) . "'");
				$complete = true;
			}
			else 
			{
				header("Location:./?auth={$_GET["auth"]}&error=105");			
			}
		}
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
		.align{width:800px;display:block;margin:0px auto;margin-bottom:50px;}
		</style>
<?php
		Head::end();
		Body::add_action("$('#error_display').modal()");
		Body::begin();
?>
		<div id="mc_cont">
			<div id="mc">
				<div class="align">
					<h1 class="uk-text-center" style="line-height:1.5em;font-size:3.2em;">Hey there, <?php echo $name; ?></h1>

					<?php if($complete): ?>
						<div class="uk-text-center">We've changed your password. You may now log in.</div>
					<?php else: ?>
						<div class="uk-text-center">
							<i>"You want to change your password, no?<br/>
							Just enter a new one in the box below,<br/>
							And if all goes well, we'll let you know<br/>
							Then you may be good to go!"</i>
						</div> 
						<form name="reset_form" class="uk-text-center" method="POST" action="./?auth=<?php echo $_GET["auth"]; ?>">
							<input type="password" class="uk-align-center uk-width-1-3" placeholder="New Password" id="npwd" name="npwd" />
							<input type="password" class="uk-align-center uk-width-1-3" placeholder="Retype Password" id="nrpwd" name="rnpwd" />
							<input type="submit" class="button_primary blue uk-text-center" value="Reset Password" id="resetbtn" />
						</form>
						
					<?php endif; ?>
					
				</div>
			</div>
		</div>
		<?php
			Body::end();
			HTML::end();
		?>
