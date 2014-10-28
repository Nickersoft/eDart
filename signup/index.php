<?php 
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

HTML::begin();
Head::begin("Sign Up");
Body::add_action("pre_home()");
Body::begin(true, true);
?>

<div id="home_container">
	<div class="layout-1200 uk-container uk-container-center">
			<div id="signup_panel" class="uk-width-1-1 uk-border-rounded uk-container-center uk-text-center">
				<h1>Join the Trading Revolution</h1>
					<form method="POST" onsubmit="clearIncomplete(this);" action="/signup/process.php" id="signup_form">
						<div class="uk-width-medium-1-3 uk-container-center">
							<input class="uk-width-1-1 text_medium" name="fname"  id="fname"  autocomplete="off" type="text"     placeholder="First Name" /></br>
							<input class="uk-width-1-1 text_medium" name="lname"  id="lname"  autocomplete="off" type="text"     placeholder="Last Name" /></br>
							<input class="uk-width-1-1 text_medium" name="eaddr"  id="eaddr"  autocomplete="off" type="text"     placeholder=".edu Address" /></br>
							<input class="uk-width-1-1 text_medium" name="pword"  id="pword"  autocomplete="off" type="password" placeholder="Password"  />
							<input class="uk-width-1-1 text_medium" name="rpword" id="rpword" autocomplete="off" type="password" placeholder="Retype Password"  />
							
	
							<?php
								$location_html   = "<b>Could not detect location</b>";
								$location_array  = get_location();
								try{
									if((trim($location_array["properties"]["city"])!="")&&(trim($location_array["properties"]["region"])!=""))
									{
										$location_html = "<b>General Location: </b>{$location_array["properties"]["city"]}, {$location_array["properties"]["region"]}";
									}
								}catch(Exception $e){}
							?>
							
							<div id="geo_location">
								<?php echo $location_html; ?>
							</div>
	
							<input type="submit" id="signup_submit" class="reset_margin uk-width-1-1 uk-align-center button_primary green" value="Let's Roll!" />
							<span>* By click the above button, you are certifing that you are at least 18 years of age</span>
					</form>
			</div>
		</div>
	</div>
</div>

<?php 
Body::end();
HTML::end();
?>