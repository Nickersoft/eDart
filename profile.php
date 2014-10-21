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

try {
	//We're using 'r' for compatibility purposes
	//Get the first user's info
	$r = $personInfo[0];
	
	$fname 				= $r ["fname"];								 //First Name
	$lname 				= $r ["lname"];								 //Last Name
	$profile_pic 		= $r ["profile_pic"];						 //Profile picture
	$join_date 			= $r ["join_date"];							 //Join Date
	$dob 				= $r ["dob"];								 //Date of Birth
	$gender_index 		= $r ["gender"];							 //Gender
	$gender 			= Lookup::Gender($gender_index);			 //Gender string
	$pronoun 			= Lookup::Pronoun($gender_index); 			//Pronoun to describe user's gender
	$lastlogin 			= $r["last_login"];  			 			//Last time they were logged in
	$lastseen 			= json_decode($r["last_location"], true); 	//Last location they logged in
	
	$privacy = json_decode($r["privacy"], true);	//The privacy array
	$privacy = (!$privacy) ? array() : $privacy;
	
	//If a load parameter is specified and this is 'image'
	if(isset($_GET["load"]) && $_GET["load"]=="image")
	{
		//We're just going to print the user's image
	
		header("Content-type: image/jpg"); //Set JPG image header
	
		//If they don't have a profile picture
		if((trim($profile_pic)=="")||(count($personInfo)==0))
		{
			//Use the default one
			$profile_pic = file_get_contents($_SERVER["DOC_ROOT"]."/img/user_icon_200.png");
		}
		
		//If a size is specified...
		if(isset($_GET["filter"]))
		{
			//Resize it accordingly
			switch(strtolower(trim($_GET["filter"])))
			{
				case "blur": //Blur the image
					$feat_data_binary_blur	= WideImage::loadFromString($profile_pic)->applyFilter(IMG_FILTER_GAUSSIAN_BLUR);
					for($i = 0; $i < 50; $i++)
					{
					$get_cur_blur 			= $feat_data_binary_blur->asString('jpg');
							$feat_data_binary_blur	= WideImage::loadFromString($get_cur_blur)->applyFilter(IMG_FILTER_GAUSSIAN_BLUR);
					}
						
					$profile_pic = $feat_data_binary_blur->asString('jpg');
					break;
			}
		}
			
		//If a size is specified...
		if(isset($_GET["size"]))
		{
			//Resize it accordingly
			switch(strtolower(trim($_GET["size"])))
			{
				case "small": //(60x60)
					$profile_pic = WideImage::load($profile_pic)->resize(50)->asString('jpg');
					break;
				
				case "huge": //(60x60)
					$profile_pic = WideImage::load($profile_pic)->resize(1000)->asString('jpg');
					break;
			}
		}
		

	
	
		echo $profile_pic; 	//Print the image contents
		exit; 				//Exit
	}
	
	//Change the profile picture to use the URL instead
	$profile_pic = "/profile.php?id=".$_GET["id"]."&load=image";
} catch(Exception $e) {
	//If the person can't be found, throw a not found page
	if($personInfo==404)
	{
		header ( 'Location:/notfound.php' );
		exit;
	}
}

HTML::begin();
Head::make("$fname $lname", true);
Body::begin(true, true);
?>
<style type="text/css">
.prefix{ display:inline-block; margin-left:25px; vertical-align:top;margin-right:10px; display:inline-block; font-size:18px; }
</style>

<div id="profile_container">
	<div id="profile">
	
		<div id="cover" style="background-image:url('/profile.php?id=<?php echo $_GET["id"]; ?>&load=image&filter=blur&size=huge')">
			<div class="layout uk-container-center">
				<div class="uk-grid">
					<div class="uk-width-1-1 reset_padding">
						<div id="picture">
							<?php if(isset($_SESSION["userid"])&&$_SESSION["userid"]==$_GET["id"]): ?><div onclick="pp_change_begin();" class="uk-width-1-1 uk-height-1-1 overlay">Click to Change</div><?php endif; ?>
							<img src="/profile.php?id=<?php echo $_GET['id']; ?>&load=image">
						</div>
					</div>
					<div class="uk-width-1-1 reset_padding">
						<div id="header">
							<h1><?php echo "$fname $lname"; ?></h1><br/>
							<?php echo (trim($lastlogin!="")) ? "Active " . getRelativeDT(time(), $lastlogin) . " ago" : ""; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<h1 id="title">Recent Activity</h1>
		
		<div class="layout uk-container-center">
			<div class="uk-grid">
				
				<!-- USER INFO -->
				<div id="left" class="uk-width-1-4 uk-hidden-small">
					<div id="info">
						<ul>
							<li><span class='uk-icon-bolt'></span>eDart user #<?php echo $_GET["id"]; ?></li>
							<li><span class='uk-icon-calendar'></span>Joined on <?php echo date("m/d/Y", $join_date); ?></li>
							<?php echo (!in_array("last_location", $privacy)&&(count($lastseen)!=0)) ? "<li><span class='uk-icon-location-arrow'></span>Last seen near {$lastseen["properties"]["city"]}</li>" : ""; ?>
							<?php echo (!in_array("gender", $privacy)&&$gender_index!=0) ? "<li><span class='uk-icon-user'></span>$gender</li>" : ""; ?>
							<?php echo (!in_array("dob", $privacy)&&$dob!=0) ? "<li><span class='uk-icon-birthday-cake'></span>" . getRelativeDT(time(), $dob) . " old</li>" : ""; ?>
							<li><span class='uk-icon-institution'></span>Attends WPI</li>
						</ul>
	
						<?php if(isset($_SESSION["userid"])&&$_SESSION["userid"]==$_GET["id"]): ?>
						
							<div class="uk-text-center">
								<input type="button" id="edit_button" value="Edit Profile" data-link="edit_profile" class="uk-align-center small_text button_primary green" value="Edit" />
							</div>
							
							<div class="hidden">
								<form id="user_image_upload_form" method="post" action="/scripts/php/widget/me/crop.php" enctype="multipart/form-data">
									<input style="z-index:-200" accept="image/*" onchange="document.getElementById('user_image_upload_form').submit();" type="file" name="pp_upload" id="user_image_upload" />
								</form>
							</div>
	
						<?php endif; ?>
	
	
					</div>
				</div>
	
				<div class="uk-width-medium-3-4 uk-width-small-1-1">
					<div class="uk-grid uk-grid-preserve">
						<!-- MAIN CONTENT -->
						<div class="uk-width-medium-7-10 uk-width-small-1-1">
							<div id="post_container">
		
							<?php
								include_once $_SERVER["DOC_ROOT"] . "/scripts/php/widget/profile/recent_activity.php";
								include_once $_SERVER["DOC_ROOT"] . "/scripts/php/widget/profile/item.php";
								include_once $_SERVER["DOC_ROOT"] . "/scripts/php/widget/profile/review.php";
								include_once $_SERVER["DOC_ROOT"] . "/scripts/php/widget/profile/edit.php";
							?>
							
							</div>
						</div>
					
			
						<div id="right" class="uk-width-3-10 uk-hidden-small">
							<ul>
								<li class="profile_tab active" value="Recent Activity" data-link="recent_activity"><span class="uk-icon-bars"></span>Recent Activity</li>
								<li class="profile_tab" value="Item Inventory" data-link="item_board"><span class="uk-icon-cubes"></span>Item Inventory</li>
								<li class="profile_tab" value="User Reviews" data-link="user_reviews" ><span class="uk-icon-star"></span>User Reviews</li>
							</ul>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
</div>
</div>

<?php
Body::end();
HTML::end();
?>
