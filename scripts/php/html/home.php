<?php include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; ?>
<div id="home_container">
	<div id="inner">
		<div id="inner_left">
			<h1>Trade Anything.</h1>
			<table>
				<tr>
					<td><div class="fa fa-plus-circle fronticon"></div></td>
					<td><span>Post</span> items you no longer need</td>
				</tr>
				<tr>
					<td><div class="fa fa-search fronticon"></div></td>
					<td><span>Search</span> for items the items you want</td>
				</tr>
				<tr>
					<td><div class="fa fa-cube fronticon"></div></td>
					<td><span>Offer</span> your item in exchange for others</td>
				</tr>
				<tr>
					<td><div class="fa fa-clock-o fronticon"></div></td>
					<td><span>Set Up</span> a time and place to trade items</td>
				</tr>
				<tr>
					<td><div class="fa fa-thumbs-up fronticon"></div></td>
					<td><span>Rank</span> the reliability of other users</td>
				</tr>
				<tr>
					<td><div class="fa fa-users fronticon"></div></td>
					<td><span>Safely</span> trade with members of your class</td>
				</tr>

			</table>
		</div>
		<div id="home_arrow"><i class="fa fa-arrow-down"></i></div>
		<div id="inner_right">
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
		</div>
	</div>
</div>
