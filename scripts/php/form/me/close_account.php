<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

if(isset($_SESSION["userid"])&&isset($_POST["confirm"])&&$_POST["confirm"]=="del")
{
	$item_call   = new Item(array("action"=>"get", "filter"=>array("usr"=>$_SESSION["userid"])));
	$user_items  = $item_call->run(true);
	$continue = true;

	foreach($user_items as $item)
	{
		if($item["status"]!=1)
		{
			$continue = false;
		}
	}

	if($continue)
	{
		$this_user = new User();
		$result = $this_user->delete();
		if($result==200)
		{
			$logout = new Login(array("action"=>"logout"));
			$logout->run();
			header("Location:/");
			exit;
		}
	}
	else
	{
		header("Location:/");
		exit;
	}
}
?>
