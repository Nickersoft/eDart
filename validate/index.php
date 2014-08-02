<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

if(isset($_SESSION["userid"]))
{
	$user_call = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
	$user_info = $user_call->run(true);
	$user_info = $user_info[0];

	switch($user_info["status"])
	{
		case 0:
			header("Location:/signup/email_sent.php");
			break;
		case 1:
			header("Location:/terms.php");
			break;
		default:
			header("Location:/");
			break;
	}
}

?>
