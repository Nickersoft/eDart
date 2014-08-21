<?php
include_once "call.php";

if(!isset($_SESSION)){try{session_start();}catch(Exception $e){}}
error_reporting(1);

class Exchange
{
	private $con;
	private $argv;

	private $isBusy = false;

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
		if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else if(!isset($action))
		{
			return 401;
		}
		else
		{
			$return;
			switch(strtolower(trim($action)))
			{
				case "get":
					$return = $this->get($argv["id"]);
					break;
				case "create":
					$return = $this->create(array("item1"=>$argv["item1"], "item2"=>$argv["item2"]));
					break;
				case "push":
					$return = $this->push($argv["id"], $argv["timestamp"]);
					break;
				case "set":
					$return = $this->set($argv["id"], $argv["timestamp"]);
					break;
				case "send":
					$return = $this->send($argv["id"], $argv["message"]);
					break;
				case "find":
					$return = $this->find();
					break;
			}

			return $return;
		}
		mysqli_close($con);
	}

	//Gets the length of a conversation for a given ID
	private function messageLen($id)
	{
		$thisExchange = $this->get($id);
		if(is_array($thisExchange))
		{
			$messageArray = $thisExchange[0]["conversation"];
			if(is_array($messageArray))
			{
				return count($messageArray);
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}

	}

	//Sends a message to the server
	private function send($id, $message)
	{
		global $con, $isBusy;

		if(!isset($id)||!isset($message))
		{
			return 401;
		}
		else if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			$thisExchange = $this->get($id);
			if(is_array($thisExchange))
			{
				$messageArray = json_decode($thisExchange[0]["messages"], true);
				$newMessage   = array("user"=>$_SESSION["userid"], "timestamp"=>time(), "message"=>htmlentities($message));
				if(is_array($messageArray))
				{
					array_push($messageArray, $newMessage);
				}
				else
				{
					$messageArray = array($newMessage);
				}

				$query = "UPDATE `exchange` SET `messages`='".mysqli_real_escape_string($con, json_encode($messageArray))."' WHERE `id`='".mysqli_real_escape_string($con, $id)."'";
				mysqli_query($con, $query);

			}

			return 200;
		}
	}

	private function create($parameters)
	{
		global $con;

		if((!isset($parameters["item1"]))||(!isset($parameters["item2"])))
		{
			return 401;
		}
		elseif(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			$offerid = random_key(256);
			$offer_fields = array("id"=>$offerid, "item1"=>$parameters["item1"], "item2"=>$parameters["item2"]);

			addRow($con, "exchange", $offer_fields);

			$originItem = new Item(array("action"=>"get", "filter"=>array("id"=>$parameters["item1"])));
			$originInfo = $originItem->run();

			if($originInfo[0]["usr"]!=$_SESSION["userid"])
			{
				return 403;
				exit;
			}

			$itemAuth = new User(array("action"=>"get", "id"=>$originInfo[0]["usr"]));
			$userGet  = $itemAuth->run();
			$afname   = $userGet[0]["fname"];

			$acceptItem = new Item(array("action"=>"get", "filter"=>array("id"=>$parameters["item2"])));
			$acceptInfo = $acceptItem->run();
			$ai_name = $acceptInfo[0]["name"];
			$ai_usr	 = $acceptInfo[0]["usr"];

			$pronoun = "their";
			switch(trim($userGet[0]["gender"]))
			{
				case "0":
					$pronoun = "his";
					break;
				case "1":
					$pronoun = "her";
					break;
			}

			$offermsg = $afname . " has decided to trade $pronoun " . $originInfo[0]["name"] . " for your " . $ai_name;
			$link     = "exchange.php?offerid=".trim($offerid);
			sendNotify($acceptInfo[0]["usr"], $offermsg, $link);

			return 200;
		}
	}

	//Sets a timestamp as the meeting date
	private function set($id, $timestamp)
	{
		global $con;

		$exchangeInfo = $this->get($id);

		if((!isset($id))||(!isset($timestamp)))
		{
			return 401;
		}
		else if((is_array($exchangeInfo))&&(count($exchangeInfo)>0))
		{
			$exchangeInfo = $exchangeInfo[0];
			$availability_array = json_decode($exchangeInfo["availability"],true);
			$date_array   = array();
			if(is_array($availability_array)&&count($availability_array)>0)
			{
				foreach($availability_array as $user=>$dates)
				{
					foreach($dates as $d)
					{
						if(in_array($timestamp, $date_array))
						{
							$item1 = new Item(array("action"=>"get", "filter"=>array("id"=>$exchangeInfo["item1"])));
							$item1_info = $item1->run();
							$item1_info = $item1_info[0];

							$item2 = new Item(array("action"=>"get", "filter"=>array("id"=>$exchangeInfo["item2"])));
							$item2_info = $item2->run();
							$item2_info = $item2_info[0];

							if(($item1_info["usr"]==$_SESSION["userid"])||($item2_info["usr"]==$_SESSION["userid"]))
							{
								$other_usr = ($item1_info["usr"]==$_SESSION["userid"]) ? $item2_info["usr"] : $item1_info["usr"];
								$other_item = ($item1_info["usr"]==$_SESSION["userid"]) ? $item2_info : $item1_info;
								$my_item = ($item1_info["usr"]==$_SESSION["userid"]) ? $item1_info : $item2_info;

								$meUser = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
								$myInfo = $meUser->run(true);
								$myInfo = $myInfo[0];

								$oUser = new User(array("action"=>"get", "id"=>$other_usr));
								$oInfo = $oUser->run(true);
								$oInfo = $oInfo[0];

								mysqli_query($con, "UPDATE `exchange` SET `date`='".mysqli_real_escape_string($con, $timestamp)."'");
								sendNotify($other_usr, $myInfo["fname"] . " selected a date for you to exchange your ". $other_item["name"] . " for a " . $my_item["name"], "exchange.php?offerid=".$id, "Meeting date selected!");
								return 200;
							}
							else
							{
								return 406;
							}
						}
						else
						{
							array_push($date_array, $d);
						}
					}
				}
			}

		}

		return 400;

	}

	//Push/pulls a date from the server
	//If the date is already on there, it is deleted
	//If not, it is added
	private function push($id, $timestamp)
	{
		global $con; //Import the global MySQL connection

		if((!isset($id))||(!isset($timestamp))) //Throw an error if a parameter isn't set
		{
			return 401;
		}
		else if(intval($timestamp)==0)
		{
			exit;
		}
		else
		{
			$exchangeInfo = $this->get($id); //Get the information for the given exchange

			if(is_array($exchangeInfo)&&count($exchangeInfo)>0) //If this exchange exists...
			{
				echo "exchange found";
				$thisExchange = $exchangeInfo[0]; //Get the first exchange which is returned
				$availability_array = json_decode($thisExchange["availability"], true); //Decode the user availability array in the 'availability' column

				if(is_array($availability_array)) //If available dates have been set before...
				{
					if(array_key_exists($_SESSION["userid"], $availability_array)) //...and the current user has selected some dates...
					{
						if(in_array($timestamp, $availability_array[$_SESSION["userid"]])) //Check to see if the given date has already been selected
						{
							//If it has, we're going to remove it
							$date_array = array(); //Declare an empty date array
							$user_availability = $availability_array[$_SESSION["userid"]]; //Get the user availability array

							foreach($user_availability as $d) //Loop through all selected dates for this user
							{
								if($d!=$timestamp) //If the visited timestamp is not the given one
								{
									array_push($date_array, $d); //Add it to the new array
								}

								//This ultimately removes all instances of the given timestamp from the date array
							}

							$availability_array[$_SESSION["userid"]] = $date_array; //Reassign the array

						}
						else //If the date hasn't been selected yet
						{
							array_push($availability_array[$_SESSION["userid"]], $timestamp); //Add it to the array

							//Check to see if the other user has the same date selected
							$visited_ts = array(); //All timestamps that have been visited by the iterator
							foreach($availability_array as $user=>$dates)
							{
								foreach($dates as $d)
								{
									if(in_array($d, $visited_ts))
									{
										return 500;
									}
									else
									{
										array_push($visited_ts, $d);
									}
								}
							}
						}
					}
					else //If the user hasn't selected any dates yet...
					{
						$availability_array[$_SESSION["userid"]] = array($timestamp); //Create a key value for them and put the given timestamp in a child array
					}
				}
				else //If there have been no dates selected yet by anyone (i.e. the array doesn't exist)
				{
					$availability_array = array($_SESSION["userid"]=>array($timestamp)); //Create it using the values given
				}

				//Push all changes to the server
				$update_query = "UPDATE `exchange` SET `availability`='".mysqli_real_escape_string($con, json_encode($availability_array))."' WHERE `id`='".mysqli_real_escape_string($con, $id)."'";
				mysqli_query($con, $update_query);
			}

			return 200; //Return success
		}
	}

	private function get($id)
	{
		global $con;

		$query = "SELECT * FROM exchange WHERE `id`='".mysqli_real_escape_string($con, $id)."'";
		$return_array = sqlToArray($con, $query, array());

		$continue = true;

		if(is_array($return_array)&&(count($return_array)!=0))
		{
			for($i = 0; $i < count($return_array); $i++)
			{
				$item1id = $return_array[$i]["item1"];
				$item2id = $return_array[$i]["item2"];

				$item1_obj = new Item(array("action"=>"get", "filter"=>array("id"=>$item1id)));
				$item1_ret = $item1_obj->run();

				$item2_obj = new Item(array("action"=>"get", "filter"=>array("id"=>$item2id)));
				$item2_ret = $item2_obj->run();

				if(($item1_ret[0]["usr"]!=trim($_SESSION["userid"])) &&
				   ($item2_ret[0]["usr"]!=trim($_SESSION["userid"])))
				   {
				   		$continue = false;
				   }
			}
		}

		if($continue)
		{
			return $return_array;
		}

		return array();
	}

	//Finds all exchanges current user is involved in
	private function find()
	{
		global $con;

		$query = "SELECT * FROM exchange";
		$return_array = sqlToArray($con, $query, array());

		$continue = true;

		$exchange_array = array();

		if(is_array($return_array)&&(count($return_array)!=0))
		{
			for($i = 0; $i < count($return_array); $i++)
			{
				$item1id = $return_array[$i]["item1"];
				$item2id = $return_array[$i]["item2"];

				$item1_obj = new Item(array("action"=>"get", "filter"=>array("id"=>$item1id)));
				$item1_ret = $item1_obj->run();

				$item2_obj = new Item(array("action"=>"get", "filter"=>array("id"=>$item2id)));
				$item2_ret = $item2_obj->run();

				if(($item1_ret[0]["usr"]==trim($_SESSION["userid"])) ||
				   ($item2_ret[0]["usr"]==trim($_SESSION["userid"])))
				   {
					    $who_ranked = (is_array(json_decode($return_array[$i]["who_ranked"], true))) ? json_decode($return_array[$i]["who_ranked"], true) : array();
					    if(!in_array($_SESSION["userid"], $who_ranked))
						{
							array_push($exchange_array, $return_array[$i]);
						}
				   }
			}
		}

		return $exchange_array;
	}

}
?>
