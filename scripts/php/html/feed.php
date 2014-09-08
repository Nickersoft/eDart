<?php

include_once $_SERVER ["DOC_ROOT"] . "/scripts/php/core.php"; //Core functionality

//Get all items in the database
$all_items 	= new Item(array("action"=>"get", "sort"=>"adddate", "order"=>"desc"));
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

<div id="request_win" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-modal-header">Request a New Item</div>
		<p>Looking for an item no one's posted yet? File an (anonymous) item request, and we'll suggest that nearby people post it!</br>
		<input type="text" placeholder="Item Name" class="uk-width-1-2 uk-align-center"/>
		When you're done, click "Request" below to send your item request.
		</p>
		<div class="uk-modal-footer">
			<div class="uk-align-right">
				<button class="button_primary blue">Cancel</button>
				<button class="button_primary green">Request</button>
			</div>			
		</div>
	</div>
</div>


<div class="layout-978 uk-container-center">
	<div class="uk-grid uk-grid-preserve" id="home">
		<?php 
		
			//Finds the latest item that has an image
			$item_obj 	= new Item(array("action"=>"get","sort"=>"adddate","order"=>"DESC"));
			$item_array = $item_obj->run(true);
			
			if(count($item_array)!=0):
				$data_url   = "";
				$count = 0;
				while (trim($item_array[$count]["image"])=="") {
					if(($count + 1) > (count($item_array)-1))
					{
						break;
					}
					else 
					{
						$count++;
					}
				}
				
				if(trim($item_array[$count]["image"])!=""):
					$feat_item				= $item_array[$count];
					$feat_data_binary 		= $item_array[$count]["image"];
					$feat_data_binary_blur	= WideImage::loadFromString($feat_data_binary)->applyFilter(IMG_FILTER_GAUSSIAN_BLUR);
					$feat_data_url  		= "data:image/jpg;base64," . base64_encode($feat_data_binary_blur->asString('jpg'));
					$feat_img_height		= $feat_data_binary_blur->getHeight();
		?>
			<div class="uk-width-1-1 uk-hidden-small">
				<div data-height="<?php echo $feat_img_height; ?>" style="background:url('<?php echo $feat_data_url; ?>');" id="home_cover" class="uk-cover-background  uk-position-relative">
				    <div class="uk-position-cover uk-width-1-1 uk-flex uk-flex-left uk-flex-middle">
			    		<div class="gradient">
							<h6><span>featured</span> in <?php echo Lookup::Category($feat_item["category"]); ?></h6>
			    		    <h1><?php echo ucwords(trim($feat_item["name"])); ?></h1>
			    		    <ul>
			    		    	<?php if(trim($feat_item["emv"])!=""): ?>
			    		    		<li><strong>Worth roughly</strong>: $<?php echo $feat_item["emv"]; ?></li>
			    		    	<?php endif; ?>
			    		    	
			    		    	<li><strong>Offers:</strong> <?php echo count(json_decode($feat_item["offers"])); ?></li>
			    		    	<li><strong>Status:</strong> <?php echo ($feat_item["status"]==1) ? "In" : "Out"; ?><li>
			    		    	<li><strong>Trade Type:</strong> <?php echo ($feat_item["duedate"]==0) ? "Permanent" : "Temporary"; ?>
			    		    </ul>
			    		    <a href="/view.php?itemid=<?php echo $feat_item["id"]; ?>&userid=<?php echo $feat_item["usr"]; ?>" id="view_button" class="button_primary dark text_medium">View Item</a>
			    		    <a href="/profile.php?id=<?php echo $feat_item["usr"]; ?>"><img class="user_picture uk-border-circle" src="/profile.php?id=<?php echo $feat_item["usr"]; ?>&load=image&size=small" /></a>
			    		</div>
		            </div>
				</div>
			</div>
			<?php endif; 
			endif; 
		?>
		
		<div class="uk-width-small-1-4">
		
			<a href="#request_win" style="margin-bottom:10px;" class="uk-hidden-small uk-width-1-1 uk-button" type="button" data-uk-modal>Request</a>
			
			<div class="child uk-hidden-small">
				<div class="title">Categories</div>
	    		<ul id="category_list" class="uk-nav uk-nav-side">
	    			<li>
	    				<a href="javascript:void(0);" onclick="select_recent(this);" >Recent 
	    					<?php if(count($items_info)!=0): ?>
	    					<span style="margin-top:2px;" class="uk-float-right uk-flex-middle uk-badge">
		    					<?php 
		    						echo count($items_info); 
		    					?>
	    					</span> 
	    					<?php endif; ?>
	    				</a>
	    			</li>
	    			<li><a href="javascript:void(0);" onclick="select_popular(this);">Popular</a></li>
	    			<?php if(count($category_array)!=0):  
							foreach($category_array as $category): ?>
								<li><a href="javascript:void(0);" onclick="select_category('<?php echo $category; ?>', this);">
									<?php echo Lookup::Category($category); ?></a></li>
					<?php   endforeach;
					 endif; ?>
				</ul>
			</div>
			
			<div class="uk-visible-small">
				<select id="category_select" class="uk-width-1-1">
					<option>
						Recent
						<?php if(count($items_info)!=0): ?>
	    				(
	    					<?php 
		    					echo count($items_info); 
		    				?>
		    			) 
	    				<?php endif; ?>
					</option>
					<option>Popular</option>
					<?php if(count($category_array)!=0):  
							foreach($category_array as $category): ?>
								<option value="<?php echo $category; ?>"><?php echo Lookup::Category($category); ?></option>
					<?php   endforeach;
					 endif; ?>
				</select>
			</div>
			
			<div class="child uk-hidden-small">
				<div class="title">Newest Members</div>
				<div class="uk-grid uk-grid-small" style="padding-left:10px;">
					<?php
					    $user_query = mysqli_query($con, "SELECT * FROM usr ORDER BY join_date DESC LIMIT 12");
						while ($user = mysqli_fetch_array($user_query)): ?>
							<div class="uk-width-1-4">
								<a data-uk-tooltip="{pos:'top'}" title="<?php echo ucwords($user["fname"] . " " . $user["lname"]); ?>" href="/profile.php?id=<?php echo $user["id"]; ?>">
									<img class="uk-border-circle" src="/profile.php?id=<?php echo $user["id"]; ?>&load=image&size=small">
								</a>
							</div>
					<?php endwhile; ?>
				</div>
			</div>
			
			<div class="child uk-hidden-small">
				<div class="title">Similar Stuff</div>
				<div class="uk-align-center" style="padding-left:10px;">
					<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
						<ins class="adsbygoogle"
						     style="display:inline-block;width:120px;height:240px"
						     data-ad-client="ca-pub-5519668009926053"
						     data-ad-slot="9417230623"></ins>
						<script>
						(adsbygoogle = window.adsbygoogle || []).push({});
					</script>	
				</div>
			</div>
		</div>
		
		<div class="uk-width-small-3-4">
			<div class="uk-grid reset_padding" id="main_board">
			</div> 
		</div>
	</div>
<?php /*
<div id="feed_cont">
	<div id="feed">
		<div id="feed_right">
			<div id="newest" class="feed_child">
				<h3>Featured</h3>
				<?php if(count($get_items)!=0): ?>
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
				<?php else: ?>
					<p>There is no featured item today</p>
				<?php endif; ?>
			</div>
			
			<div id="requests" class="feed_child">
				<h3>Nearby Item Requests</h3>
				<section id="request_list">
					<?php 
						$item_req_obj = new Feed();
						$item_arr = $item_req_obj->local_items();
						if(count($item_arr)!=0):
					?>
					<p>Have an item? Why not post it?</p>
					<ul id="request_container">
					<?php
						for($i = 0; $i < 5; $i++):
							if(isset($item_arr[$i])):
								$item = $item_arr[$i];
					?>
						<li><?php echo ucwords($item["name"]); ?>
					<?php endif; 
					endfor; ?>
					</ul>
					<?php endif; ?>
					<p>
					 Want to make a request? <br/> <a onclick="display_post_irequest();">Click here.</a>
					</p>
				</section>
				
				<section id="request_post">
					<a onclick="display_list_irequest();" class="pull_right fa fa-times" style="color:gray;"></a>
					<p>Type the name of your item below, then click 'Submit'</p>
					<input id="item-request-name" type="text" class="small_text fill_x reset_margin" placeholder="Item Name" />
					<button onclick="post_irequest();" class="small_text center bbtn" style="margin-top:10px;">Submit</button>
				</section>
				
			
					<p id="request_thanks">Thanks! Keep your eyes open for anyone who posts it!</p>
			</div>
			
			<div id="ads" class="feed_child">
				<h3>Temporary Ads</h3>
				<p>We know you hate them, but just bare with us a little while, okay?</p>
				<div class="center">
					<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
					<!-- eDart Ads 2 -->
					<ins class="adsbygoogle"
					     style="display:inline-block;width:120px;height:240px"
					     data-ad-client="ca-pub-5519668009926053"
					     data-ad-slot="9417230623"></ins>
					<script>
					(adsbygoogle = window.adsbygoogle || []).push({});
					</script>				
				</div>
			</div>
		</div>

		<div id="feed_left">

			<ul id="feed_toolbar">

				<li id="all" onclick="get_recent_activity();" class="glyphicon glyphicon-home"></li>

			</ul>

			<div class="feed_child">
				<h3>Categories</h3>
				<ul id="categories">
					<?php if(count($category_array)!=0):  
							foreach($category_array as $category): ?>
								<li onclick="get_items('<?php echo $category; ?>', '<?php echo Lookup::Category($category); ?>', this);">
									<?php echo Lookup::Category($category); ?>
								<div class="glyphicon glyphicon-play" style="margin-left:5px;"></div>
					<?php   endforeach;
						  else: ?>
						  <p style="text-transform:none;">No categories to display</p>
					<?php endif; ?>
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
</div>*/
?>