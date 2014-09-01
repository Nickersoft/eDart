<?php
	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
	$user_info	= array();

	//If we're logged in, get the user info
	if(isset($_SESSION["userid"]))
	{
		$user_id 	= $_SESSION["userid"];
		$user_obj 	= new User(array("action"=>"get", "id"=>$user_id));
		$user_info 	= $user_obj->run(true);
		$user_info  = $user_info[0];

		//And include the add wizard
		include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/add_wizard.php";
	}

	//Let's count how many site members we have

	//Connect to MySQL
	$mysqli_obj = mysqli_connect(host(), username(), password(), mainDb());

	//Get the user count (not possible through API)
	$query = mysqli_query($mysqli_obj, "SELECT COUNT(*) AS count FROM `usr`");

	//Get the number of users
	$user_count = mysqli_fetch_array($query);
	$user_count = $user_count[0];

	//If a notification brought us to this page
	if(isset($_GET["ref"]))
	{
		$query_string  = "";
		$ref = strtolower(trim($_GET["ref"]));

		switch($ref[0])
		{
			case "n": //(NOTIFY)
				$query_string = "UPDATE `notify` SET `read`='1' WHERE `usr`='{$_SESSION["userid"]}' AND `id`='" . substr($ref, 1) . "'";
				break;
		}

		mysqli_query($mysqli_obj, $query_string);
	}

	mysqli_close($mysqli_obj);
?>
<div id="fb-root"></div>
<div id="banner_placeholder">
	<div id="banner">
		<div id="inner">

			<?php if(getcwd()!==$_SERVER["DOC_ROOT"]): ?>
				<a onclick="window.location='/';" class="button_back">
					<?php echo (isset($_SESSION["userid"])) ? "Return to Feed" : "Return Home"; ?>
				</a>
			<?php else:  ?>
					<div id="logo">
						<a href="/">
							<img src="/img/logo.png">
						</a>
					</div>
			<?php endif; ?>

			<?php if(isset($_SESSION["userid"])):
					$user_call = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
					$user_info = $user_call->run(true);
					$user_info = $user_info[0];
					if($user_info["status"]==2):
			?>

				<form method="GET" id="hfrm" action="/search.php">
					<input name="keyword" type="search" maxlength="40" onkeyup= "if(event.keyCode==13){document.getElementById('hfrm').submit();}" id="headsearch" autocomplete="off" placeholder="Click here to start your search" />
				</form>

				<a href="/profile.php?id=<?php echo $_SESSION["userid"]; ?>">
					<div id="minippic" style="background:url('/me/picture/?size=small') center center;">
					</div>
				</a>

				<div id='infobox'>
					<div id="tooltips">
						<?php include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/menus.php"; ?>
					</div>
					<a href="/profile.php?id=<?php echo $_SESSION["userid"]; ?>"><?php echo "{$user_info["fname"]} {$user_info["lname"]}"; ?></a><br/>
					<div id="user_options" class="menu_link" onclick="display_menu('#options_menu', this);">
						Options
						<div id="options_menu" class="menu">
							<div class="tip"></div>
							<ul>
								<li onclick="facebook_login();">Invite Friends</li>
								<li><a href="/changes" target="_blank">View Changelog</a></li>
								<li><a style="color:tomato" href="/bugs">Report a Bug</a></li>
								<div class="divide"></div>
								<li onclick='logout();'>Logout</li>
							</ul>
						</div>
					</div>
				</div>
			<?php else: ?>
				<a onclick="window.location='/validate/'" class="button_validate small_text">Validate Account</a>
			<?php endif;
				else: //<div id="subtxt">Now with echo $user_count; members worldwide.</div> ?>
					<table id="loginbx">
						<tr>
							<td id="cntlgnm"></td>

							<td>
								<input name="leaddr" type="text" id="leaddr" class="inpt text_medium" placeholder="Email Address" autocomplete="off" onkeydown="return_login(event, 'loginarrow');" />
							</td>

							<td>
								<input class="inpt text_medium" name="lpword" id="lpword" placeholder="Password" data-dummy="dlpword" autocomplete="off" type="password" onkeydown="return_login(event, 'loginarrow');" />
							</td>

							<td>
								<div id="loginarrow" title="Login to eDart" class="loginbtn fa fa-caret-right" onclick="login(document.getElementById('leaddr').value, document.getElementById('lpword').value,'',function(){print_login_error();});"></div>
							</td>

							<td>
								<a id="loginforgot" href="/forgot" title="Forgot Password?">Forgot?</a>
							</td>
						</tr>
					</table>
			<?php endif; ?>
		</div>
	</div>
</div>
