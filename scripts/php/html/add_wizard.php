<?php include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; ?>
<div id="postbox" class="uk-modal">
	<div class="uk-modal-dialog">
		<div class="uk-modal-header">
			<a class="uk-modal-close uk-close"></a>
			<div data-default="New Item" id="wz_title">New Item</div>
		</div>
		<div class="uk-modal-content">
  				<div id="add_wizard">
  					<div class="add_slide" id="basics">

       					<h1>Basics</h1>
       					<p>Here you can enter the name of your item, a brief description, and post a picture of your item. Remember, the more information you post, the more legitmate your items will seem. When you're ready to continue, click out of all text areas, then click 'Next'.</p>

	       				<div id="ai_left">

	       					<div id="wz_picture">
								<div id="wz_loader">
									<img src="/img/719.GIF">
									Uploading to the</br>interwebs...
								</div>
								<div id="overlay" onclick="$('#item_image_browser').click();">
							<div id="inner">
								Upload Image
							</div>
						</div>
					</div>

					<form id="itemupload_image_form" method="post" target="hidden_frame" action="/scripts/php/ajax/item/image_upload.php" enctype="multipart/form-data">
						<input style="z-index:-200" accept="image/*" onchange="start_upload();" type="file" name="item_upload" id="item_image_browser" />
					</form>

					<iframe name="hidden_frame" id="hidden_frame" style="visibility:hidden;"></iframe>

	       				</div>

	       				<div id="ai_right">
	       					<input onchange="set_info();" name="name" maxlength="30" id="wz_name" class="inpt small_text" placeholder="Item Name" autocomplete="off" type="text" />
	      					<textarea name="itemdesc" rows="3" class="inpt iteminfo" id="wz_desc" maxlength="100" placeholder="Enter a brief description"></textarea>
	       				</div>
	       			</div><div class="add_slide" id="classifications">

       					<h1>Classifications</h1>
       					<p>Here you can tell users what condition your item is in, whether it be brand new or whether you've been using it for the past ten years. You can also select which category you wish to place your item in, so users can find it easier. </p>

	       				<div id="ai_left">
							<h2>Category</h2>
	       					<select id="wz_category" class="small_text selectbox">
								<?php foreach(Lookup::Category() as $category): ?>
							<option><?php echo $category["text"]; ?></option>
						<?php endforeach; ?>
       					</select>
       				</div>

       				<div id="ai_right">
						    <h2>Condition</h2>
       						<select id="wz_condition" class="small_text selectbox">
								<?php foreach(Lookup::Condition() as $condition): ?>
       								<option><?php echo $condition["text"]; ?></option>
       							<?php endforeach; ?>
	       						</select>
						</div>
	       			</div><div class="add_slide" id="pickup">

       					<h1>Pickup Location</h1>
       					<p>In order to make an exchange, you'll need to meet with your tradee in person. Select one of our "safety-approved" meetup locations from the dropdown below. When you're ready to continue, click 'Next'. </p>

							<select id="wz_pickup" class="dropdown">
								<option>Rubin Campus Center</option>
								<option>George C. Gordon Library</option>
								<option>Morgan Commons</option>
								<option>Salisbury Laboratories</option>
								<option>Fuller Laboratories</option>
								<option>Recreation Center</option>
								<option>The Quad</option>
							</select>

	       			</div><div class="add_slide" id="expiration">

       					<h1>Expiration</h1>
       					<p>In order to post an item, you must have an expiration date (not to be confused with a due date, which is next). This is how long the item will be available to accept offers. Once you've selected an expiration date, click 'Next'. </p>

				            <div class="form-group">
				                <div class='input-group date uk-align-center datebox' id="wz_expiration">
				                    <input type='text' class="form-control" id="wz_expiration_str" readonly />
				                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
						    </div>

	       			</div><div class="add_slide" id="duedate">

       					<h1>Due Date</h1>
       					<p>Now, this step is <b>optional</b>. Some of us don't like letting go of things for good. Some of us prefer the concept of <i>loaning</i>. By setting a due date for your item, you are essentially <i>loaning</i> your item to another user until the specified date. Also note that if your item has a due date, you can only make offers on other items that also have due dates. If you don't set a due date, you can only make offers on items that also don't have due dates. When you are ready to continue, click "Next".</p>

	       				<div id="check_container">
       						<input type="checkbox" id="wz_dodue" name="dodue" onchange="check_dodue();" checked />
       						Yes, I would like to set a due date
       					</div>

			            <div id="duedate_container" class="form-group">
			                <div class='input-group date uk-align-center datebox' id='wz_duedate' >
			                    <input type='text' class="form-control"  id='wz_duedate_str' readonly />
			                    <span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
			                    </span>
			                </div>
					    </div>

	       			</div><div class="add_slide" id="done">
       					<h1>...and you're done!</h1>
       					<p>You can review your information below. To publish your new item, click "Post" below.</p>
       					<table id="item_info_table">
       						<tr>
       							<th>Name:</th>
       							<td id="disp_item_name">None</td>
       						</tr>
       						<tr>
       							<th>Description:</th>
       							<td id="disp_item_description">None</td>
       						</tr>
       						<tr>
       							<th>Category:</th>
       							<td id="disp_item_category">None</td>
       						</tr>
       						<tr>
       							<th>Condition:</th>
       							<td id="disp_item_condition">None</td>
       						</tr>
       						<tr>
       							<th>Pickup Location:</th>
       							<td id="disp_item_pickup">Nowhere</td>
       						</tr>
       						<tr>
       							<th>Expiration Date:</th>
       							<td id="disp_item_expiration">None</td>
       						</tr>
       						<tr>
       							<th>Due Date:</th>
       							<td id="disp_item_duedate">None</td>
       						</tr>
       					</table>
	       			</div>


       					<form id="submit_item_form" target="hidden_frame" action="/scripts/php/ajax/item/add.php" method="POST">
		       				<input type="hidden" id="itemupload_code" 	name="image" 	value="" />
							<input type="hidden" id="itemupload_id" 	name="id"  		value="" />

       						<input type="hidden" id="itemupload_name" 			name="name" />
       						<input type="hidden" id="itemupload_description" 	name="description" />
       						<input type="hidden" id="itemupload_category"		name="category" />
       						<input type="hidden" id="itemupload_condition" 		name="condition" />
       						<input type="hidden" id="itemupload_stadd1" 		name="stadd1" />
       						<input type="hidden" id="itemupload_duedate" 		name="duedate" />
       						<input type="hidden" id="itemupload_expiration" 	name="expiration" />

       					</form>

				</div>
		</div>
		
			<div class="uk-modal-footer">
  				<ul id="pacer">
  					<li class="bullet"></li>
  					<li class="bullet"></li>
  					<li class="bullet"></li>
  					<li class="bullet"></li>
  					<li class="bullet"></li>
  					<li class="bullet"></li>
  				</ul>

    			<button type="button" id="add_back" class="button_primary blue" onclick="pull_slide();">
    				Back
    			</button>

    			<button type="button" id="add_next" data-action="next" onclick="activate_next();" class="button_primary green">
    				Next
    			</button>
			</div>
		</div>
</div>
