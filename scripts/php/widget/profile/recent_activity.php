<?php 
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
?>
<div id="recent_activity" class="profile_tab_content">
	<?php
		$log = getRecentActivity($_GET ["id"]);
		if(count($log)===0): ?>
		<h5>This user has no recent activity to display</h5>
	<?php else: ?>
		<?php	for($i = 0; $i < count ( $log ); $i ++):
			$regdate = date ( "Y-m-d H:i:s", $log[$i]["date"]);
			$reldate = getRelativeDT (time(), $log[$i]["date"]);
			$fulldte = date ( "l, F jS, Y", $log[$i]["date"]); ?>
			<div class="post">
				<div title="<?php echo $fulldte; ?>" class="date"><?php echo $reldate; ?> ago</div>
				<a href="<?php echo $log[$i]["link"]; ?>"><?php echo $log[$i]["string"]; ?></a>
			</div>
		<?php    endfor; ?>
	<?php endif; ?>
</div>