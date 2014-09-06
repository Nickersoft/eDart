<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
$newPerson  = new User(array("action"=>"get", "id"=>$_GET["id"]));
$personInfo = $newPerson->run(true);

try { 
	$r = $personInfo[0];
	$rank = json_decode($r["rank"], true); //The user's rankings
}catch(Exception $e) {}
?>

<div id="user_reviews" class="profile_tab_content">
	<?php if(count($rank)==0): ?>
		<h5>This user currently has no user reviews</h5>
	<?php else:
		foreach($rank as $ranking):
			$total = 0;
			foreach($ranking["points"] as $point)
			{
				$total += intval($point);
			}
			$total /= 4;
			$total /= 2;
			$total = round($total);
			?>
			<div class="review">
				<div class="info">
					<div class="rating">
						<?php for($i = 0; $i < 5; $i++): ?>
							<span class="<?php echo ($i <= $total) ? 'uk-icon-star' : 'uk-icon-star-o' ?>"></span>
						<?php endfor; ?>
					</div>
					<?php echo (trim($ranking["description"])!="") ? "&ldquo;{$ranking["description"]}&rdquo;" : ""; ?></p>
					<ul>
						<li>Reliability: <?php echo $ranking["points"][0]; ?></li>
						<li><div class="divide"></div></li>
						<li>Friendliness: <?php echo $ranking["points"][1]; ?></li>
						<li><div class="divide"></div></li>
						<li>Consistency: <?php echo $ranking["points"][2]; ?></li>
						<li><div class="divide"></div></li>
						<li>Overall: <?php echo $ranking["points"][3]; ?></li>
					</ul>
				</div>
			</div>
		<?php endforeach;
	endif; ?>
</div>