<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
$item_call   = new Item(array("action"=>"get", "filter"=>array("usr"=>$_GET["id"])));
$user_items  = $item_call->run(true);
?>

<div id="item_board" class="profile_tab_content">
	<?php if(count($user_items)==0): ?>
		<h5>This user currently has no items</h5>
	<?php else: ?>
		<?php	foreach($user_items as $item): ?>
				<div class="uk-width-1-1 uk-align-center"> 
					<div class="item" onclick="window.location='/view.php?itemid=<?php echo $item["id"]; ?>&userid=<?php echo $item["usr"]; ?>';">
						<div class="uk-grid uk-grid-preserve reset_padding">
							<div class="uk-width-4-6 info">
								<div class="header"><?php echo $item["name"]; ?></div>
									<div class="description"><?php echo $item["description"]; ?></div>
									<div class="overview uk-grid">
										<div class="uk-width-1-3" title="Number of Offers">
											<span class="uk-icon-cube"></span> <?php echo (count(json_decode($item["offers"],true))) ? count(json_decode($item["offers"], true)) : 0; ?> 
										</div>
										<div class="uk-width-1-3" title="View Count">
											<span class="uk-icon-eye"></span> <?php echo $item["views"]; ?>
										</div>
										<div class="uk-width-1-3" title="Estimated Market Value (EMV)">
											<span class="uk-icon-usd"></span> <?php echo (strlen($item["emv"])!=0) ? $item["emv"] : 0; ?>
										</div>
									</div>
							</div>
							<div class="uk-width-2-6">
								<div style="background:url('/imageviewer/?id=<?php echo $item["id"]; ?>&size=medium') no-repeat center center;" class="thumbnail"> 
									<div class="gradient"></div>
								</div>
								</div>
							</div>
						</div>
					</div>		
				<?php endforeach; 
		endif; 			
	?>
</div>