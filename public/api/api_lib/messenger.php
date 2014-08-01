<?php
include_once "call.php";

if(!isset($_SESSION)){try{session_start();}catch(Exception $e){}}
error_reporting(1);

class Messenger
{	
	private $con; 
	private $argv;
	
	function __construct($parameters)
	{
		global $con, $argv;
		$con = mysqli_connect(host(), username(), password(), mainDb());
		$argv = $parameters;
	} 
	
	public function run()
	{
		global $con, $argv;
		
		$action = $argv["action"];
		if(!isset($action))
		{
			return 401;
		}
		else if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			$return;
			switch(strtolower(trim($action)))
			{
				case "get":
					$return = $this->get($argv["filter"],$argv["sort"],$argv["order"]);
					break;
				case "send":
					$return = $this->send($argv);
					break;
				case "listen":
					$return = $this->listen($argv["thread"]);
					break;
				
			}

			return $return;
		}
		mysqli_close($con);
	}
	
	private function listen($id)
	{
		if(!isset($id))
		{
			return 401;
		}
		else
		{
			$listener = new Listener(array("type"=>CONVERSATION));
			$response = $listener->listen();
		
			if(is_array($response))
			{
				$output_array = array();
			
				foreach($response as $v)
				{
					if((is_array($v))&&(array_key_exists("thread",$v)))
					{
						if($v["thread"]==$id)
						{
							array_push($output_array, $v);
						}
					}
					
				}
				return $output_array;
			}
			else
			{
				return $response;
			}
		}
	}
	
	private function send($send_parameters)
	{
		global $con;
		
		$to 	= $send_parameters["to"];
		$from 	= $_SESSION["userid"];
		$msg 	= $send_parameters["msg"];
		$subj 	= $send_parameters["subject"];
		$convoid = $send_parameters["thread"];
		
		if( isset($to) && isset($msg) && isset($subj) )
		{
			if(!isset($convoid))
			{
				$convoid = random_key(256);
			}
			$send_array = array("thread"=>$convoid, "sender"=>$from, "receiver"=>$to, "subject"=>$subj, "msg"=>$msg, "date"=>date("Y-m-d H:i:s"));
			addRow($con, "msg", $send_array);
			return 200;
		}
		else
		{
			return 401;
		}
	}
	
	private function get($filter, $sortBy=NULL, $sortOrder=NULL)
	{
		global $con;
		
		$escaped_id = mysqli_real_escape_string($con, $_SESSION["userid"]);
		$query = "SELECT * FROM msg WHERE (`from`='".$escaped_id."' OR `to`='".$escaped_id."')";
	
		if(isset($filter)&&is_array($filter))
		{
			foreach($filter as $k=>$v)
			{
				if(isset($k)&&isset($v))
				{
					$query .= "AND `".trim($k)."`='".trim($v)."'";
				}
			}

		}
		
		switch(strtolower(trim($sortOrder))=="d")
		{
			case "a":
				$sortOrder = "ASC";
				break;
			default:
				$sortOrder = "DESC";
				break;
		}
		
		if($sortBy)
		{
			$query .= " ORDER BY `".mysqli_real_escape_string($con, $sortBy)."` ".mysqli_real_escape_string($con, strtoupper($sortOrder));		
		}
		
		return sqlToArray($con, $query, array());	
	}
	
}
?>