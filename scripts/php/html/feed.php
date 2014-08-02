<?php

include_once $_SERVER ["DOC_ROOT"] . "/scripts/php/core.php"; //Core functionality

//Get info about the most recent item
$items = new Item(array("action"=>"get", "sort"=>"adddate", "order"=>"desc"));
$get_items = $items->run();
$recent_item = $get_items[0];

$recent_id			= $recent_item["id"];			//Item ID
$recent_name 		= $recent_item["name"];			//Item name
$recent_price 		= $recent_item["emv"];			//Item EMV
$recent_status		= $recent_item["status"];		//Item status
$recent_duedate		= $recent_item["duedate"];		//Item due date
$recent_expires		= $recent_item["expiration"];	//Item expiration timestamp
$recent_owner_id	= $recent_item["usr"];			//Item owner

//Number of offers on the item
$recent_offercnt  = count(json_decode($recent_item["offers"], true));

//Expiration in relative form
$relative_exp	= getRelativeDT(time(), $recent_expires);

//Let's get the owner's name, shall we?
$owner 			= new User(array("action"=>"get", "id"=>$recent_owner_id));
$owner_info		= $owner->run(true);
$owner_name		= $owner_info[0]["fname"] . " " . $owner_info[0]["lname"];

//Depending on the status code (0, 1, or 2), change the status to a string
switch($recent_status)
{
	case 0:
		$recent_status = "Out";
		break;
	case 1:
		$recent_status = "In";
		break;
	case 2:
		$recent_status = "Offered";
		break;
}

//Get all items in the database
$all_items 	= new Item(array("action"=>"get"));
$items_info = $all_items->run(true);

$category_array = array(); //Will hold all categories

//Push all the categories into the array once
foreach($items_info as $item)
{
	if(!in_array($item["category"], $category_array))
	{
		array_push($category_array, $item["category"]);
	}
}

?>

<div id="feed_cont">
	<div id="feed">
		<div id="feed_right">
			<div id="newest" class="feed_child">
				<h3>Featured</h3>
				<div class="img" style="background:url('/imageviewer/?id=<?php echo $recent_id; ?>') center center no-repeat;"></div>
				<div id="descpnl">
					<h2><a href="/view.php?itemid=<?php echo $recent_id; ?>&userid=<?php echo $recent_owner_id; ?>"><?php echo $recent_name; ?></a></h2>
					<ul>
						<?php if(trim($recent_price)!=""): ?>
								<li>Worth: $<?php echo $recent_price; ?></li>
						<?php endif; ?>

						<li>Current offers: <?php echo $recent_offercnt; ?></li>
						<li>Status: <?php echo $recent_status; ?></li>
						<li>Trade type: <?php echo ($recent_duedate==0) ? "Permanant" : "Temporary"; ?></li>

						<?php if($recent_duedate!=0): ?>
								<li><?php echo date("\D\u\e: F jS, Y \a\\t g:i A", $recent_duedate); ?></li>
						<?php endif; ?>

						<li>Posted by: <a href="/profile.php?id=<?php echo $recent_owner_id; ?>"><?php echo $owner_name; ?></a></li>
						<li><?php echo $relative_exp; ?> left until expiration</li>
					</ul>
				</div>
			</div>
			<div id="ads" class="feed_child">
				<h3>Temporary Ads (Coming Soon)</h3>
				<p>We know you hate them, but just bare with us a little while, okay?</p>
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- Temporary Ads -->
				<ins class="adsbygoogle"
				style="display:inline-block;width:300px;height:600px"
				data-ad-client="ca-pub-5519668009926053"
				data-ad-slot="7900458226"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>
		</div>

		<div id="feed_left">

			<ul id="feed_toolbar">

				<li id="all" onclick="get_recent_activity();" class="glyphicon glyphicon-home"></li>

			</ul>

			<div class="feed_child">
				<h3>Categories</h3>
				<ul id="categories">
					<?php foreach($category_array as $category): ?>
							<li onclick="get_items('<?php echo $category; ?>', '<?php echo Lookup::Category($category); ?>', this);">
								<?php echo Lookup::Category($category); ?>
							<div class="glyphicon glyphicon-play" style="margin-left:5px;"></div>
					<?php endforeach; ?>
				</li>
			</div>

			<div class="feed_child" id="newest_members">
				<h3>Newest Members</h3>
				<?php
				    $user_query = mysqli_query($con, "SELECT * FROM usr ORDER BY join_date DESC LIMIT 4");
					while ($user = mysqli_fetch_array($user_query)): ?>
						<div  class="img">
							<img  onclick="window.location='/profile.php?id=<?php echo $user["id"]; ?>" style="cursor:pointer;" src="/profile.php?id=<?php echo $user["id"]; ?>&load=image&size=small" >
						</div>

				 		<div class="member">
				 			<div class="txtcnt">
				 				<div class="name" style="cursor:pointer;"><a href="/profile.php?id=<?php echo $user["id"]; ?>"><?php echo ucwords("{$user["fname"]} {$user["lname"]}"); ?></a></div>
								<div class="sub"> Joined <?php echo date("F jS", $user["join_date"]); ?></div>
							</div>
						</div>
				<?php endwhile; ?>
			</div>

		</div>

		<div id="feed_title" class="feed_child">
			<div id="inner_title"></div>
		</div>

		<div id="feed_center" class="feed_child">

			<div id="postCont">
				<!--This is where our feed will go-->
				<div id="waitload">Please wait while we get the latest content...</div>
			</div>

		</div>
	</div>
</div>
