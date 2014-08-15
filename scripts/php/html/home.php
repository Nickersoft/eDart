<?php include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; ?>
<div id="home_container">
	<div class="layout-978 uk-container-center">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<h1>Trade Anything.</h1>
			</div>
			<div class="uk-width-1-2">
				<ul>
					<li> 
						<i class="uk-icon-plus-circle uk-float-left"></i>
						<span>Post</span> items you no longer need
					</li>
					<li>
						<i class="uk-icon-search uk-float-left"></i>
						<span>Search</span> for items the items you want
					</li>
					<li>
						<i class="uk-icon-cube uk-float-left"></i>
						<span>Offer</span> your item in exchange for others
					</li>
					<li>
						<i class="uk-icon-clock-o uk-float-left"></i>
						<span>Set Up</span> a time and place to trade items
					</li>
					<li>
						<i class="uk-icon-thumbs-up uk-float-left"></i>
						<span>Rank</span> the reliability of other users
					</li>
					<li>
						<i class="uk-icon-users uk-float-left"></i>
						<span>Safely</span> trade with members of your class
					</li>
				</ul>
			</div>
			<div class="uk-width-1-2">
				<ul class="uk-width-1-1 button_list uk-align-center">
					<li><a id="signup_submit" class="button_primary green important fill_x" href="/signup">Sign Up</a></li>
					<li><a id="signup_submit" class="button_primary blue important fill_x">View the Kickstarter</a></li>
					<li><a id="signup_submit" class="button_primary red important fill_x">Join the Waitlist</a></li>
				</ul>
				
			<!-- 
				<div id="signup">
					<h1>Sign Up.</h1>
						<form method="POST" onsubmit="clearIncomplete(this);" action="/signup/process.php" id="signup_form">
							<input class="inpt" name="fname"  id="fname"  autocomplete="off" type="text"     placeholder="First Name" /></br>
							<input class="inpt" name="lname"  id="lname"  autocomplete="off" type="text"     placeholder="Last Name" /></br>
							<input class="inpt" name="eaddr"  id="eaddr"  autocomplete="off" type="text"     placeholder=".edu Address" /></br>
	
							<div id="passwords">
								<input class="inpt" name="pword"  id="pword"  autocomplete="off" type="password" placeholder="Password"  />
								<input class="inpt" name="rpword" id="rpword" autocomplete="off" type="password" placeholder="Retype Password"  />
							</div>
	
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
	
							<input type="submit" id="signup_submit" class="gbtn" value="Let's Roll!" />
							<span>By click the above button, you are certifing that you are at least 18 years of age</span>
					</form>
				</div>
				 -->
			</div>
		</div>
	</div>
</div>
