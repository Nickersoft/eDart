<?php

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

if(!isset($_GET["item"])) { echo "No item ID specified"; die; }

$id = $_GET["item"];

$item_obj  = new Item(array("action"=>"get", "filter"=>array("id"=>$id)));
$item_info = $item_obj->run(true);

if(count($item_info)==0) { echo "Item does not exist"; die; }

$item_info = $item_info[0];

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="/fonts/Vegur/stylesheet.css">
		<link rel="stylesheet" type="text/css" media="screen" href="/lib/min/?g=css">
	</head>
	<body>
		<div id="embed_parent" onclick="window.open('/view.php?itemid=<?php echo $item_info['id']; ?>&userid=<?php echo $item_info['usr']; ?>', '_blank');" >
			<div id="header"><img src="/img/edartlogo2.png"></div>
			<div id="body">
				<img id="picture" src="/imageviewer/?id=<?php echo $item_info["id"]; ?>&size=small">
				<div id="info">
					<h1><?php echo $item_info["name"]; ?></h1>
					<div id="details">
						Posted on <?php echo date("F jS, Y", $item_info["adddate"]); ?> <br/>
						Currently has <?php echo count(json_decode($item_info["offers"])); ?> offers <br/>
						<?php if($item_info["status"]==1): ?>
							Available until <?php echo date("F jS, Y", $item_info["expiration"]); ?> <br/>
						<?php else: ?>
							Not currently available for trade <br/>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
