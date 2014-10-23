<?php
/*
 * Page Name: Item View
 * Purpose: Viewing page for items
 * Last Updated: 6/6/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

//Use the URL query string to get the full relative URL of the current page
//(will be useful later)
$url = "view.php?".$_SERVER["QUERY_STRING"];

//If the user ID or item ID aren't part of the URL
if((!isset($_GET["userid"]))||(!isset($_GET["itemid"])))
{
	//Throw a not found page
	header("Location: /notfound.php");
	exit;
}

/* * * DECLARE A SHIT-TON OF VARIABLES * * */
/* * * A description can be found below * * */

$aid = $_GET['userid']; //The user ID of the towner of this item

//Let's connect to MySQL...
$con = mysqli_connect(host(), username(), password(), mainDb());

//If you can't connect...
if(mysqli_connect_errno())
{
	//Throw an error
	echo "Failed to connect: " . mysqli_connect_error();
}

//Get the current item info
$thisItem = new Item(array("action"=>"get", "filter"=>array("id"=>$_GET["itemid"], "usr"=>$_GET["userid"])));
$gotItem  = $thisItem->run(true);

//If the item can't be found...
if(count($gotItem)==0)
{
	//...throw a not found page
	header("Location: /notfound.php");
	exit;
}

$name 		= $gotItem[0]['name'];			//Item name
$desc 		= $gotItem[0]['description'];	//Item description
$stadd1		= $gotItem[0]['stadd1'];		//Street address 1 of pickup
$stadd2		= $gotItem[0]['stadd2'];		//Street address 2 of pickup
$room		= $gotItem[0]['room'];			//Room number of pickup
$citytown	= $gotItem[0]['citytown'];		//City/town of pickup
$state		= $gotItem[0]['state'];			//State of pickup
$dd			= $gotItem[0]['duedate'];		//Due date of pickup
$imgloc		= $gotItem[0]['image'];			//Item image
$expire 	= $gotItem[0]["expiration"];	//Item expiration
$category   = $gotItem[0]["category"];		//Item category
$condition  = $gotItem[0]["condition"];		//Item condition
$emv		= $gotItem[0]['emv'];			//Item EMV
$dodue		= (intval($dd)!=0);				//Whether the item will have a due date
$views		= $gotItem[0]['views'];			//Number of views

$relative_dd = getRelativeDT(time(), $dd);		//Due date relative to today
$relative_ex = getRelativeDT($expire, time());  //Expiration date relative to today

//Increment view count (disregarding the owner of the item)
if(isset($_SESSION["userid"]) && ($_SESSION["userid"]!=$_GET["userid"]))
{
	mysqli_query($con, "UPDATE `item` SET `views`='".mysqli_real_escape_string($con, $views+1)."' WHERE `usr`='".mysqli_real_escape_string($con, $_GET["userid"])."' AND `id`='".mysqli_real_escape_string($con,$_GET["itemid"])."'");
}

//Get all offers on the item and store it in an array
$offers 	= $gotItem[0]['offers'];
$offers     = json_decode($offers, true);

//Convert the status of the item to a number
$status		= intval($gotItem[0]["status"]);

//Get info about the owner of the item
$authorUser = new User(array("action"=>"get", "id"=>$gotItem[0]["usr"]));
$userGet	= $authorUser->run(true);

$profpic 	= $userGet[0]['profile_pic'];			//The owner's profile picture
$afname 	= $userGet[0]['fname'];					//The owner's first name
$alname 	= $userGet[0]['lname'];					//The owner's last name
$adob		= $userGet[0]["dob"];					//The owner's date of birth
$agender	= Lookup::Gender($userGet[0]["gender"]); //The owner's gender

//Get other items in by this user
$other_itemReq = new Item(array("action"=>"get", "filter"=>array("usr"=>$aid)));
$other_items = $other_itemReq->run();

//Get other items in this category
$sim_itemReq = new Item(array("action"=>"get", "filter"=>array("category"=>$category)));
$sim_items = $sim_itemReq->run();

$user_items = array();

if(isset($_SESSION["userid"]))
{
	//Get items available for offer
	$user_itemReq = new Item(array("action"=>"get", "filter"=>array("usr"=>$_SESSION["userid"], "status"=>"1")));
	$user_items = $user_itemReq->run();
}

//Store the owner's privacy settings in an array
$privacy	= is_array(json_decode($userGet[0]["privacy"], true)) ? json_decode($userGet[0]["privacy"], true) : array();

//Get the join date of the user
$jdate 		= $userGet[0]["join_date"];

//Get the full page URL
$full_url = urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

/* * * ACCEPTING AN ITEM * * */
if(isset($_POST["accept"])&&trim($_POST["accept"])!="")
{
	//Create a new exchange
	$acceptExchange = new Exchange(array("action"=>"create", "item1"=>$_GET["itemid"], "item2"=>$_POST["accept"]));
	$acceptExchange->run();

	//Redirect to the new exchange page
	header("Location: /".$url);
	exit;
}

/* * * WITHDRAWING AN ITEM * * */
	
	if(isset($_POST["withdraw"])&&trim($_POST["withdraw"])!="")
	{
		//Get info about the withdrawl item
		$wdRequest = new Item(array("action"=>"get", "filter"=>array("id"=>$_POST["withdraw"])));
		$wdInfo    = $wdRequest->run(true);

		//If the user doesn't own this item
		if($wdInfo[0]["usr"]!=$_SESSION["userid"])
		{
			//Just refresh the page
			header("Location: /".$url);
			exit;
		}
		else //If the user DOES own the item
		{
			//Create a new empty offer array
			$newArray = array();

			//Attempt to copy all offers into the new array, leaving out the withdrawl item
			if(is_array($offers))
			{
				foreach($offers as $o)
				{
					if($o["id"]!=$_POST["withdraw"])
					{
						array_push($newArray, $o);
					}
				}
			}

			//Update the current item with the new offer array
			$query = "UPDATE `item` SET `offers`='".mysqli_real_escape_string($con, json_encode($newArray))."' WHERE `id`='".mysqli_real_escape_string($con, $_GET["itemid"])."'";
			mysqli_query($con, $query);

			//Redirect to the non-post-form page
			header("Location: /".$url);
			exit;
		}

	}

/* * * MAKING AN OFFER * * */

if(isset($_POST["offer"])&&trim($_POST["offer"])!="")
{
	//Get info about the offered item
	$itemCheck = new Item(array("action"=>"get", "filter"=>array("id"=>$_POST["offer"])));
	$itemRet   = $itemCheck->run(true);

	//If the item exists
	if((is_array($itemRet))&&(count($itemRet)!=0))
	{
		//Use the API to make the offer
		$offerItem = new Item(array("action"=>"offer", "id"=>$_GET["itemid"], "offer"=>$_POST["offer"]));
		$offerItem->run();
	}

	//Redirect to a non-post-form page
	header("Location: /".$url);
	exit;
}


HTML::begin();
Head::make($name, true);
Body::begin();

			?>
			
			<div id="lightbox" class="uk-modal">
			    <div class="uk-modal-dialog uk-modal-dialog-frameless">
			        <a href="" class="uk-modal-close uk-close uk-close-alt"></a>
			        <img style="width:100%;" src="/imageviewer/?id=<?php echo $_GET["itemid"]; ?>" alt="<?php echo $name; ?>">
			    </div>
			</div>
			
			<div id="embed_box" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						<a class="uk-modal-close uk-close"></a>
						Embed this Item
					</div>
		  			<div class="uk-modal-content">
		  				<p>Copy and paste this code into your website to display a link to this page.</p>
		  				<textarea onclick="this.select();" class="hidden_input"><link rel="stylesheet" type="text/css" media="screen" href="https://<?php echo $_SERVER["HTTP_HOST"]; ?>/scripts/css/embed.css"><iframe class="edart_iframe" src="https://<?php echo $_SERVER["HTTP_HOST"]; ?>/embed/?item=<?php echo $_GET['itemid']; ?>&user=<?php echo $_GET['userid'];?>" width="320" height="150" frameBorder="0"></iframe></textarea>
		      		</div>
		  			<div class="uk-modal-footer">
		    			<button type="button" id="close" data-dismiss="modal" aria-hidden="true" class="button_primary green">Close</button>
		  			</div>
				</div>
			</div>

			<div id="qr_box" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						<a class="uk-modal-close uk-close"></a>
						Scan this Item
					</div>
					<div class="uk-modal-content">
						<p>Scan or print this QR code to obtain a direct link to this page.</p>
						<div id="qrdisp" style="background: url('https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=L&data=<?php echo $full_url; ?>') no-repeat 0 0;"></div>
					</div>
					<div class="uk-modal-footer">
						<button type="button" id="close" data-dismiss="modal" aria-hidden="true" class="button_primary green">Close</button>
					</div>
				</div>
			</div>

			<div class="layout-1200 uk-container-center">
				<div class="uk-grid uk-grid-preserve">
					<div class="uk-width-1-1">
						<div class="uk-grid uk-grid preserve">
							<div class="uk-width-small-3-10">
								<a href="#lightbox" data-uk-modal>
									<div class="image_body" style="background-image:url('/imageviewer/?id=<?php echo $_GET["itemid"]; ?>&size=thumbnail');"></div>
								</a>
								<div class="image_toolbox">
									<a href="#embed_box" data-uk-modal><span data-uk-tooltip="{pos:'bottom'}" title="Get HTML" class="uk-icon-code"></span></a>
									<a href="#qr_box" data-uk-modal><span data-uk-tooltip="{pos:'bottom'}" title="Scan QR Code" class="uk-icon-qrcode" ></span></a>
									<?php if(isset($_SESSION["userid"])&&$userGet[0]["id"]==$_SESSION["userid"]):
											$myInfo = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
											$me = $myInfo->run(true);
											$me = $me[0];
									?>
										
										<?php if(($status!=0)&&($me["status"]==2)): ?>
											<span onclick="open_editor('<?php echo trim($_GET["itemid"]); ?>');" data-uk-tooltip="{pos:'bottom'}" title="Edit Item" class="uk-icon-edit" ></span>
											<span onclick="confirm_delete('<?php echo trim($_GET["itemid"]); ?>');" data-uk-tooltip="{pos:'bottom'}" title="Delete Item" class="uk-icon-times"></span>
										<?php else: ?>
											<a href="#embed_box" data-uk-modal><span data-uk-tooltip="{pos:'bottom'}" title="View Transaction"  class="uk-icon-eye"></span></a>
										<?php endif; ?>
									
									<?php endif; ?>
								</div>

							</div>
							
							<div class="uk-width-small-7-10" id="item_info">
								<h1><?php echo $name; ?></h1>
								<div class="uk-width-1-1 item_description">
									<?php echo $desc; ?>
									<div class="uk-align-right">
										<div class="uk-badge"><?php echo Lookup::Category($category); ?></div>
									</div>
								</div>
								<hr/>
								<div id="specs">
									<ul>
										<?php if($dodue): ?>
											<li><b>Due:</b> <?php echo ((date("Y-m-d", $dd)==date("Y-m-d")) ? "Today" : $relative_dd . " from now"); ?></li>
										<?php endif; ?>
										
										<?php if($emv!=""): ?>
											<li><b>Worth:</b> <a href="/emv.php">$<?php echo $emv; ?>.00</a></li>
										<?php endif; ?>
										
										<?php if($expire>time()): ?>
											<li><b>Expires:</b> <?php echo (date("Y")!=date("Y",$expire)) ? date("F jS, Y", $expire) : date("F jS", $expire);; ?> at <?php echo date("g:i A", $expire) ?>  (<?php echo $relative_ex; ?> from now)</li>
										<?php endif; ?>
									</ul>
									
									<?php if($expire<time()): ?>
											<div class="uk-width-1-1 uk-text-center uk-alert uk-alert-danger">This item has expired</div>
									<?php endif; ?>
								</div>
										
						</div>
						</div>
					</div>
										
					<div class="uk-width-1-1">
					
						<div class="uk-grid uk-grid-preserve">
							<div class="uk-width-small-3-10" style="padding-top:15px;">

								<ul class="uk-subnav uk-subnav-pill uk-text-center" data-uk-tab="{connect:'#info_container'}" id="item_tabs">
								   <li class="uk-active"><a>About Owner</a></li>
								  <li><a>More from Owner</a></li>
								  <li><a>Similar Items</a></li>
								</ul>
	
								<div id="info_box">
									<div class="uk-switcher uk-margin " id="info_container">
										<div class="owner">
											<div class="uk-grid uk-grid-preserve">
												<div class="uk-width-2-6">
													<img src="/profile.php?id=<?php echo $aid; ?>&load=image" class="uk-border-circle owner_picture" onclick="window.location='profile.php?id=<?php echo $aid; ?>';" ?>								
												</div>
												
												<div class="uk-width-4-6" style="margin-top:8px;">
													<div class="infotxt">
														<h1><a href="/profile.php?id=<?php echo $aid; ?>"><?php echo "$afname $alname"; ?></a></h1>
					
														<div id="uinfo">
															Member since <?php echo date("F j, Y", $jdate); ?> <br/>
															<?php echo (!is_array($agender)&&!in_array("gender", $privacy)) ? trim($agender) : ""; ?>
															<?php echo (!is_array($adob)&&(trim($adob)!=0)&&(!in_array("dob", $privacy))) ?   ", " . trim(getRelativeDT(time(), $adob)) . " old<br/>" : ""; ?>
														</div>
														
													</div>
												</div>
											</div>						
										</div>
										
										<div class="other">
											<?php if(count($other_items)<=1): ?>
													<br><div style="font-size:14px;margin-left:25px;margin-bottom:20px;color:dimgray;">Nothing to show here!</div>
											<?php else:
													for($i = 0; $i < 3; $i++):
														if((isset($other_items[$i]))&&($other_items[$i]["id"]!=$_GET["itemid"])):
											?>															
															<div class="item" onclick="window.location='/view.php?itemid=<?php echo $other_items[$i]["id"]; ?>d&userid=<?php echo $other_items[$i]["usr"]; ?>';">
																<div class="uk-grid uk-grid-preserve reset_padding">
																	<div class="uk-width-4-6 info">
																		<div class="header"><?php echo $other_items[$i]["name"]; ?></div>
																		<div class="description"><?php echo $other_items[$i]["description"]; ?></div>
																		<div class="overview uk-grid">
																			<div class="uk-width-1-3" title="Number of Offers">
																				<span class="uk-icon-cube"></span> <?php echo count(Json_decode($other_items[$i]["offers"], true)); ?>
																			</div>
																			<div class="uk-width-1-3" title="View Count">
																				<span class="uk-icon-eye"></span> <?php echo $other_items[$i]["views"]; ?>
																			</div>
																			<div class="uk-width-1-3" title="Estimated Market Value (EMV)">
																				<span class="uk-icon-usd"></span> <?php echo $other_items[$i]["emv"]; ?>
																			</div>
																		</div>
																	</div>
																	<div class="uk-width-2-6">
																		<div style="background:url('/imageviewer/?id=<?php echo $other_items[$i]["id"]; ?>&size=medium') no-repeat center center;" class="thumbnail"> 
																			<div class="gradient"></div>
																		</div>
																	</div>
																</div>
															</div>
											<?php 		endif;
												  	endfor; ?>
													<div class="item_viewmore" onclick="window.location='/search.php?keyword=<?php echo urlencode("User:$aid"); ?>';">
														<div class="uk-hidden-small uk-width-1-1 uk-button" style="padding:5px;">View More</div>
													</div>
											<?php endif; ?>										
										</div>			
										
										<div class="similar">
											<?php if(count($sim_items)<=1): ?>
													<br><div style="font-size:14px;margin-left:25px;margin-bottom:20px;color:dimgray;">Nothing to show here!</div>
											<?php else:
													for($i = 0; $i < 3; $i++):
														if((isset($sim_items[$i]))&&($sim_items[$i]["id"]!=$_GET["itemid"])):
											?>															
															<div class="item" onclick="window.location='/view.php?itemid=<?php echo $sim_items[$i]["id"]; ?>d&userid=<?php echo $sim_items[$i]["usr"]; ?>';">
																<div class="uk-grid uk-grid-preserve reset_padding">
																	<div class="uk-width-4-6 info">
																		<div class="header"><?php echo $sim_items[$i]["name"]; ?></div>
																		<div class="description"><?php echo $sim_items[$i]["description"]; ?></div>
																		<div class="overview uk-grid">
																			<div class="uk-width-1-3" title="Number of Offers">
																				<span class="uk-icon-cube"></span> <?php echo count(Json_decode($sim_items[$i]["offers"], true)); ?>
																			</div>
																			<div class="uk-width-1-3" title="View Count">
																				<span class="uk-icon-eye"></span> <?php echo $sim_items[$i]["views"]; ?>
																			</div>
																			<div class="uk-width-1-3" title="Estimated Market Value (EMV)">
																				<span class="uk-icon-usd"></span> <?php echo $sim_items[$i]["emv"]; ?>
																			</div>
																		</div>
																	</div>
																	<div class="uk-width-2-6">
																		<div style="background:url('/imageviewer/?id=<?php echo $sim_items[$i]["id"]; ?>&size=medium') no-repeat center center;" class="thumbnail"> 
																			<div class="gradient"></div>
																		</div>
																	</div>
																</div>
															</div>
											<?php 		endif;
												  	endfor; ?>
													<div class="item_viewmore" onclick="window.location='/search.php?keyword=<?php echo urlencode("User:$aid"); ?>';">
														<div class="uk-hidden-small uk-width-1-1 uk-button" style="padding:5px;">View More</div>
													</div>
											<?php endif; ?>										
										</div>							
									</div>
								</div>
							</div>
							<div class="uk-width-small-7-10" id="offer_board">
								<h2>Current Offers</h2>
								<form id="po_form" action="/<?php echo $url; ?>" method="post">
									<?php $disallow_offers = true; //Disables the ability to make offers ?>
									<h6>
										<?php if($expire < time()): ?>
										<?php elseif($status==0): ?>
											This item is currently out.
										<?php elseif(!isset($_SESSION["userid"])): ?>
											You must be logged in to make an offer.
										<?php elseif($_SESSION["userid"]==$_GET["userid"]): ?>
											Please select an offered item below.
											<input type="hidden" value="" name="accept" id="accept_item"/>
										<?php elseif(count($user_items)==0): ?>
											You currently have no items you may offer. Why not <a href="./me/?load=additem" target="_blank">add</a> one?
										<?php else:
												$disallow_offers = false;
											  endif; ?>
									</h6>
								
									<?php if(!$disallow_offers): ?>
										<select name= "offer" id= "offerbox" class= "chosen-select chosen-search" data-placeholder="Please select an item" autocomplete= "off">
											<option></option>
											<?php foreach($user_items as $item):
													if(($item["duedate"]!=0)==$dodue): ?>
														<option value="<?php echo $item["id"]; ?>"><?php echo $item["name"]; ?></option>
											<?php 	endif;
												  endforeach; ?>
										</select>
									<?php endif; ?>
										<input type="hidden" value="" name="withdraw"  id="withdraw_item" />
							</form>
	
							<?php if(count($offers) == 0): ?>
									<h6>No offers yet!</h6>
							<?php else:
									if(is_array($offers)):
										foreach($offers as $offer):
											$item_call = new Item(array("action"=>"get", "filter"=>array("id"=>$offer["id"])));
											$item_info = $item_call->run(true);
											$item_info = $item_info[0];
	
											$owner_call = new User(array("action"=>"get", "id"=>$item_info["usr"]));
											$owner_info = $owner_call->run(true);
											$owner_info = $owner_info[0];
							?>
											<div class="offer" >
												<div class="uk-grid uk_grid_preserve reset_padding">
													<div onclick="window.location='./view.php?itemid=<?php echo $offer["id"]; ?>&userid=<?php echo $item_info["usr"]; ?>';" 
													     class="uk-width-2-10 picture" 
													     style="background-image:url('/imageviewer/?id=<?php echo $offer["id"]; ?>&size=thumbnail');">
													</div>		
													
													<div class="uk-width-6-10 info">
															<div onclick="window.location='./view.php?itemid=<?php echo $offer["id"]; ?>&userid=<?php echo $item_info["usr"]; ?>'">
																<?php echo $item_info["name"]; ?>
																<div class="description">
																	<?php echo $item_info["description"]; ?>
																</div>
														</div>
													</div>
													
													<div class="uk-width-2-10 post_date">
														Offered on <?php echo (date("Y", $offer["timestamp"])!=date("Y")) ? date("F jS", $offer["timestamp"]) : date("F jS", $offer["timestamp"]); ?> <br/>
														<?php if($item_info["duedate"]!=0): ?>
															Due: <?php echo date("m/d/Y", $item_info["duedate"]); ?>
														<?php endif; ?>
													</div>
													
													
													<?php if(isset($_SESSION["userid"])): ?>												
														<?php if($status==0): //If this item is out... ?>
															<div class="acc" style="cursor:pointer;">This offer has been accepted</div>
														<?php elseif($_SESSION["userid"]==$owner_info["id"]): //If the current user owns this offer... ?>
															<div class="del" style="cursor:pointer;" onclick="confirm_withdraw('<?php echo $offer["id"]; ?>')">Withdraw Offer</div>
														<?php elseif($_SESSION["userid"]==$_GET["userid"]): //If the current user owns the current item... ?>
															<div class="acc" style="cursor:pointer;" onclick="confirm_accept('<?php echo $offer["id"]; ?>','<?php echo $item_info["name"]; ?>')">Accept Offer</div>
														<?php endif; ?>
													<?php endif; ?>
												</div>

											</div>
							<?php 		endforeach;
									endif;
								  endif; ?>
							</div>
						</div>
					</div>
			</div>			
			

			
			<?php /*
			
			
			<div class="layout-1200 uk-container-center">
				<div id="hold">
					<div id="cover">
						<?php if($status==0): ?>
							<img src="./img/out.png" style="float:right;margin-top:-1px;display:inline-block;margin-right:-1px;">
						<?php endif; ?>
						<div class="pic">
							<a href="#lightbox" data-uk-modal><img id="itempic" class="uk-border-rounded" src="/imageviewer/?id=<?php echo $_GET["itemid"]; ?>&size=medium"></a>
						</div>
						<div id='titcont'>
							<div id="title">
								<h1 class="ttrim" id="itemnameh">
									<?php echo $name; ?>
								</h1>
								<span>
									(<?php echo Lookup::Condition($condition); ?>)
								</span>
							</div>
							<div id="ie">
								<div style="font-size: 18px;" >
									<?php echo ($dodue) ? "Due: " . ((date("Y-m-d", $dd)==date("Y-m-d")) ? "Today" : $relative_dd . " from now") : "Looking for a permanant trade"; ?>
	
									<?php if($emv!=""): ?>
										<br/>
										Worth roughly: <div style="display:inline-block;cursor:pointer;" onclick="window.open('/emv.php');">$ <?php echo $emv; ?>.00</div>
									<?php endif; ?>
									<br/>
									Offer expires on <?php echo (date("Y")!=date("Y",$expire)) ? date("F jS, Y", $expire) : date("F jS", $expire);; ?> at <?php echo date("g:i A", $expire) ?>  (<?php echo $relative_ex; ?> from now)
								</div>
							</div>
							<div id="desc">
								<?php echo $desc; ?>
							</div>
						</div> <!-- End of the title -->
						<div id="repbtn" onclick="$('#embed_box').modal();">Embed</div>
						<div id="qrbtn"  onclick="$('#qr_box').modal();">QR Code</div>
					</div>
					<div id="primary">
						<div id="lefti">
							<ul class="nav nav-tabs" role="tablist" id="item_tabs">
							  <li class="active"><a href="#owner_info" role="tab" data-toggle="tab">About <?php echo $afname; ?></a></li>
							  <li><a href="#other_items" role="tab" data-toggle="tab">More From <?php echo $afname; ?></a></li>
							  <li><a href="#similar_items" role="tab" data-toggle="tab">More Like This</a></li>
							</ul>
	
							<div class="tab-content">
							  	<div class="tab-pane active" id="owner_info">
									<img class="profpic" src="/profile.php?id=<?php echo $aid; ?>&load=image" onclick="window.location='profile.php?id=<?php echo $aid; ?>';" ?>
									<div class="infotxt">
										<div onclick="window.location='profile.php?id=<?php echo $aid; ?>';"><?php echo "$afname $alname"; ?></div>
	
										<div id="uinfo">
											Member since <?php echo date("F j, Y", $jdate); ?> <br/>
											<?php echo (!is_array($agender)&&!in_array("gender", $privacy)) ? $agender . "<br/>" : ""; ?>
											<?php echo (!is_array($adob)&&(trim($adob)!=0)&&(!in_array("dob", $privacy))) ?  getRelativeDT(time(), $adob) . " old<br/>" : ""; ?>
										</div>
									</div>
							  	</div>
							  	<div class="tab-pane" id="other_items">
									<?php if(count($other_items)<=1): ?>
											<br><div style="font-size:14px;margin-left:25px;margin-bottom:20px;color:dimgray;">Nothing to show here!</div>
									<?php else:
											for($i = 0; $i < 3; $i++):
												if((isset($other_items[$i]))&&($other_items[$i]["id"]!=$_GET["itemid"])):
									?>
													<div class="item">
														<img class="itempic" src="./imageviewer/?id=<?php echo $other_items[$i]["id"]; ?>" onclick="window.location='/view.php?itemid=<?php echo $other_items[$i]["id"]; ?>&userid=<?php echo $other_items[$i]["usr"]; ?>';" >
														<div class="infotxt">
										       				<div onclick="window.location='/view.php?itemid=<?php echo $other_items[$i]["id"]; ?>&userid=<?php echo $other_items[$i]["usr"]; ?>';"><?php echo $other_items[$i]["name"]; ?></div>
															<div id="iinfo">
																Posted <?php echo getRelativeDT(time(), $other_items[$i]["adddate"]); ?> ago<br/>
																Currently has <?php echo count(json_decode($other_items[$i]["offers"])); ?> offers <br/>
																Seeking a <?php echo ($other_items[$i]["duedate"]!=0) ? "temporary" : "permanant"; ?> trade <br/>
																<br/>
															</div>
														</div>
													</div>
									<?php 		endif;
										  	endfor; ?>
											<div class="item_viewmore" onclick="window.location='/search.php?keyword=<?php echo urlencode("User:$aid"); ?>';">
												<div class="inner">View More</div>
											</div>
									<?php endif; ?>
							  	</div>
							  	<div class="tab-pane" id="similar_items">
									<?php if(count($sim_items)<=1): ?>
											<br><div style="font-size:14px;margin-left:25px;margin-bottom:20px;color:dimgray;">Nothing to show here!</div>
									<?php else:
											for($i = 0; $i < 3; $i++):
												if((isset($sim_items[$i]))&&($sim_items[$i]["id"]!=$_GET["itemid"])):
									?>
													<div class="item">
														<img class="itempic" src="./imageviewer/?id=<?php echo $sim_items[$i]["id"]; ?>" onclick="window.location='/view.php?itemid=<?php echo $sim_items[$i]["id"]; ?>&userid=<?php echo $sim_items[$i]["usr"]; ?>';" >
														<div class="infotxt">
										       				<div onclick="window.location='/view.php?itemid=<?php echo $sim_items[$i]["id"]; ?>&userid=<?php echo $sim_items[$i]["usr"]; ?>';"><?php echo $sim_items[$i]["name"]; ?></div>
															<div id="iinfo">
																Posted <?php echo getRelativeDT(time(), $sim_items[$i]["adddate"]); ?> ago<br/>
																Currently has <?php echo count(json_decode($sim_items[$i]["offers"])); ?> offers <br/>
																Seeking a <?php echo ($sim_items[$i]["duedate"]!=0) ? "temporary" : "permanant"; ?> trade <br/>
																<br/>
															</div>
														</div>
													</div>
									<?php 		endif;
										  	endfor; ?>
											<div class="item_viewmore" onclick="window.location='/search.php?keyword=<?php echo urlencode("User:$aid"); ?>';">
												<div class="inner">View More</div>
											</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
	
						<div id="righti">
							<div id="offerbrd">
								<h2>Make an Offer</h2>
								<form id="itemform" action="/<?php echo $url; ?>" method="post">
								<?php $disallow_offers = true; //Disables the ability to make offers ?>
								<div class="notxt">
									<?php if($expire < time()): ?>
										This item has expired. No offers can be made or accepted during this time.<br/> <a target="_blank" href="/expire.php">How come?</a>
									<?php elseif($status==0): ?>
										This item is currently out.
									<?php elseif(!isset($_SESSION["userid"])): ?>
										You must be logged in to make an offer.
									<?php elseif($_SESSION["userid"]==$_GET["userid"]): ?>
										Please select an offered item below.
										<input type="hidden" value="" name="accept" id="acceptitem"/>
									<?php elseif(count($user_items)==0): ?>
										You currently have no items you may offer. Why not <a href="./me/?load=additem" target="_blank">add</a> one?
									<?php else:
											$disallow_offers = false;
										  endif; ?>
								</div>
								
								<?php if(!$disallow_offers): ?>
											<select name= "offer" id= "offerbox" class= "chosen-select chosen-search" data-placeholder="Please select an item" onchange= "document.getElementById('itemform').submit();" autocomplete= "off">
												<option></option>
												<?php foreach($user_items as $item):
														if(($item["duedate"]!=0)==$dodue): ?>
															<option value="<?php echo $item["id"]; ?>"><?php echo $item["name"]; ?></option>
												<?php 	endif;
													  endforeach; ?>
											</select>
								<?php endif; ?>
									<input type="hidden" value="" name="withdraw"  id="withdrawitem" />
								</form>
	
								<?php if(count($offers) == 0): ?>
									<div class="notxt">No offers yet!</div>
								<?php else:
										if(is_array($offers)):
											foreach($offers as $offer):
													$item_call = new Item(array("action"=>"get", "filter"=>array("id"=>$offer["id"])));
													$item_info = $item_call->run(true);
													$item_info = $item_info[0];
	
													$owner_call = new User(array("action"=>"get", "id"=>$item_info["usr"]));
													$owner_info = $owner_call->run(true);
													$owner_info = $owner_info[0];
												?>
												<div class="offer" >
													<div class="uofu">
															<img onclick="window.location='./view.php?itemid=<?php echo $offer["id"]; ?>&userid=<?php echo $item_info["usr"]; ?>'" class="offer_pic" src="/imageviewer/?id=<?php echo $offer["id"]; ?>&size=small" />
															<div class="uofd">
																Offered on <?php echo (date("Y", $offer["timestamp"])!=date("Y")) ? date("F jS", $offer["timestamp"]) : date("F jS", $offer["timestamp"]); ?><br/>
																<?php if($item_info["duedate"]!=0): ?>
																	Due: <?php echo date("m/d/Y", $item_info["duedate"]); ?>
																<?php endif; ?>
															</div>
															<div onclick="window.location='./view.php?itemid=<?php echo $offer["id"]; ?>&userid=<?php echo $item_info["usr"]; ?>'" class="oftxt">
																<?php echo $item_info["name"]; ?>
																<div class="ofdesc">
																	<?php echo $item_info["description"]; ?>
																</div>
														</div>
													</div>
													<?php if($status==0): //If this item is out... ?>
														<div class="acc" style="cursor:pointer;">This offer has been accepted</div>
													<?php elseif($_SESSION["userid"]==$owner_info["id"]): //If the current user owns this offer... ?>
														<div class="del" style="cursor:pointer;" onclick="confirm_withdraw('<?php echo $offer["id"]; ?>')">Withdraw Offer</div>
													<?php elseif($_SESSION["userid"]==$_GET["userid"]): //If the current user owns the current item... ?>
														<div class="acc" style="cursor:pointer;" onclick="confirm_accept('<?php echo $offer["id"]; ?>','<?php echo $item_info["name"]; ?>')">Accept Offer</div>
													<?php endif; ?>
												</div>
								<?php 		endforeach;
										endif;
									  endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		*/
												  	?>
		<?php
			mysqli_close($con);	//Close the MySQL connection
			Body::end();
			HTML::end();
		?>
