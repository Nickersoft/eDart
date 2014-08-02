<?php
/* 
 * Page Name: Profile Page
 * Purpose: A user's profile page
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER ["DOC_ROOT"] . "/scripts/php/core.php"; //Import core functionality

//If no user ID is specified, throw a not found page
if (empty($_GET['id'])) 
{
	header ( 'Location:/notfound.php' );
	exit;
}

//Get info about the given user
$newPerson = new User(array("action"=>"get", "id"=>$_GET["id"]));
$personInfo = $newPerson->run(true);

//If the person can't be found, throw a not found page
if($personInfo==404)
{
	header ( 'Location:/notfound.php' );
	exit;
}

//We're using 'r' for compatibility purposes
//Get the first user's info
$r = $personInfo[0];

$pfname = $r ["fname"];			//First Name
$plname = $r ["lname"];			//Last Name
$profpic = $r ["profile_pic"];	//Profile Picture
$jdate = $r ["joindate"];		//Join Date
$dob = $r ["dob"];				//Date of Birth
$geni = $r ["gender"];			//Gender
$bio = $r ["bio"];				//Biography
$gender = "Female";				//Gender (default is female to keep feminists happy)
$pronoun = "She";				//Pronoun to describe user's gender
$lastseen = $r["lastlocation"]; //Last location they logged in
$lastlogin = $r["last_login"];  //Last time they were logged in

$privacy = json_decode($r["privacy"], true);	//The privacy array
$privacy = (!$privacy) ? array() : $privacy;

$rank = json_decode($r["rank"], true);		//The user's rankings

//If the gender is male (0)
if ($geni == "0") 
{
	//Change accordingly
	$gender = "Male";
	$pronoun = "He";
} 
elseif ($geni == "2") //If it's CS
{
	//Change accordingly
	$gender = "Computer Scientist";
	$pronoun = "He/She";
}

//If a load parameter is specified and this is 'image'
if(isset($_GET["load"]) && $_GET["load"]=="image")
{
	//We're just going to print the user's image

	header("Content-type: image/jpg"); //Set JPG image header

	//If they don't have a profile picture
	if(trim($profpic)=="")
	{
		//Use the default one
		$profpic = file_get_contents($_SERVER["DOC_ROOT"]."/img/user_icon_200.png");
	}
	else //If a profile picture is set...
	{
		//If a size is specified...
		if(isset($_GET["size"]))
		{
			//Resize it accordingly
			switch(strtolower(trim($_GET["size"])))
			{
				case "small": //(60x60)
				$profpic = WideImage::load($profpic)->resize(60)->asString('jpg');
				break;
			}
		}	
	}

	echo $profpic; 	//Print the image contents
	exit; 			//Exit
}

//Change the profile picture to use the URL instead
$profpic = "/profile.php?id=".$_GET["id"]."&load=image";

HTML::begin();
Head::make("$pfname $plname", true);
Body::begin(true);
?>
			<style type="text/css">
				.prefix{ display:inline-block; margin-left:25px; vertical-align:top;margin-right:10px; display:inline-block; font-size:18px; }
			</style>

			<div id="profile_container">
				<div id="links">
					<div style="padding: 10px;">
						<h3>Recent Activity</h3>
						
						<br/>
			
						<?php

							//Get this user's recent activity
							$log = getRecentActivity ( $_GET ["id"] );

							//If there is no recent activity
							if (count ( $log ) === 0) 
							{
								//Say so
								echo "<h5>This user has no recent activity to display</h5>";
							} 
							else //If there is recent activity
							{
								//Begin the main container
								$html = "<div style=\"width:100%;white-space:normal;overflow:auto;height:500px;display:block;\"><div>";

								//Loop through each and print it
								for($i = 0; $i < count ( $log ); $i ++) 
								{
									$regdate = date ( "Y-m-d H:i:s", $log[$i]["date"]);
									$reldate = getRelativeDT (time(), $log[$i]["date"]);
									$fulldte = date ( "l, F jS, Y", $log[$i]["date"]);
									$html .= "<div style=\"cursor:default;margin-bottom:10px;\"><a href=\"".$log[$i]["link"] ."\">". $log[$i]["string"] . "</a><div title=\"$fulldte\" style=\"display:inline-block;color:#A4FF95;margin-left:5px;font-size:14px;\"> " . $reldate . " ago</div></div>";
								}

								//Close the container
								$html .= "</div></div>";
				
								echo $html; //Print the recent activity
							}
						?>
					</div>
				</div>

				<div id="linkhold"></div> <!--Placeholder for fixed element. DON'T REMOVE. -->

				<div id="user_profile">
					<div id="myprofile">
						<div style="margin-top:20px;">
							<div id="profpic">
								<img style="width:200px;height:200px;" <?php echo "src=\"$profpic\""; ?> id="pic">
						 	</div>

						 	<h6>
						 		<?php 

						 			echo "$pfname $plname"; 

						 			//If we know when they were last logged in, print it
						 			if($lastlogin)
						 			{
						 				$last_login_str = getRelativeDT(time(), $lastlogin);
							 			echo "<div style=\"display:inline-block;font-size:14px;margin-left:10px;color:dimgray;\">Active $last_login_str ago</div>";
						 			}
						 		?>
						 	</h6>

							
							<div style="padding:1px;">

								<div class="prefix bdy">
									<span>Member #: </span> <?php echo $_GET["id"]; ?> 
								</div>

									<br/>

								<div class="prefix bdy">
									<span>Member Since: </span> <?php echo date ( "F j, Y", $jdate ); ?> 
								</div>

									<br/>
			
								<?php

								/* * * FUNCTION DEFINITIONS * * */

									//Return color string based on number value					
									function getColor($int) {
										$rtrn = "";
										if ($int <= 3) {
											$rtrn = "red";
										} elseif ($int <= 5) {
											$rtrn = "yellow";
										} else {
											$rtrn = "green";
										}
										return $rtrn;
									}

								/* * * LAST LOCATION * * */

									//If we're allowed to show last location...
									if(!in_array("lastlocation", $privacy))	
									{
										//...and it's not empty
										if(trim($lastseen)!="") 
										{
											$lastseen_html = <<<LSN
												<div class="prefix bdy">
													<span>Last Seen Near: </span>$lastseen
												</div>

													<br/>
LSN;
										
											echo $lastseen_html; //Print "last seen near"
										}
									}

								/* * * GENDER * * */

									//If we're allowed to show the user's gender...
									if(!in_array("gender", $privacy))	
									{
										$gender_html = <<<GDR
											<div class="prefix bdy">
												<span>Gender: </span>$gender
											</div>
												<br/>
GDR;
			
										echo $gender_html; //Print the user's gender
									}
			
								/* * * DOB * * */

									//If we're allowed to show the user's age...
									if(!in_array("dob", $privacy))	
									{
										//Calculate their age based on DOB
										$yr = floor ( (abs ( $dob - time () )) / (60 * 60 * 24 * 30.4375 * 12) );

										//If their DOB is specified
										if($dob!=0)
										{
											$dob_html = <<<DOB
												<div class="prefix bdy">
													<span>Age: </span>$yr years old
												</div>
													<br/>
DOB;
											echo $dob_html; //Print their age
										}
									}
			
								/* * * OVERALL RATINGS * * */

									$tots = 0; //The total number of ratings
									$sums = 0; //The sum of all of the ratings
			

									$rating_html = <<<RATE
										<div class="prefix bdy">
											<span style="margin-bottom:5px;">Overall Rating: </span>
RATE;

									//If the user has some ratings
									if (count ( $rank ) != 0) 
									{
										//Loop through each rating
										foreach ( $rank as $key => $value ) {

											//If it's a field (bar), and not a description
											if (trim ( $key ) != "desc") {

												//Get all of the values
												foreach ( $value as $v ) {

													//And add them up
													$sums += intval ( $v + 1 );

													//And increment the total count
													$tots ++;
												}
											}
										}
		
										//We then use this info to calculate the average rating			
										$total = floor ( $sums / $tots );

										//Here's our preprogrammed array of rating descriptions
										$arr = array (
											'Utterly disgraceful!',
											'Horrible!',
											'Pretty bad',
											'Mediocre',
											'Average',
											'Alright, I guess...',
											'Pretty cool',
											'Good',
											'Great',
											'Absolutely amazing!' 
											);

										$rating_html .= $arr[$total-1]; //Use the average to get a preprogrammed description

										//Here, we'll create the rating bar
										
										//Base on our rating, set a background for the bar	
										$background = getColor($total);

										//This will be helpful in accurately displaying the bar
										$total *= 20; 
										
										$rating_bar = <<<RTB
											<div id="rating_bar_cont">
												<div id="rating_bar" style="width:{$total}px;" class="$background"></div>
											</div>
RTB;
										$rating_html .= $rating_bar . "</div>";

									} 
									else //If there are no ratings...
									{
										$rating_html .= "N/A (This user has not yet been rated)"; //say so
									}

									echo $rating_html; //Print out the rating HTML

								/* * * BIOGRAPHY * * */

									//If the user has a bio
									if (trim ( $bio ) != "") 
									{
										$bio_html = <<<BIO
											<br/>
											<div class="prefix bdy">
												<span>About Me: </span>
											</div>
												<br/>
											<div style="height:auto;padding-left:50px;display:block;" id="biotxt" class="bdy">
												$bio
											</div>
												<br/>
BIO;
										echo $bio_html;
									}
							?>
						</div>
					</div>
				</div>
			
			<div id="ucmnthd">User Comments</div>

				<div id="cmntcont">
					<div id="theinnerring">

						<?php

						/* * * USER COMMENTS * * */

							$description_array = $rank["desc"];

							//We'll be using the description array to iterate through all of the keys

							//If there are no reviews
							if(!$rank||count($description_array)==0)
							{
								echo "<div class=\"urvs_subtxt\">This user has no reviews yet!</div>";
							}
							else
							{
								for($v = 0; $v < count($description_array); $v++)
								{
										//-1 so we can accurately get a color for it

										$reli_n = intval($rank["1"][$v]) - 1; //Reliability
										$frie_n = intval($rank["2"][$v]) - 1; //Friendliness
										$resp_n = intval($rank["3"][$v]) - 1; //Responsibility
										$over_n = intval($rank["4"][$v]) - 1; //Overall

										$reli_c = getColor($reli_n); //Color for reliability
										$frie_c = getColor($frie_n); //Color for friendliness
										$resp_c = getColor($resp_n); //Color for responsibility
										$over_c = getColor($over_n); //Color for overall

										//Auto generated description if none was given
										$auto_desc = $pronoun . "'s " . strtolower ( $arr [(($reli_n + $frie_n + $resp_n + $over_n) / 4)] );

										//Use this description in case there was no given description
										if (trim($description_array[$v]) != "") 
										{
											$auto_desc = $description_array[$v];
										}

										//Generate the ranking string
										$rank_str = <<<RANK
											<div class="cmnt">

												<div class="cmt">
													<i>"$auto_desc"</i>
												</div>
						
												<div class="iconset">
													<img title="Rating: $arr[$reli_n]" class="icon" src="./img/icon/$reli_c.png">Reliability
													<img title="Rating: $arr[$frie_n]" class="icon" src="./img/icon/$frie_c.png">Friendliness
													<img title="Rating: $arr[$resp_n]" class="icon" src="./img/icon/$resp_c.png">Responsibility
													<img title="Rating: $arr[$over_n]" class="icon" src="./img/icon/$over_c.png">Overall Experience
												</div>

											</div>
RANK;
										//Print it
										echo $rank_str;
									
								}
							}
						?>
					</div>			
				</div>
			</div>
		</div>
			
<?php 
Body::end();
HTML::end();
?>