<?php
/*
 * Page Name: Exchange
 * Purpose: Guides users through their exchanges
 * Last Updated: 6/4/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

/* * * Include a series of widgets as opposed to printing out raw HTML * * */

// 1) Calender selector
include_once $_SERVER["DOC_ROOT"]."/scripts/php/widget/exchange/calender.php";

// 2) Meetup countdown
include_once $_SERVER["DOC_ROOT"]."/scripts/php/widget/exchange/meetup.php";

// 3) Pickup countdown
include_once $_SERVER["DOC_ROOT"]."/scripts/php/widget/exchange/pickup.php";

// 4) Rating widget
include_once $_SERVER["DOC_ROOT"]."/scripts/php/widget/exchange/rater.php";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
 
//Connect to MySQL for our non-API related queries
$con = mysqli_connect(host(), username(), password(), mainDb());

//Will remove
if(!isset($_SESSION)){session_start();} //Resume the session
//

$oid = $_GET["offerid"]; //Retrieve the offer ID

$currentExchange = new Exchange(array("action"=>"get", "id"=>$oid)); //Get info about the exchange
$getExchange = $currentExchange->run(); //Read the info into an array

//If no info can be found about the exchange...
if((!is_array($getExchange))||(count($getExchange)==0))
{
	header("Location: /notfound.php"); //...go to a not found page
	exit; //Exit
}

//who_ranked keeps info about who has ranked the other thus far. It is stored in an array in the database.
//If the array is null, then just make it empty. If it isn't, store it in the variable.
$who_ranked = (is_array(json_decode($getExchange[0]["who_ranked"], true))) ? json_decode($getExchange[0]["who_ranked"], true) : array();

//If the user has already ranked the other user, the exchange (for them) is over
//Take them to a "special page"

if(in_array($_SESSION["userid"], $who_ranked))
{
	include_once $_SERVER["DOC_ROOT"] . "/gameover.php";
	die;
}

/* * * GET OUR INFO * * */

	//Retrieve info about the first item and store it in an array
	$item1 		= new Item(array("action"=>"get", "filter"=>array("id"=>$getExchange[0]["item1"])));
	$item1_info = $item1->run();

	//Retrieve info about the second item and store it in an array
	$item2 		= new Item(array("action"=>"get", "filter"=>array("id"=>$getExchange[0]["item2"])));
	$item2_info = $item2->run();

	//Using session info, figure out whether the user ID of the other user (not the one logged in)
	$other_id	= ($item1_info[0]["usr"] == $_SESSION["userid"]) ? $item2_info[0]["usr"] : $item1_info[0]["usr"];

	//Get info about the other user and store it into an array
	$otherUser	= new User(array("action"=>"get", "id"=>$other_id));
	$otherInfo	= $otherUser->run(true);

/* * * END GET INFO * * */

/* * * DECLARE A SHIT-TON OF VARIABLES * * */

	/* --- MEETING UP --- */

		// The meeting place is that of the first item (the user who accepted the offer)


		$meetdt 	= $getExchange[0]["date"]; 		//The meeting date (timestamp)

		$stadd1		= $item1_info[0]["stadd1"]; 	//Street address 1
		$stadd2		= $item1_info[0]["stadd2"];		//Street address 2
		$citytown 	= $item1_info[0]["citytown"];	//City/Town
		$room		= $item1_info[0]["room"];		//Room number
		$state   	= $item1_info[0]["state"];		//State

		//Append all this info into an address string to display
		$address	= $item1_info[0]["stadd1"] 	 . ", " .
					  $item1_info[0]["citytown"] . ", " .
					  $item1_info[0]["state"];

	/* --- ITEM INFO --- */

		// Item 1 is the item of the user who accepted
		// Item 2 is of the one who offered

		$i1name 	= $item1_info[0]["name"];	//Item 1 name
		$duedate_1	= $item1_info[0]["duedate"];	//Item 1 due date

		$i2name		= $item2_info[0]["name"];	//Item 2 name
		$duedate_2	= $item2_info[0]["duedate"]; 	//Item 2 due date

	/* --- DUE INFORMATION --- */

		$duedate    = ($duedate_1<$duedate_2) ? $duedate_1 : $duedate_2; //The due date based on which one is earlier

	/* --- MESSAGING --- */

		$msgarr		= $getExchange[0]["messages"]; //All of the exchange chat's contents

	/* --- THE OTHER USER --- */

		$other_fname = $otherInfo[0]["fname"]; 		//First name
		$other_lname = $otherInfo[0]["lname"];		//Last name

/* * * END VARIABLE DECLARATIONS * * */

// * * * USER RANKING SCRIPT * * */

	//If a post call has been made to this page to rank a user...
	if(isset($_POST["rate_desc"]))
	{
		$points   = array();	//This will store of all the bar values

		//Loop through the entire post call, looking for 'bars'
		foreach($_POST as $k=>$v)
		{
			//If we found one...
			if(strpos($k, "rank_")!==false)
			{
				//Push it's index (count) and value into the array
				array_push($points, $v);
			}
		}

		//Get the current rankings of the user
		$userRank = json_decode($otherInfo[0]["rank"], true);

		//If the array is null, make a new one
		if(!is_array($userRank))
		{
			$userRank = array();
		}

		//Generate a master array
		$master_array = array("points"=>$points, "description"=>$_POST["rate_desc"]);

		//Push it
		array_push($userRank, $master_array);

		//Set the new ranking array
		//Because we're changing someone else's information, we can't use the API

		$query = "UPDATE `usr` SET `rank`='".mysqli_real_escape_string($con, json_encode($userRank))."' WHERE `id`='".mysqli_real_escape_string($con, $otherInfo[0]["id"])."'";
		mysqli_query($con, $query);

		//Add the user to the array of people who ranked
		array_push($who_ranked, $_SESSION["userid"]);
		mysqli_query($con, "UPDATE `exchange` SET `who_ranked`='".mysqli_real_escape_string($con, json_encode($who_ranked))."' WHERE `id`='".mysqli_real_escape_string($con, $oid)."'");

		sendNotify($other_id, "Someone has ranked you!", "profile.php?id=$other_id");

		header("Location:/");
	}

/* * * END USER RANKING SCRIPT * * */

HTML::begin();
Head::make("Exchange with $other_fname $other_lname | $i1name  for $i2name", false);
Body::add_action("pre_exchange()");
Body::add_action("codeAddress('$address')");
Body::begin();

		//This converts the first name of the other user to a JavaScript variable for us to use later.
		echo "<script>var fname = \"$other_fname\";</script>";
?>

		<style type="text/css">
		#banner { border-bottom:none !important; }
		</style>

			<?php

			/* * * PRINT OUT THE CHAT PANEL * * */

				//The head

				$chat_head = <<<BOB
								<img src="./img/sideshadowl.png"
									 style="position:fixed;z-index:200;right:35%;width:20px;height:100%;">

								<div id="xchgapp">
									<div id="chatpnl">
										<div id="chtxt"
											 style="font-size:48px;margin:20px;margin-top:90px;text-align:center;color:white;">
											 	Chat With $other_fname
										</div>

										<div style="background:white;height:1px;width:200px;margin:0 auto;"></div>

										<div id="msgc" style="margin:0px;padding:0px;position:relative;height:75%;overflow:auto;">
BOB;

				echo $chat_head; //Print out the chat head

				//Get all the messages from the server and break it down into an array
				$msg_br = json_decode($msgarr, true);

				//Loop through them all backwards
				for($i=count($msg_br)-1;$i>=0;$i--)
				{
					$value = $msg_br[$i]; //Get each message as its own array

					$msg   = trim($value["message"]); 	//Message content
					$aus   = trim($value["user"]); 		//User ID
					$ts    = trim($value["timestamp"]); //Timestamp

					//Get the user's info

					$authUser = new User(array("action"=>"get", "id"=>$aus));
					$auInfo   = $authUser->run(true);

					//Load their display name into one string
					$userDName 	= $auInfo[0]["fname"] . " " . $auInfo[0]["lname"];

					//Get the relative time the message was posted
					$relDt 		= getRelativeDT(time(), $ts) . " ago";

					//Get the HTML for the message
					$html = <<<EOD
							<div class="msg">
								<div class="inner">
									<div class="img_wrap"><a href="/profile.php?id=$aus"><img class="pic" src="/profile.php?id=$aus&load=image&size=small"></a></div>

									<div class="holder">
										<div class="title"><a href="/profile.php?id=$aus">$userDName</a></div>
										<div class="body">$msg</div>
									</div>

									<div class="date">$relDt</div>

								</div>
							</div>
EOD;

					echo $html; //Print it
				}

				echo "</div>"; //Close the chat subpanel

				//Load the chat size into a JavaScript variable
				echo "<script type=\"text/javascript\">len=".count($msg_br).";</script>";

				//Get the placeholder text for the send message input field
				$msgdstr = "Type a Message to ".$other_fname;

				echo "<input name=	\"msgtxt\"
							 type=		\"text\"
							 id=		\"msgtxt\" 
							 class=		\"xchgbx\"
							 data-default = \"$msgdstr\"
							 data-set = \"1\"
							 onfocus = \"if(this.getAttribute('data-set')=='1'){this.style.color='white';this.value='';this.setAttribute('data-set', '0');}\"
							 onblur  = \"if(this.getAttribute('data-set')=='0'){this.style.color='';this.value='$msgdstr';this.getAttribute('data-default');this.setAttribute('data-set','1');}\"
							 autocomplete=	\"off\"
							 value=		\"$msgdstr\"
					 		 onkeydown=	\"if(event.keyCode==13){message_send(this.value);}\"
				/>";

				echo "</div>"; //Close the entire side chat panel

			/* * * END CHAT PANEL * * */

			/* * * PRINT OUT THE MAP AREA * * */

				//Print out the main container head
				echo "";

				//Formats the second street address appropriately depending on if there is one
				$stadd2_f = (trim($stadd2)!="") ? $stadd2 . "</br>" : "";

				//Same goes for room numbers
				$roomnum_f = (trim($room)!="") ? "Room ". $room . "</br>" : "";

				$map_overlay = <<<ASS
								<div style="overflow:hidden;position:relative;height:40%;width:65%;">
									<div id="map-overlay">
										<div style="padding-top:100px;">
											$stadd1
											</br>
											$stadd2_f
											$roomnum_f
											$citytown, $state
										</div>
									</div>

									<div id="map-canvas" style="width:100%;height:100%;"></div>
									<div id="map-txt" class="glyphicon glyphicon-info-sign"></div>
								</div>
ASS;

				echo $map_overlay; //Print out the map

			/* * * END MAP AREA * * */

			/* * * PRINT OUT THE MAIN PAGE W/ WIDGETS * * */

			//Print out the pre-container
			echo "<div style=\"position:fixed;height:50%;z-index:1;overflow:visible;width:100%;\">";

			//Print out the main container
			echo "<div id=\"cont\" style=\"margin-left:0px;\">";

			//Format the meeting date
			$dispmdt = date('l, F jS, Y \a\t g A',strtotime($meetdt));

			//Format the due date
			$dd = date('l, F jS, Y \a\t g A',strtotime($duedate));

				
			//All cases
			$case1 = (($meetdt!=0) 		 && //If the meeting date is determined
					 ($duedate < time()) && //If the it has passed the due date
					 ($duedate!=0)); 		//And the item is on a return basis

			$case2 = (($meetdt!=0)	   && //If the meeting date is determined
					 ($meetdt < time())&& //If it's passed the meeting date
					 ($duedate==0)); 	  //If the exchange is permanent

			$case3 = (($meetdt!=0)	   && //If the meeting date is determined
					 ($meetdt < time())&& //And it's passed the meeting date
					 ($duedate!=0)); 	  //And the item is due for exchange

			//If the exchange is over (case1 or case2)
			if($case1||$case2)
			{
				//Print out the rating tool
				$newRating = new Rater($other_id);
				$newRating->output();
			}
			else if($case3) //If it's past the meeting date (the item is in exchange) and it's due to be returned
			{
				//Show the pickup countdown
				$newPickup = new Pickup($duedate);
				$newPickup->output();
			}
			else //If the users have yet to meet
			{
				//If the meeting date has not been determined...
				if($meetdt==0)
				{
					//...show the calender
					$newCal = new Calender(trim($oid));
					$newCal->output();
				}
				else //If it has...
				{
					//...print out the countdown to the meetup
					$newMeetup = new Meetup($meetdt);
					$newMeetup->output();
				}
			}

			echo "</div>"; //Close the main container

			?>
			<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
			<script type="text/javascript" src="/lib/min/?g=js"></script>

<?php
	Body::end(false);
	HTML::end();
	mysqli_close($con); //Close the existing MySQL connection
?>
