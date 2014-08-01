<?php
include_once "call.php";

error_reporting(1);

class Listener
{	

	private $con; 
	private $argv;
	
	function __construct($parameters)
	{
		global $con, $argv;
		$con = mysqli_connect(host(), username(), password(), mainDb());
		$argv = $parameters;
	} 
	
	public function listen()
	{
		global $con, $argv;

		if(!isset($argv["type"]))
		{
			return 401;
		}
		else if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			$table = "";
			$user_match = array();
			
			switch($argv["type"])
			{
				case USER:
					$table = "usr";
					$user_match = array("id");
					break;
					
				case ITEM:
					$table = "item";
					$user_match = array("usr");
					break;
					
				case CONVERSATION:
					$table = "msg";
					$user_match = array("from", "to");
					break;
					
				default:
					exit;
			}
			
			$user_cond = "WHERE";
			foreach($user_match as $v)
			{
				$user_cond .= " `".mysqli_real_escape_string($con, $v)."` = '".mysqli_real_escape_string($con, $_SESSION["userid"])."' OR";
			}
			
			$user_cond = substr($user_cond, 0, strlen($user_cond)-2);
			$query_str =  "SELECT * FROM `".mysqli_real_escape_string($con,$table)."` ".$user_cond;

			$start_length 	= count(sqlToArray($con,$query_str,array()));
			$current_length = count(sqlToArray($con,$query_str,array()));
			$timeout = 0;
			
			while( ($current_length <= $start_length) && ($timeout <= 29) )
			{
				sleep(1);
				clearstatcache();
				$timeout++;
				$current_length = count(sqlToArray($con,$query_str,array()));
				if($timeout>29)
				{
					return 201;
					exit;
				}
			}
			
			return sqlToArray($con,$query_str,array());
		}
	}

	
}
?>