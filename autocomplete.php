[
<?php
	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
	
	$all_items 		= new Item(array("action"=>"get"));
	$all_item_info	= $all_items->run(true);
	
	for($i = 0; $i < count($all_item_info); $i++):
		$item = $all_item_info[$i];
?>
    {"value":"<?php echo $item["name"]; ?>", "title":"<?php echo $item["name"]; ?>", "url":"/view.php?&itemid=<?php echo $item["id"]; ?>&userid=<?php echo $item["usr"]; ?>", "text":"<?php $short_desc = explode($item["description"], '.'); echo $short_desc[0]; ?>"}
<?php
	if($i!=(count($all_item_info)-1))
		echo ",";
	
	endfor; ?>

]