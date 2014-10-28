<!DOCTYPE html><html><head></head><body onload="redirect();">
<?php

	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

	$redirect_url = "";

	//Get the entire POST array and store it into a modifiable variable
	$post_array = $_POST;

	//And remove the action attribute from it
	unset($post_array["action"]);

	//If there is an image attribute...
	if(trim($post_array["image"])!="")
	{
		//conver the image from base 64 to raw binary
		$post_array["image"] = base64_decode($post_array["image"]);
	}

	$post_array["condition"] = intval($post_array["condition"]) + 1;
	$post_array["category"]  = intval($post_array["category"]) + 1;

	//If an item ID was specified...
	if(trim($post_array["id"])!="")
	{
		//Get the item ID from the array
		$item_id = $post_array["id"];

		//Then remove it from the array
		unset($post_array["id"]);

		//If there is no item image
		if(isset($post_array["image"])&&trim($post_array["image"])=="")
		{
			//Don't modify the image. Remove it from the update array.
			unset($post_array["image"]);
		}

		//Run an update API call using the item ID
		$newItem = new Item(array("action"=>"update", "id"=>$item_id, "fields"=>$post_array));
		$code = $newItem->run(true);
		$redirect_url = "/view.php?itemid=$item_id&userid={$_SESSION["userid"]}";
	}
	else
	{
		//remove any ID attribute from the array
		unset($post_array["id"]);

		//And use the rest of the info to create a new item
		$newItem = new Item(array("action"=>"create", "fields"=>$post_array));
		$new_item_id = $newItem->run(true);

		$redirect_url = "/view.php?itemid=$new_item_id&userid={$_SESSION["userid"]}";
	}

	$redirect_script = <<<SCRIPT
		<script type="text/javascript">
			function redirect() {
				parent.location = "$redirect_url";
			}
		</script>
		<script type="text/javascript" src="/lib/jquery-1.10.2/jquery-1.10.2.min.js"></script>
SCRIPT;

	echo $redirect_script;
?>
</body></html>
