<?php
	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
	if(!isset($_SESSION["userid"])){ die; }
?>

<div onclick="$('#postbox').modal();" class="icon fa fa-plus"></div>

<?php
	$my_exchanges   = new Exchange(array("action"=>"find"));
	$exchange_array = $my_exchanges->run();
?>
<div onclick="display_menu('#exchange_menu', this);" id="exchange_icon" class="<?php if(count($exchange_array)!=0): ?> static_active <?php endif; ?> icon fa fa-exchange">
	<?php
	 	  if(count($exchange_array)!=0): ?>
			<div class="badge" onclick="display_menu('#exchange_menu', this);"><?php echo count($exchange_array); ?></div>
	<?php endif; ?>
	<div id="exchange_menu" class="menu">
		<div class="tip"></div>
		<h1>My Exchanges</h1>
		<div id="container">
			<?php
				if(count($exchange_array)==0): ?>
					<h6>You have no current exchanges</h6>
				<?php else:
					foreach($exchange_array as $exchange):
						$item1_obj  	= new Item(array("action"=>"get", "filter"=>array("id"=>$exchange["item1"])));
						$item1_info 	= $item1_obj->run(true);
						$item1_info		= $item1_info[0];

						$item2_obj  = new Item(array("action"=>"get", "filter"=>array("id"=>$exchange["item2"])));
						$item2_info = $item2_obj->run(true);
						$item2_info = $item2_info[0];

						//Make sure item 1 is the item the current user owns
						if($item2_info["usr"]==$_SESSION["userid"])
						{
							$temp_array = $item1_info;
							$item1_info = $item2_info;
							$item2_info = $temp_array;
						}

						$exchange_status = "Waiting for you to review your partner";

						$exchange_status_icon = "glyphicon glyphicon-ok";
				?>
					<div onclick="window.location='/exchange.php?offerid=<?php echo $exchange["id"]; ?>'" class="exchange">
						<div class="content">
							<div class="text">
								<div class="title"><?php echo "<span class='primary'>{$item1_info["name"]}</span> <span class='small_text glyphicon glyphicon-play'></span> {$item2_info["name"]}"; ?></div>
								<div class="sub">
									<?php if($exchange["date"]!=0):
											if($exchange["date"]<time()): ?>
												Met on <?php echo date("F jS, Y", $exchange["date"]); ?>
									<?php   else: ?>
												Meeting on <?php echo date("F jS, Y", $exchange["date"]); ?>
									<?php   $exchange_status 	  = "Waiting to meet up";
											$exchange_status_icon = "glyphicon glyphicon-refresh";
										    endif;
									      endif; ?>
									<br/>
										<?php if(($item1_info["duedate"]==0)&&($item2_info["duedate"]==0)): ?>
											Items are not due for return
										<?php else: ?>
											<?php
												$duedate = ($item1_info["duedate"]<$item2["duedate"]) ? $item1_info["duedate"] : $item2_info["duedate"];
												  if($duedate<time()): ?>
													Items returned on
											<?php else: ?>
													Items due for return on
											<?php endif;
												  $exchange_status 		= "Waiting for return items";
												  $exchange_status_icon = "glyphicon glyphicon-dashboard";
												  echo date("F jS, Y", $duedate); ?>
										<?php endif; ?>
									<br/>
									<?php echo $exchange_status; ?>
								</div>
							</div>
							<div class="icon <?php echo $exchange_status_icon; ?>"></div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div onclick="display_menu('#notification_menu', this);" id="notify_icon" class="icon fa fa-globe">
	<?php
		$user_obj	    = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
		$user_info      = $user_obj->run(true);
		$user_info 		= $user_info[0];
		$notifications  = $user_info["notify"];

		//Unread message count
		$new_count = 0;
		foreach($notifications as $notification)
		{
			if($notification["read"]!=1)
			{
				$new_count++;
			}
		}

		  if($new_count!=0):
	?>
		<div class="badge" onclick="display_menu('#notification_menu', this);"><?php echo $new_count; ?></div>
	<?php endif; ?>

	<div id="notification_menu" class="menu">
		<div class="tip"></div>
		<h1>My Notifications</h1>
		<div id="container">
			<?php
				if(count($notifications)==0): ?>
				<h6>You have no current exchanges</h6>
			<?php
				else:
					foreach($notifications as $notification):
			?>
				<div onclick="window.location='<?php echo "./{$notification['link']}&ref=n{$notification['id']}"; ?>'" class="notification">
					<div class="content">
						<div class="text">
							<div class="title"><?php echo $notification["message"]; ?></div>
							<div class="date">
								<?php echo getRelativeDT(time(), $notification["date"]); ?> ago
							</div>
						</div>
						<div class="icon fa <?php echo ($notification["read"]==1) ? "fa-circle-o" : "fa-circle"; ?>" ></div>
					</div>
				</div>
			<?php endforeach;
		endif;
			?>
		</div>
	</div>
</div>
