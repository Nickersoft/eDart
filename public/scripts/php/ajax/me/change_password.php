<?php
//Includ everything
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

//If the password was not specified...
if($_POST["current"]==""||$_POST["password"]==""||$_POST["repeat_password"]=="")
{
	//...throw an error
	echo 105;
	exit;
}
//If the passwords do not match...
else if($_POST["password"]!=$_POST["repeat_password"])
{
	//...throw an error
	echo 105;
	exit;
}
//If everything looks good...
else
{
	$user_get_call = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
	$user_info     = $user_get_call->run(true);
	$user_info 	   = $user_info[0];

	$current_salt    = return_salt($user_info["password"]);
	$hashed_password = hash_password($_POST["current"], $current_salt);

	if($hashed_password!=$user_info["password"])
	{
		echo 105;
		exit;
	}
	else
	{
		//Update the user's password
		$thisUser = new User(array("action"=>"update", "fields"=>array("password"=>$_POST["password"])));
		$thisUser->run(true);

		//Redirect and exit
		echo 200;
		exit;
	}
}

?>
