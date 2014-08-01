<?php
//Listens for new messages

	include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

	$con = mysqli_connect(host(), username(), password(), mainDb());

	$id  = $_POST["id"];

	function message_len()
	{
		global $id;
		$ex 	= new Exchange(array("action"=>"get", "id"=>$id));
		$ex_inf = $ex->run();
		
		$conv 	= json_decode($ex_inf[0]["messages"], true);
		$conv_d = (is_array($conv)) ? $conv : array();

		return count($conv_d);
	}

	if(!isset($id))
	{
		echo 401;
	}
	else if(!isset($_SESSION["userid"]))
	{
		echo 403;
	}
	else
	{
		session_write_close();
		$ml = message_len($id);
		$start_length 	= $ml;
		$current_length = $ml;
		$timeout = 0;
		
		while( ($current_length <= $start_length) && ($timeout <= 29) )
		{
			sleep(1);
			clearstatcache();
			$current_length = message_len($id);
			$timeout++;
			if($timeout>29)
			{
				exit;
				break;
			}
		}
		
		$thisObj  = new Exchange(array("action"=>"get", "id"=>$_POST["id"]));
		$thisInfo = $thisObj->run();
		$convInfo = $thisInfo[0]["messages"];
		echo $convInfo;
	}

?>