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
$newPerson  = new User(array("action"=>"get", "id"=>$_GET["id"]));
$personInfo = $newPerson->run(true);

$item_call   = new Item(array("action"=>"get", "filter"=>array("usr"=>$_GET["id"])));
$user_items  = $item_call->run(true);

//If the person can't be found, throw a not found page
if($personInfo==404)
{
	header ( 'Location:/notfound.php' );
	exit;
}

//We're using 'r' for compatibility purposes
//Get the first user's info
$r = $personInfo[0];

$fname 				= $r ["fname"];					 //First Name
$lname 				= $r ["lname"];					 //Last Name
$profile_pic 		= $r ["profile_pic"];			 //Join Date
$join_date 			= $r ["join_date"];				 //Join Date
$dob 				= $r ["dob"];					 //Date of Birth
$domail				= $r ["do_mail"];
$gender_index 		= $r ["gender"];				 //Gender
$bio 				= $r ["bio"];					 //Biography
$gender 			= Lookup::Gender($gender_index);	 //Gender string
$pronoun 			= Lookup::Pronoun($gender_index); //Pronoun to describe user's gender
$lastlogin 			= $r["last_login"];  			 //Last time they were logged in
$lastseen 			= json_decode($r["last_location"], true); 	//Last location they logged in

$privacy = json_decode($r["privacy"], true);	//The privacy array
$privacy = (!$privacy) ? array() : $privacy;

$rank = json_decode($r["rank"], true);		//The user's rankings

//If a load parameter is specified and this is 'image'
if(isset($_GET["load"]) && $_GET["load"]=="image")
{
	//We're just going to print the user's image

	header("Content-type: image/jpg"); //Set JPG image header

	//If they don't have a profile picture
	if(trim($profile_pic)=="")
	{
		//Use the default one
		$profile_pic = file_get_contents($_SERVER["DOC_ROOT"]."/img/user_icon_200.png");
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
				$profile_pic = WideImage::load($profile_pic)->resize(60)->asString('jpg');
				break;
			}
		}
	}

	echo $profile_pic; 	//Print the image contents
	exit; 			//Exit
}

//Change the profile picture to use the URL instead
$profile_pic = "/profile.php?id=".$_GET["id"]."&load=image";

HTML::begin();
Head::make("$fname $lname", true);
Body::begin();
?>
<style type="text/css">
.prefix{ display:inline-block; margin-left:25px; vertical-align:top;margin-right:10px; display:inline-block; font-size:18px; }
</style>

<div id="profile_container">
	<div id="profile">
		<div id="cover"><img src="/img/bg.jpg"></div>
		<div id="header">
			<div id="text">
				<h1><?php echo "$fname $lname"; ?></h1>
				<?php echo (trim($lastlogin!="")) ? "Active " . getRelativeDT(time(), $lastlogin) . " ago" : ""; ?>
			</div>
		</div>
		<h1 id="title">Recent Activity</h1>
		<div id="content">
			<!-- USER INFO -->
			<div id="left">
				<div id="picture">
					<?php if(isset($_SESSION["userid"])&&$_SESSION["userid"]==$_GET["id"]): ?><div onclick="pp_change_begin();" class="overlay">Click to Change</div><?php endif; ?>
					<img src="/profile.php?id=<?php echo $_GET['id']; ?>&load=image">
				</div>
				<div id="info">
					<ul>
						<li><span class='fa fa-bolt'></span>eDart user #<?php echo $_GET["id"]; ?></li>
						<li><span class='fa fa-calendar'></span>Joined on <?php echo date("m/d/Y", $join_date); ?></li>
						<?php echo (!in_array("last_location", $privacy)&&(count($lastseen)!=0)) ? "<li><span class='fa fa-location-arrow'></span>Last seen near {$lastseen["properties"]["city"]}</li>" : ""; ?>
						<?php echo (!in_array("gender", $privacy)&&$gender_index!=0) ? "<li><span class='fa fa-user'></span>$gender</li>" : ""; ?>
						<?php echo (!in_array("dob", $privacy)&&$dob!=0) ? "<li><span class='fa fa-child'></span>" . getRelativeDT(time(), $dob) . " old</li>" : ""; ?>
						<li><span class='fa fa-institution'></span>Attends WPI</li>
					</ul>

					<?php if(isset($_SESSION["userid"])&&$_SESSION["userid"]==$_GET["id"]): ?>
						<input type="button" id="edit_button" value="Edit Profile" data-link="edit_profile" class="small_text center gbtn" value="Edit" />

						<div class="hidden">
							<form id="user_image_upload_form" method="post" action="/scripts/php/widget/me/crop.php" enctype="multipart/form-data">
								<input style="z-index:-200" accept="image/*" onchange="document.getElementById('user_image_upload_form').submit();" type="file" name="pp_upload" id="user_image_upload" />
							</form>
						</div>

					<?php endif; ?>


				</div>
			</div>

			<!-- MAIN CONTENT -->
			<div class="group">
				<div id="center">
					<div id="post_container">
						<div id="recent_activity" class="profile_tab_content">
							<?php
							$log = getRecentActivity($_GET ["id"]);
							if(count($log)===0): ?>
							<h5>This user has no recent activity to display</h5>
						<?php else: ?>
							<?php	for($i = 0; $i < count ( $log ); $i ++):
								$regdate = date ( "Y-m-d H:i:s", $log[$i]["date"]);
								$reldate = getRelativeDT (time(), $log[$i]["date"]);
								$fulldte = date ( "l, F jS, Y", $log[$i]["date"]); ?>
								<div class="post">
									<div title="<?php echo $fulldte; ?>" class="date"><?php echo $reldate; ?> ago</div>
									<a href="<?php echo $log[$i]["link"]; ?>"><?php echo $log[$i]["string"]; ?></a>
								</div>
							<?php    endfor; ?>
						<?php endif; ?>
					</div>

					<div id="item_board" class="profile_tab_content">
						<?php if(count($user_items)==0): ?>
							<h5>This user currently has no items</h5>
						<?php else: ?>
							<?php	foreach($user_items as $item): ?>
								<div class="item" onclick="//window.location='/view.php?itemid=<?php echo $item["id"]; ?>&userid=<?php echo $item["usr"]; ?>';">
									<img class="picture" src="/imageviewer/?id=<?php echo $item["id"]; ?>&size=thumbnail">
									<div class="info">
										<h2><?php echo $item["name"]; ?></h2>
										<p><?php echo $item["description"]; ?></p>
										<ul>
											<li>Posted: <?php echo date("m/d/y", $item["adddate"]); ?></li>
											<li><div class="divide"></div></li>
											<li><?php echo ($item["expiration"]<time()) ? "Expired" : "Expires: " . date("m/d/y", $item["expiration"]); ?></li>
											<li><div class="divide"></div></li>
											<li><?php echo count(json_decode($item["offers"], true)); ?> offers</li>
										</ul>
									</div>
									<?php if(isset($_SESSION["userid"])&&($_SESSION["userid"]==$_GET["id"])): ?>
										<div class="controls">
											<?php	if($item["status"]==1): ?>
														<button class="edit_button" title="Edit Item" data-id="<?php echo $item["id"]; ?>" ><span class="fa fa-pencil"></span></button>
														<button class="delete_button" title="Delete Item" data-id="<?php echo $item["id"]; ?>"><span class="fa fa-times"></span></button>
											<?php   else: ?>
														<button style="height:100%;" title="View Exchange" class="exchange_button"><span class="fa fa-eye"></span></button>
											<?php   endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php    endforeach; ?>
						<?php  endif; ?>
					</div>

					<div id="user_reviews" class="profile_tab_content">
						<?php if(count($rank)==0): ?>
							<h5>This user currently has no user reviews</h5>
						<?php else:
							foreach($rank as $ranking):
								$total = 0;
								foreach($ranking["points"] as $point)
								{
									$total += intval($point);
								}
								$total /= 4;
								$total /= 2;
								$total = round($total);
								?>
								<div class="review">
									<div class="info">
										<div class="rating">
											<?php for($i = 0; $i < 5; $i++): ?>
												<span class="fa <?php echo ($i <= $total) ? 'fa-star' : 'fa-star-o' ?>"></span>
											<?php endfor; ?>
										</div>
										<?php echo (trim($ranking["description"])!="") ? "&ldquo;{$ranking["description"]}&rdquo;" : ""; ?></p>
										<ul>
											<li>Reliability: <?php echo $ranking["points"][0]; ?></li>
											<li><div class="divide"></div></li>
											<li>Friendliness: <?php echo $ranking["points"][1]; ?></li>
											<li><div class="divide"></div></li>
											<li>Consistency: <?php echo $ranking["points"][2]; ?></li>
											<li><div class="divide"></div></li>
											<li>Overall: <?php echo $ranking["points"][3]; ?></li>
										</ul>
									</div>
								</div>
							<?php endforeach;
						endif; ?>
					</div>

					<div id="edit_profile" class="profile_tab_content">
						<div class="edit_parent">
							<div id="change_info" class="edit_section">
								<h3>Basics</h3>
								<input type="text" id="user_fname" name="fname" class="small_text" 			placeholder="First Name"	value="<?php echo $fname; ?>"/>
								<input type="text" id="user_lname" name="lname" class="small_text" 			placeholder="Last Name"		value="<?php echo $lname; ?>"/>
								<input type="text" id="user_dob"   name="dob"   class="datebox small_text" 	placeholder="Date of Birth"	value="<?php echo date("m/d/Y", $dob); ?> "/>

								<textarea id="user_bio" class="small_text" name="bio" placeholder="Enter a short bio here."><?php echo $bio; ?></textarea>

								<select id="user_gender" name="gender" class="chosen-select select small_text" data-placeholder="Gender"/>
									<option></option>
									<?php
										$gender_options = Lookup::Gender();
										foreach($gender_options as $gender_option): ?>
											<option <?php echo ($gender_option["code"]==$gender_index) ? "selected" : ""; ?> ><?php echo $gender_option["text"]; ?></option>
									<?php endforeach; ?>
								</select>

								<div class="check">
									<input type="checkbox" id="user_domail" name="domail" <?php echo ($domail == "1") ? "checked" : ""; ?> />Receive emails from eDart
								</div>

								<button class="bbtn" onclick="user_send_data();">Save Info</button>
							</div>
							<div id="change_privacy" class="edit_section">
								<h3>Privacy</h3>
								<p>Select any information below you do not wish to display on your public profile.</p>
								<ul id="privacy_checkboxes">
									<li>
										<input type="checkbox" name="gender" id="privacy_gender"  <?php echo (!in_array("gender", $privacy)) ? "checked" : ""; ?> /> Gender
									</li>

									<li>
										<input type="checkbox" name="dob" id="privacy_age" <?php echo (!in_array("dob", $privacy)) ? "checked" : ""; ?> /> Age
									</li>

									<li>
										<input type="checkbox" name="last_location" id="privacy_location"  <?php echo (!in_array("last_location", $privacy)) ? "checked" : ""; ?> /> Last Location (Approximate)
									</li>
								</ul>
								<input type="button" class="bbtn" onclick="privacy_send_data();" value="Change Privacy" />
							</div>
							<div id="change_password" class="edit_section">
								<h3>Password</h3>
								<input type="password" id="user_pw"    name="cur_pw" class="small_text" placeholder="Current Password"	  />
								<input type="password" id="user_npw"   name="new_pw" class="small_text" placeholder="New Password"		  />
								<input type="password" id="user_rpw"   name="ret_pw" class="small_text" placeholder="Retype New Password" />

								<button class="bbtn" onclick="password_send_data();">Change Password</button>
							</div>
							<div id="delete_account" class="edit_section">
								<h3>Delete Account</h3>
								<p>
									Leaving so soon? But you just got here! Oh alright... if you want to leave us, that's your call.
									Click the button below to permanently close your account.
								</p>
								<p>
									If the button is grayed out,
									it means you have an item/items currently out and we're not going to let you bail on your partner.
									Once your exchanges are all complete, <i>then</i> you can close your account.
								</p>

								<?php   $disabled_str = "";
										foreach($user_items as $item)
										{
											if(intval($item["status"])!=1)
											{
												$disabled_str = "disabled";
											}
										}
								?>
								<form id="delete_account_form" method="post" action="/scripts/php/form/me/close_account.php">
									<input type="hidden" name="confirm" value="del" />
									<input type="submit" class="bbtn" value="Close Account" <?php echo $disabled_str; ?>/>
								</form>
							</div>

					</DIV>
					</div>

				</div>
			</div>

			<div id="right">
				<ul>
					<li class="profile_tab active" value="Recent Activity" data-link="recent_activity"><span class="fa fa-bars"></span>Recent Activity</li>
					<li class="profile_tab" value="Item Inventory" data-link="item_board"><span class="fa fa-cubes"></span>Item Inventory</li>
					<li class="profile_tab" value="User Reviews" data-link="user_reviews" ><span class="fa fa-star"></span>User Reviews</li>
				</ul>
			</div>
		</div>
	</div>
</div>
</div>

<?php
Body::end();
HTML::end();
?>
