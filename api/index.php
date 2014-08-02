<?php
include_once "api_lib/call.php";

$lib = strtolower(trim($_GET["lib"]));

$library;
$return;
switch($lib)
{
	case "item":
		$library = new Item($_GET);
		$return  = $library->run();
		break;
	case "user":
		$library = new User($_GET);
		$return  = $library->run(false);
		break;
	case "login":
		$library = new Login($_GET);
		$return  = $library->run();
		break;
	case "listener":
		$library = new Listener($_GET);
		$return  = $library->listen();
		break;
	case "messenger":
	    $library = new Messenger($_GET);
		$return  = $library->run();
		break;
	case "exchange":
		$library = new Exchange($_GET);
		$return  = $library->run();
		break;
	case "feed":
		$library = new Feed($_GET);
		$return  = $library->get();
	break;
	default:
		$return = 405;
		break;
}
//If the return value isn't still null
if($return){
	if(is_array($return)) //And if the return value is an array
	{
		echo json_encode($return); //Return the JSON data
	}
	else //If not...
	{
		echo $return; //Return the [probable] string
	}
}
else{ //If it's still null...
	echo 400; //The code failed.
}

?>