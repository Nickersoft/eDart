<?php 
	/* 
	 * Page Name: Home
	 * Purpose: Either the splash page or the feed page, depending on if the user is logged in
	 * Last Updated: 6/5/2014
	 * Signature: Tyler Nickerson
	 * Copyright 2014 eDart
	 *
	 * [Do not remove this header. One MUST be included at the start of every page/script]
	 *
	 */

	//MUST be at the top of every file
	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Include core functionality

	HTML::begin();
	Head::make("eDart Beta", false);

	//If the user is logged in...
	if(isset($_SESSION["userid"]))
	{
		//We run a collection of feed functions
		Body::add_action("pre_feed()");
	}
	else //If not...
	{
		//We run some home functions
		Body::add_action("pre_home()");
	}

		Body::begin();
	
		/* * * HOME PAGE * * */

			//Print the main page//

			//If we aren't logged in, print the home/splash page
			if(!isset($_SESSION["userid"]))
			{

					$location_html   = "<b>Could not detect location</b>";
					$location_string = get_location();
					if(trim($location_string)!="")
					{
						$location_html = "<b>General Location: </b>" . $location_string;
					}
					
					//Print the signup box
					$signup_pnl = <<<EOF
									<div id="supnl">
										<h1>Sign Up</h1>
										<h2>To Get Started!</h2>
											<form method="POST" onsubmit="clearIncomplete(this);" action="/signup/process.php" id="sufrm"> 
												<input style="width:100%;" class="inpt" name="fname" id="fname" autocomplete="off" type="text" placeholder="First Name" /></br>  
												<input style="width:100%;" class="inpt" name="lname" id="lname" autocomplete="off" type="text" placeholder="Last Name" /></br> 
												<input style="width:100%;" class="inpt" name="eaddr" id="eaddr" autocomplete="off" type="text" placeholder=".edu Address" /></br> 
					 
												<div style="position:relative;height:75px;"> 
													<div style="width:48%;float:left;"> 
														<input style="width:100%;" class="inpt" id="pword"  name="pword" placeholder="Password" autocomplete="off" type="password" /> 
													</div> 

													<div style="width:48%;float:right;"> 
														<input style="width:100%"  class="inpt" id="rpword"  name="rpword" placeholder="Retype Password"  autocomplete="off" type="password" /> 
													</div> 
												</div> 
					 
												<div id="geo_location"> 
													$location_html
												</div> 

												<input type="submit" class="gbtn" style="float:left;margin-left:60%; " value="Let's Roll!" /> 
										</form> 
								</div>
EOF;

					echo $signup_pnl;

					//If an error is specified in the GET parameters...
					if(isset($_GET["error"]))
					{

						//Print the error box for any sign up errors

						$error_box = <<<EOL
						<div id="error_display" class="modal fade">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						        <h4 class="modal-title">Could Not Sign Up</h4>
						      </div>
						      <div id="error_body" class="modal-body">
						       	
						      </div>
						      <div class="modal-footer">
						        <button type="button" data-dismiss="modal" class="bbtn">Okay</button>
						      </div>
						    </div>
						  </div>
						</div>
EOL;

						echo $error_box;

						//Print the JavaScript to power the error box
						$error_code = trim($_GET["error"]);
						$error_scripts = <<<WAT
							<script>
								function checkError() {
									var error = $error_code;
									var msg   = "";
									switch(error)
									{
										case 103:
											msg = "This email address is already registered.";
											break;
											
										case 105:
											msg = "Passwords do not match. Please try again.";
											break;
											
										case 104:
											msg = "We're sorry, but you must be a WPI (whip-ee) student to use this site. You must use your WPI email to sign up. We may open our doors to more schools in the future, but for now, goat on or get out.";
											break;
											
										case 401:
											msg = "Please enter all fields before continuing.";
											break;

									}
									document.getElementById('error_body').innerHTML = msg;
									$('#error_display').modal();
								};
							 </script>
WAT;

							echo $error_scripts;
					}	 

					//Get our main welcome HTML
					$main_html = <<<WUT
						<div id="mcont">
							<div id="welcomehdr">For WPI Students, By WPI Students</div> 
							<div id="bdtxt">
								<table style="width:100%;margin:0px;">

									<tr>
										<td><div class="glyphicon glyphicon-plus-sign fronticon"></div></td>
										<td><b>Post</b> items you would like to trade with other students</td>
									</tr>

									<tr>
										<td><div class="glyphicon glyphicon-search fronticon"></div></td>
										<td><b>Search</b> for items you would like to trade</td>
									</tr>

									<tr>
										<td><div class="glyphicon glyphicon-time fronticon"></div></td>
										<td><b>Set up</b> a time and place to meet your partners to make exchanges</td>
									</tr>

									<tr>
										<td><div class="glyphicon glyphicon-home fronticon"></div></td>
										<td><b>Request</b> that your item be returned to you when it is done being used</td>
									</tr>

									<tr>
										<td><div class="glyphicon glyphicon-thumbs-up fronticon"></div></td>
										<td><b>Rank</b> the reliability of other users</td> 
									</tr>
					
								</table>

								<div id="movie_text">
									Learn how it works by clicking 

									<div class="link" onclick="window.open('/video','name','height=360,width=640')">
										here.
									</div>

								</div>
							</div>   
						</div>
WUT;

					echo $main_html; //Print out the HTML

			}
			else //If the user IS logged in...
			{
				/* * * FEED * * */

					include $_SERVER["DOC_ROOT"] . "/scripts/php/html/feed.php"; //Print out the feed HTML
			}
		
			Body::end(); 
			HTML::end();
?>