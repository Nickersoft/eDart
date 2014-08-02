<?php
	
	$one_week_ago = strtotime("-1 weeks");
	$current_date = time();

	$con = mysqli_connect(host(), username(), password(), mainDb());

	$user_query = mysqli_query($con, "SELECT * FROM `usr` WHERE `joindate` > " . $one_week_ago);
	while($row = mysqli_fetch_array($user_query))
	{
		$message = "Hey! Welcome to eDart! ";
	}
	mysqli_close($con);
?>