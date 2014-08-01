<?php
/* 
 * Page Name: Printing Functions
 * Purpose: A collection of functions for printing the same code on multiple pages
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Import core functionality

$rt = "/"; //This is our root.

/*  
 *	  Name: check_version
 * Purpose: Checks the browser version and redirects if neccessary
 * Returns: Void
 */
function check_version()
{
	//Get the current user agent
	$ua = $_SERVER["HTTP_USER_AGENT"]; 

	//Split the HTTP user agent by spaces
	$brow = explode(" ", strtolower($ua)); 

	//Determines whether the site will pass the browser check
	$pass = true; 

	//Iterate through each part of the user agent
	for($i = 0 ; $i<=count($brow)-1; $i++) 
	{
		//Current 'word' in the user agent array
		$b = $brow[$i]; 

		//Split it by slashes
		$brk = explode("/", $b); 

		//If it talks about Google Chrome...
		if(in_string($b, "chrome")) 
		{
			//This is generic version checking code that appears for every browser
			$dots = explode('.',$brk[1]);
			$v = intval($dots[0]);

			//If the browser is Google 28 or less
			if($v<29)
			{
				//Go to error page
				header("Location:/norun.php");
			}

			break 1; //Escape the if statement
		}
		//If it talks about Firefox...
		else if (in_string($b, "firefox"))
		{
			$dots = explode('.',$brk[1]);
			$v = intval($dots[0]);

			//If it is Firefox 22 or less...
			if($v<23)
			{
				//Go to error page
				header("Location:/norun.php");
			}

			break 1;
		}
		//If it talks about Opera...
		else if (in_string($b, "opera"))
		{
			$dots = explode('.',$brk[1]);
			$v = intval($dots[0]);

			//If it's Opera 11 or less...
			if($v<9)
			{
				//Go to error page
				header("Location:/norun.php");
			}

			break 1;
		}
		//If it talks about Safari...
		else if (in_string($b, "safari"))
		{
			$dots = explode('.',$brk[1]);
			$v = intval($dots[0]);

			//If it's Safari 4 or less...
			if($v<5)
			{
				//Go to error page
				header("Location:/norun.php");
			}

			break 1;
		}
		//If it talks about (God forbid) Internet Explorer...
		else if (in_string($b, "msie"))
		{
			$dots = explode('.',$brow[$i+1]);
			$v = intval($dots[0]);

			//If it is IE 7 or less...
			if($v<8)
			{
				//Go to error page
				header("Location:/norun.php");
			}

			break 1;
		}
	}
}

/*  
 *	  Name: is_mobile
 * Purpose: Determines whether the user is using a mobile phone
 * Returns: Boolean
 */
function is_mobile()
{
	//Get the current user agent
	$ua = $_SERVER["HTTP_USER_AGENT"];

	//If it contains any of the following browsers...
	if (
	in_string($ua, "Windows CE") ||
	in_string($ua, "AvantGo") ||
	in_string($ua,"Mazingo") ||
	in_string($ua, "Mobile") ||
	in_string($ua, "T68") ||
	in_string($ua,"Syncalot") ||
	in_string($ua, "Blazer") ) 
	{
		$DEVICE_TYPE="MOBILE"; //Set the device type to 'mobile'
	}

	//Return whether the device type is mobile
	return (isset($DEVICE_TYPE) && $DEVICE_TYPE=="MOBILE");
}

/*  
 *	  Name: in_string
 * Purpose: Supporting function to tell whether a string is within a string
 * Returns: Boolean
 */
function in_string($haystack, $needle)
{
	//Return whether the string's start index can be found
	return (strpos($haystack, $needle)!==false);
}

/*  
 *	  Name: print_footer
 * Purpose: Prints the footer of the page
 * Returns: Void
 */
function print_footer()
{
	$footer = <<<FOOT
		<footer>
			<nav>
				<a href="/about">About</a> | 
				<a href="/abuse">Report Abuse</a> | 
				<a target="_blank" href="/privacy.htm">Privacy Policy</a> | 
				<a target="_blank" href="/terms.htm">Terms of Service</a> 
			</nav>
			<section>
				<div style="width:100%;text-align:center;">Created by <a href="http://twitter.com/tylernickerson">Tyler Nickerson</a>, '17</div>
				<div style="width:100%;text-align:center;">Copyright &copy; 2014 eDart</div>
			</section>
		</footer>
		<script type="text/javascript" src="/lib/min/?g=js"></script>
FOOT;

	echo $footer; //Print the footer
}

/*  
 *	    Name: 	print_header
 *   Purpose: 	Prints the header of the page
 * Arguments:
 *		- ctrl: Whether or not the login/profile box in the right hand corner is shown
 * Returns: 	Void
 */
function print_header($ctrl)
{
	check_version(); //Check to make sure the browser is supported
	
	/* * * MOBILE FORMATTING AND FAVICON * * */

		$additional = ""; //Additional things we'll be adding to the header

		//If the device is mobile...
		if(is_mobile()) 
		{ 
			//...use a mobile stylesheet
			$additional .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/scripts/css/mob/mobile.css\">";
		}

		//Changes favicon URL based on browser
		if(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "safari"))
		{
			$additional .= "<link rel=\"SHORTCUT ICON\" href=\"/favicon.ico?v=2\" />";
		}
		else
		{
			$additional .= "<link rel=\"SHORTCUT ICON\" href=\"/favicon.ico\" />";		
		}

	/* * * MAIN BANNER CONTENTS * * */

		$banner_contents = "";

		//If we aren't on the homepage..	
		if(getcwd()!==$_SERVER["DOC_ROOT"])
		{
			//Create a 'return home' or 'return to feed' button

			$text = "Return Home";

			//If we're logged in...
			if(isset($_SESSION["userid"]))
			{
				//...it'll be 'return to feed'
				$text = "Return to Feed";
			}

			//Add this to our banner
			$banner_contents .= "<div onclick=\"window.location='/';\" class=\"backbtn\">$text</div>";
		}
		else //If we are on the homepage...
		{
			//Make it the logo
			$banner_contents .= "<div id=\"logo\"><a href=\"/\"><img src=\"/img/logo.png\"></a></div>"; 

			//If we aren't logged in...
			if(!isset($_SESSION["userid"]))
			{
				//Display that text that shows how many members we have

				//Connect to MySQL
				$con = mysqli_connect(host(), username(), password(), mainDb());

				//Get the user count (not possible through API)
				$query = mysqli_query($con, "SELECT COUNT(*) AS count FROM `usr`");

				//Get the number of users
				$user_count = mysqli_fetch_array($query);
				$user_count = $user_count[0];

				$all_items   = new Item(array("action"=>"get"));
				$items_array = $all_items->run(true);
				$item_count  = count($items_array);

				//...add it to the banner
				$banner_contents .= "<div id=\"subtxt\">Now with $user_count members worldwide.</div>";	

				//Close the connection 
				mysqli_close($con);
			}
		}

		$add_box = "";

		//If we are going to show the login/profile panel...
		if($ctrl)
		{
			//If the user is logged in
			if(isset($_SESSION["userid"]))
			{
				//Print their control panel and search box
				$banner_contents .= get_search_box();
				$banner_contents .= get_profile_box($_SESSION["userid"]);	
				//$add_box 		 .= get_add_box();
			}
			else //If not...
			{
				//Print the login box
				$banner_contents .= get_login_box();
			}
		}

		$user_menu = get_user_menu();

	/* * * MAIN HEADER * * */

		$header = <<<HEAD
					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0" />
					<meta name="description" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students." /> 
					<meta name="keywords" content="edart,beta,bartering,tradegrouper,trade,trading,tradby,college,worcester,polytechnic,institute,wpi,2013,free,online,database" /> 
					<meta name="robots" content="index, follow" /> 
					<meta name="Headline" content="Welcome to eDart!">
					<meta name="CPS_SITE_NAME" content="Welcome to eDart!">
					<meta property="og:title" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students."> 
					<meta property="og:type" content="website"> 
					<meta property="og:description" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students.">
					<meta property="og:site_name" content="eDart">
					<meta charset="UTF-8">

					<noscript>
						<meta http-equiv="refresh" content="0;URL=/noscript.php">
					</noscript>

					<link rel="stylesheet" type="text/css" media="screen" href="/files/fonts/Vegur/stylesheet.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/files/fonts/Titillium/stylesheet.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/min/?g=css">

					<script>
						document.cookie='';

						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

						ga('create', 'UA-44057002-1', 'wewanttotrade.com');
						ga('send', 'pageview');
					</script>

					$additional
					$add_box
					$user_menu

					<div id="bpho" style="height:60px;width:100%;">
						<div id="banner">
							$banner_contents
						</div>
					</div>
HEAD;

		echo $header; //Print the header
}

/*  
 *	  Name: get_search_box
 * Purpose: Returns the HTML of the eDart search box
 * Returns: String
 */
function get_search_box()
{
	//Default placeholder text
	$txt = "Click here to start your search";

	$search_html = <<<SRCH
		<form method="GET" id="hfrm" action="/search.php">
			<input 			name=		"keyword"
							type=		"text"
							onkeyup= "if(event.keyCode==13){document.getElementById('hfrm').submit();}"
							id=		"headsearch"
							autocomplete=	"off"
							data-default="$txt"
							value=		"$txt" />
		</form>
SRCH;

	return $search_html;
}

/*  
 *	  Name: get_user_menu
 * Purpose: Returns the HTML of the user menu, or a blank string if not logged in
 * Returns: String
 */
function get_user_menu()
{
	$return_str = ""; //Our return string

	//If the user is logged in...
	if(isset($_SESSION["userid"]))
	{
		//Script for connecting to Facebook
		$fb_script = <<<FB
				<div id="fb-root"></div>

				<script>
				
					window.fbAsyncInit = function() {
		  			FB.init({
		    			appId      : '1410963979147478',
		    			status     : true, // check login status
		    			cookie     : true, // enable cookies to allow the server to access the session
		    			xfbml      : true  // parse XFBML
			  			});
			  		}

					function fblogin() 
					{
						try
						{
							FB.ui({
		  						method: 'send',
		  						link: 'http://wewanttotrade.com/',
							});	
						}
						catch(e)
						{
							FB.login();
						}
					}
					    (function(d){
		   				var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		   				if (d.getElementById(id)) {return;}
		   				js = d.createElement('script'); js.id = id; js.async = true;
		   				js.src = "//connect.facebook.net/en_US/all.js";
		   			  	ref.parentNode.insertBefore(js, ref);
					  }(document));

				</script>
FB;

		$user_id = $_SESSION["userid"]; //Assign the current user ID to a variable

		//Create the options menu	
		$options_menu = <<<OPT
				<div id="usermenu">
					<div id="tip">
						<img src="/img/tool.png">
					</div>
				
					<ul>
						<li><a href="/me?load=manage">Manage Account</a></li>
						<li onclick="fblogin();">Invite Friends</li>
						<li><a href="/profile.php?id=$user_id">View Public Profile</a></li>
					
						<div class="divide"></div>
						<li><a href="/changes" target="_blank">View Changelog</a></li>
						<li><a style="color:tomato" href="/bugs">Report a Bug</a></li>
						<li onclick='logout();'>Logout</li>
					</ul>
			  </div>
OPT;
		//Append the two
		$return_str = $fb_script . $options_menu;
	}

	return $return_str;
}
/*  
 *	  Name: get_profile_box
 * Purpose: Returns the HTML of the profile control panel
 * Returns: String
 */
function get_profile_box($user_id)
{
	//Get the info on the given user
	$newUser = new User(array("action"=>"get", "id"=>$user_id));
	$userArray = $newUser->run(true);
/*	<div id="tmenu">
					<div onclick="$('#postbox').modal();" class="ticon glyphicon glyphicon glyphicon-plus"></div>
				</div>*/
	//Create the user box
	$user_box = <<<UBOX
			<a href="/me">
				<div id="minippic" >
					<img src="/me/picture/?size=small" style="width:50px;height:50px;">
				</div>
			</a>

			<div id='infobox'>

				<a href="/me">
					{$userArray[0]["fname"]} {$userArray[0]["lname"]}
				</a>

				<br/>

				<div id="lgotxt" onclick="displayMenu();">Options</div>

			</div>

			<script type="text/javascript">
				function displayMenu()
				{
					var menu = document.getElementById('usermenu');
					var optxt = document.getElementById('lgotxt');
					if(menu.style.display=='block')
					{
						optxt.style.color='';
						menu.style.display='none';
					}
					else
					{
						optxt.style.color='white';
						menu.style.display='block';
					}
				}
			</script>
UBOX;

	return $user_box;
}

/*  
 *	  Name: get_login_box
 * Purpose: Returns the HTML of the login panel
 * Returns: String
 */
function get_login_box()
{
	$login = <<<LGN
		<script type="text/javascript">

			var lerr = false;

			function lenter(e)
			{
				if(e.keyCode == 13)
				{
					document.getElementById("loginarrow").click();
				}
			}

			function setLErr()
			{
				lerr = true;
			}

			var cntarr = new Array('Oh bother... looks like your credentials are incorrect!', 'Whoops... wrong credentials... try again?', 'Are you sure you remember your credentials? Because they\'re incorrect.', 'Oh man... can\'t log you in. Try again?', 'Incorrect username or password... we\'d tell you which one, but that would just make it easier for hackers to hack us.', 'Incorrect credentials (please don\'t hate us!)');
			
			function getLoginNo()
			{
				var greeting = cntarr[Math.floor(Math.random()*(cntarr.length-1))+1];
				document.getElementById('cntlgnm').innerHTML=greeting;
			}
		</script>

		<table id="loginbx">
			<tr>
				<td id="cntlgnm" style="color:green;width:250px;font-size:12px;"></td>
				<td>
					<input 	name=		"leaddr" 
						type=		"text" 			
						id=		"leaddr" 	 
						class=		"inpt" 
						style=		"font-size:16px;float:left;margin-top:0px;"
						data-default="Email Address"
						autocomplete=	"off" 
						value=		"Email Address" 
						onkeydown=	"lenter(event);"
					/>
				</td>

				<td>
					<input 	style="color:black;width:100%;font-size:16px;margin-top:0px;" 
							class=		"inpt" 
							name=		"lpword" 
							id=		"lpword" 
							data-dummy="dlpword"
							autocomplete=	"off" 
							type=		"password"				
							onkeydown=	"lenter(event);" 
						/>

					<input 	style=		"width:100%;
								font-size:16px;
								margin-top:0px;" 
						class=		"inpt" 
						id=		"dlpword" 
						autocomplete=	"off" 
						type=		"text" 
						value=		"Password" 
					/>
				</td>
			
				<td>
					<div id="loginarrow" title="Login to eDart" class="loginbtn glyphicon glyphicon-circle-arrow-right" onclick="login(document.getElementById('leaddr').value, document.getElementById('lpword').value,'',function(){getLoginNo();});"></div>
				</td>
		
				<td>
					<div id="loginforgot" title="Forgot Password?" class="loginbtn glyphicon glyphicon-question-sign" onclick="window.location='/forgot';"></div>
				</td>
			</tr>
		</table>
LGN;

	return $login;
}

/*  
 *	  Name: get_add_box
 * Purpose: Returns the HTML of the item posting dropdown window
 * Returns: String
 */
function get_add_box()
{
	//These variables can be found in 'call.php' in api_lib
	global $available_condition, $available_category;

	$condition_list = "<select id=\"itemupload_condition\" class=\"inpt small_text selectbox\">";
	$category_list  = "<select id=\"itemupload_category\"  class=\"inpt small_text selectbox\">";

	foreach($available_category as $cat)
	{
		$category_list .= "<option>$cat</option>";
	}

	foreach($available_condition as $cond)
	{
		$condition_list .= "<option>$cond</option>";
	}

	$condition_list .= "</select>";
	$category_list  .= "</select>";

	$add_html = <<<ADD
		<div id="postbox" class="modal fade">

			<div class="modal-dialog">

				<div class="modal-content">

					<div class="modal-header">
        		
        				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        					&times;
        				</button>
        
        				<h4 class="modal-title">
        					Post New Item [IN BETA - PLEASE DO NOT USE]
        				</h4>

      				</div>
      
	      			<div class="modal-body">

	       				<div id="ai_left">

	       					<div id="itemupload_picture" style="background:url('/img/default.png') no-repeat center center;">
								<div id="itemupload_loader">
									<img src="/img/719.GIF">
									Uploading to the</br>interwebs...
								</div>
							</div>			

	       					<input type="button" value="Upload Image" onclick="$('#item_image_browser').click();" style="margin-top:10px;width:100%;" class="bbtn" />

							<form id="itemupload_image_form" method="post" target="hidden_frame" action="/scripts/php/ajax/item/image_upload.php" enctype="multipart/form-data">
								<input style="z-index:-200" accept="image/*" onchange="document.getElementById('itemupload_loader').style.display='block'; document.getElementById('itemupload_image_form').submit();" type="file" name="item_upload" id="item_image_browser" />
							</form>

							<iframe name="hidden_frame" style="visibility:hidden;"></iframe>

	       				</div>
	       	
	       				<div id="ai_right">

	       					<div class="box_hdr">
	       						Basics
	       					</div>

	       					<input type="hidden" name="image" id="itemupload_code" value="" />
							<input type="hidden" name="action" id="itemupload_action" value="create" />
							<input type="hidden" name="id" id="itemupload_id" value="" />

	       					<input name="itemname" maxlength="30" id="itemupload_name" class="inpt small_text" data-default="Item Name" autocomplete="off" value="Item Name" type="text" />
	      					
	       					in

	       					$category_list

	      					<textarea name="itemdesc" rows="3"  class="inpt iteminfo" id="item_desc" maxlength="100" data-default="Enter a brief description">Enter a brief description</textarea>

	      					<div class="center" style="margin-bottom:10px;">
	      						
	      						$condition_list
	      					</div>

	   						<div class="box_hdr">Pickup Location</div>

							<select id="dropdown">
								<option>Rubin Campus Center</option>
								<option>George C. Gordon Library</option>
								<option>Morgan Commons</option>
								<option>Salisbury Laboratories</option>
								<option>Fuller Laboratories</option>
								<option>Recreation Center</option> 
								<option>The Quad</option>
							</select>

							<div class="box_hdr">Due Date</div>
							<input type="label" name="date" onfocus="this.blur()" class="hidden_input small_text" id="datepicker" value="Click here to set a due date" readonly/>

	       				</div>

	      			</div>
      
	      			<div class="modal-footer">

	        			<button type="button" class="bbtn" data-dismiss="modal">
	        				Cancel
	        			</button>

	        			<button type="button" class="gbtn">
	        				Post Item
	        			</button>

	      			</div>

    			</div>
  			</div>
		</div>
ADD;

	return $add_html;
}

function minify($string)
{
	return str_replace("\n", "", str_replace("\t", "", $string));
}
?>
