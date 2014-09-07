<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

//Get info about the given user
$newPerson  = new User(array("action"=>"get", "id"=>$_GET["id"]));
$personInfo = $newPerson->run(true);

try {
	//We're using 'r' for compatibility purposes
	//Get the first user's info
	$r = $personInfo[0];

	$fname 				= $r ["fname"];					  //First Name
	$lname 				= $r ["lname"];					  //Last Name
	$dob 				= $r ["dob"];					  //Date of Birth
	$domail				= $r ["do_mail"];			   	  //Can we email them?
	$gender_index 		= $r ["gender"];				  //Gender
	$bio 				= $r ["bio"];					  //Biography
	$gender 			= Lookup::Gender($gender_index);  //Gender string
	$pronoun 			= Lookup::Pronoun($gender_index); //Pronoun to describe user's gender

	$privacy = json_decode($r["privacy"], true);	//The privacy array
	$privacy = (!$privacy) ? array() : $privacy;

	$rank = json_decode($r["rank"], true);		//The user's rankings
}catch(Exception $e){}
?>
<div id="edit_profile" class="profile_tab_content">
	<div class="edit_parent">
		<div id="change_info" class="edit_section">
			<h3>Basics</h3>
			<input type="text" id="user_fname" name="fname" class="small_text uk-width-2-3" 			placeholder="First Name"	value="<?php echo $fname; ?>"/>
			<input type="text" id="user_lname" name="lname" class="small_text uk-width-2-3" 			placeholder="Last Name"		value="<?php echo $lname; ?>"/>
			<input type="text" id="user_dob"   name="dob"   class="datebox small_text uk-width-2-3" 	placeholder="Date of Birth"	value="<?php echo date("m/d/Y", $dob); ?> "/>

			<textarea id="user_bio" class="small_text uk-width-2-3" name="bio" placeholder="Enter a short bio here."><?php echo $bio; ?></textarea>

			<div class="uk-width-2-3 uk-align-center">
				<select id="user_gender" name="gender" class="chosen-select select small_text" data-placeholder="Gender"/>
					<option></option>
					<?php
						$gender_options = Lookup::Gender();
						foreach($gender_options as $gender_option): ?>
							<option <?php echo ($gender_option["code"]==$gender_index) ? "selected" : ""; ?> ><?php echo $gender_option["text"]; ?></option>
					<?php endforeach; ?>
				</select>		
			</div>
			
			<div class="check uk-text-center">
				<input type="checkbox" id="user_domail" name="domail" <?php echo ($domail == "1") ? "checked" : ""; ?> />Receive emails from eDart
			</div>

			<button class="uk-align-center button_primary blue" onclick="user_send_data();">Save Info</button>
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
			<input type="button" class="uk-align-center button_primary blue" onclick="privacy_send_data();" value="Change Privacy" />
		</div>
		<div id="change_password" class="edit_section">
			<h3>Password</h3>
			<input type="password" id="user_pw"    name="cur_pw" class="uk-width-2-3 small_text" placeholder="Current Password"	  />
			<input type="password" id="user_npw"   name="new_pw" class="uk-width-2-3 small_text" placeholder="New Password"		  />
			<input type="password" id="user_rpw"   name="ret_pw" class="uk-width-2-3 small_text" placeholder="Retype New Password" />

			<button class="uk-align-center button_primary blue" onclick="password_send_data();">Change Password</button>
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
				<input type="submit" class="uk-align-center button_primary blue" value="Close Account" <?php echo $disabled_str; ?>/>
			</form>
		</div>
	</div>
</div>