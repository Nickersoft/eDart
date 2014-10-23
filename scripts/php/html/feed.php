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
		<div class="uk-modal-header">
			<a class="uk-modal-close uk-close"></a>
			<div class="uk-modal-title">Request a New Item</div>	
		</div>
		<div class="uk-modal-content">
			<p>Looking for an item no one's posted yet? File an (anonymous) item request, and we'll suggest that nearby people post it!</br>
			<input type="text" id="item-request-name" placeholder="Item Name" class="uk-width-1-2 uk-align-center"/>
			When you're done, click "Request" below to send your item request.
			</p>
		</div>
		<div class="uk-modal-footer">
			<div class="uk-align-right">
				<button class="button_primary blue uk-modal-close">Cancel</button>
				<button class="button_primary green" onclick="post_item_request();">Request</button>
			</div>			
		</div>
	</div>
</div>


<div class="layout-1200 uk-container-center">
	<div class="uk-grid uk-grid-preserve" id="home">
			<div class="uk-width-small-1-5">
		
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
						while ($user = mysqli_fetch_array($user_query)): 
							$display_name = htmlentities(htmlentities(ucwords($user["fname"] . " " . $user["lname"])));
						?>
						
							<div class="uk-width-1-4">
								<a data-uk-tooltip="{pos:'top'}" title="<?php echo $display_name; ?>" href="/profile.php?id=<?php echo $user["id"]; ?>">
									<img class="uk-border-circle" src="/profile.php?id=<?php echo $user["id"]; ?>&load=image&size=small">
								</a>
							</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	
		<div class="uk-width-small-4-5">
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
		
			<div class="uk-width-1-1">
				<div class="uk-grid uk-grid-preserve reset_padding">
					<div class="uk-width-medium-4-5 uk-width-small-1-1">
						<div class="uk-grid reset_padding" id="main_board">
						</div> 
					</div>
			
					<div class="uk-width-1-5 uk-hidden-medium">
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
				</div>
			</div>

		</div>
	</div>