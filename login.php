<?php 
/*
 * Page Name: Generic Login Page
* Purpose: Let the user log in a possibly redirect to a target landing page
* Last Updated: 8/20/2014
* Signature: Tyler Nickerson
* Copyright 2014 eDart
*
* [Do not remove this header. One MUST be included at the start of every page/script]
*
*/

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

HTML::begin();
Head::make("Login");
Body::add_action("pre_home()");
Body::begin(true, true);
?>

<div id="home_container">
	<div class="layout-978 uk-container uk-container-center">
			<div id="signup_panel" class="uk-width-1-1 uk-border-rounded uk-container-center uk-text-center">
				<h1>Login to eDart</h1>
					<form method="POST" onsubmit="clearIncomplete(this);" action="/signup/process.php" id="signup_form">
						<div class="uk-width-medium-1-3 uk-container-center">
							<input class="uk-width-1-1 text_medium"	name="leaddr" id="lpeaddr" autocomplete="off" type="text"     placeholder="Email Address" onkeydown="return_login(event, 'loginbtn');" />
							<input class="uk-width-1-1 text_medium"	name="lpword" id="lppword" autocomplete="off" type="password" placeholder="Password" 	 onkeydown="return_login(event, 'loginbtn');" />
										
							<?php
								$con = mysqli_connect(host(), username(), password(), mainDb());

								$rdr = ""; //The default place we'll redirect to

								//If we're set to redirect
								if(isset($_GET["redirect"])&&trim($_GET["redirect"]!=""))
								{
									$rdr = $_GET["redirect"]; //Change the redirect location
								}
							?>
							
							<input type="button" style="margin-top:10px;" class="uk-width-1-1 button_primary green" id="loginbtn" onclick="login(document.getElementById('lpeaddr').value,document.getElementById('lppword').value, '<?php echo $rdr; ?>',function(){});" value="Let's Go!" />
					</form>
			</div>
		</div>
	</div>
</div>

<?php 
Body::end();
HTML::end();
?>

