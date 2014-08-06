<?php
include_once "call.php";

if(!isset($_SESSION)){try{session_start();}catch(Exception $e){}}
error_reporting(E_ALL);
ini_set('display_errors', '1');
class Item
{
	private $con;
	private $argv;

	function __construct($fields)
	{
		global $con, $argv;
		$con = mysqli_connect(host(), username(), password(), mainDb());
		$argv = $fields;
	}

	public function run($bypass=false)
	{
		global $con, $argv;

		$action = $argv["action"];
		if(!isset($action))
		{
			return 401;
		}
		else
		{
			$return;
			$updateBlock = array();
			$createBlock = array();
			$getBlock    = array();

			if(!$bypass)
			{
				$updateBlock = array("offers", "status", "image");
				$createBlock = array("id","offers", "emv", "image");
				$getBlock    = array("image");
			}

			switch(strtolower(trim($action)))
			{
				case "get":
					$filter = (isset($argv["filter"])) ? $argv["filter"] : null;
					$sort   = (isset($argv["sort"]))   ? $argv["sort"]   : null;
					$order  = (isset($argv["order"]))  ? $argv["order"]  : null;

					$return = $this->get($filter, $sort, $order, $getBlock);
					break;
				case "update":
					$return = $this->update($argv["id"], $argv["fields"], $updateBlock);
					break;
				case "create":
					$return = $this->create($argv["fields"], array("name","category","expiration","duedate","stadd1","citytown","state"), $createBlock);
					break;
				case "delete":
					$return = $this->delete($argv["id"]);
					break;
				case "offer":
					$return = $this->offer($argv["id"], $argv["offer"]);
					break;
			}

			return $return;
		}
		mysqli_close($con);
	}

	private function offer($id, $offer)
	{
		global $con;

		if((!isset($id))||(!isset($offer)))
		{
			return 401;
		}
		else
		{
			$item_info = $this->get(array("id"=>$id));
			$offer_array = json_decode($item_info[0]["offers"], true);

			if(!is_array($offer_array)){
				$offer_array = array();
			}

			$msg = "";
			$remindex = -1;

			for($i = 0; $i < count($offer_array); $i++)
			{
				if($offer_array[$i]["id"]==$offer)
				{
					$remindex = $i;
				}
			}

			if($remindex==-1)
			{
				$new_offer = array("id"=>$offer, "timestamp"=>time());
				array_push($offer_array, $new_offer);

				$this->update($offer, array("status"=>"2"), array());

				$update_query = "UPDATE `item` SET `offers`='" . mysqli_real_escape_string($con, json_encode($offer_array)) . "' WHERE `id`='" . mysqli_real_escape_string($con, $id) . "'";
				mysqli_query($con, $update_query);

				$offeredItem = new Item(array("action"=>"get", "filter[id]"=>$offer));
				$offeredInfo = $offeredItem->run();

				$name    = $offeredInfo[0]["name"];

				if(!$_SESSION["userid"]||$_SESSION["userid"]!=$offeredInfo[0]["usr"])
				{
					return 401;
					exit;
				}

				$offerAuth   = new User(array("action"=>"get", "id"=>$offeredInfo[0]["usr"]));
				$authInfo    = $offerAuth->run();

				$offermsg = $authInfo[0]["fname"] . " made an offer on your item: " . $item_info[0]["name"];

				$link = "view.php?itemid=".$item_info[0]["id"]."&userid=".$item_info[0]["usr"];

				sendNotify($item_info[0]["usr"], $offermsg, $link);

				$vowels = array('a','e','i','o','u') ;
				$a_str 	= in_array($item_info[0]["name"][0], $vowels) ? "an" : "a";

				$feed = new Feed();
				$feed->add($_SESSION["userid"], "offered $pronoun {$offeredInfo[0]["name"]} for $a_str {$item_info[0]["name"]}", time(), $link);
			}
			else
			{
				$query = "UPDATE `item` SET `status`='1' WHERE `id`='".mysqli_real_escape_string($con, $offer)."'";
				mysqli_query($con, $query);

				unset($offer_array[$remindex]);
			}


			return 200;
		}
	}

	private function delete($id)
	{
		global $con;

		if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else if((!isset($id))||($id==""))
		{
			return 401;
		}
		else
		{
			if($this->canModify($id))
			{
				$query = "DELETE FROM `item` WHERE `usr`='".mysqli_real_escape_string($con, $_SESSION["userid"])."' AND `id`='".mysqli_real_escape_string($con, $id)."'";
				mysqli_query($con, $query);
				return 200;
			}
			else
			{
				return 406;
			}
		}
	}

	private function canModify($id)
	{
		global $con;

		$curItem  = new Item(array("action"=>"get", "filter"=>array("id"=>$id)));
		$itemInfo = $curItem->run(true);

		$curUser = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
		$userInfo = $curUser->run(true);

		return (($itemInfo[0]["status"]=="1")&&($userInfo[0]["status"]=="2"));
	}

	private function calculateEMV($name)
	{
		if((!isset($name)) || (trim($name)==""))
		{
			return 402;
			exit;
		}

		$emv		= "0";
 	//svcs.ebay.com/
		$endpoint 	= "http://66.135.211.97/services/search/FindingService/v1";
		$version 	= "1.0.0";
		$appid		= "Nickerso-c126-4222-a7b6-f39d93c08e9b";
		$globalid	= "EBAY-US";
		$query		= $name;
		$safequery	= urlencode($query);

		$apicall 	 = "$endpoint?";
		$apicall 	.= "OPERATION-NAME=findItemsByKeywords";
		$apicall 	.= "&SERVICE-VERSION=$version";
		$apicall 	.= "&SECURITY-APPNAME=$appid";
		$apicall 	.= "&GLOBAL-ID=$globalid";
		$apicall 	.= "&keywords=$safequery";
		$apicall	.= "&paginationInput.entriesPerPage=5";
		$apicall	.= "&itemFilter%280%29.name=Condition&itemFilter%280%29.value=New&itemFilter%281%29.name=ListingType&itemFilter%281%29.value=FixedPrice&itemFilter%282%29.name=buyItNowAvailable&itemFilter%282%29.value=true";

        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL, $apicall);
        curl_setopt ($crl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);

		$resp = simplexml_load_string($ret);

		if($resp->ack == "Success")
		{
			$item 	= $resp->searchResult->item[0];
			$prcex  = explode(".",$item->sellingStatus->currentPrice);
			$price 	= $prcex[0];
			$emv	= $price;
		}

		return $emv;
	}

	private function update($id,$fields,$forbidden)
	{
		global $con, $available_condition, $available_category;

		if((!isset($id))||(!isset($fields)))
		{
			return 401;
		}
		else
		{
			if(!isset($_SESSION["userid"]))
			{
				return 403;
			}
			else
			{
				if($this->canModify($id))
				{
					$esc_sesh = mysqli_real_escape_string($con, trim($_SESSION["userid"]));
					$esc_id   = mysqli_real_escape_string($con, trim($id));

					$query = "UPDATE `item` SET ";
					foreach($fields as $k=>$v)
					{
						if((isset($k)&&isset($v))&&(!in_array($v,$forbidden)))
						{
							$query .= "`" . mysqli_real_escape_string($con, trim($k)) . "`='".mysqli_real_escape_string($con, trim($v))."',";
						}
					}
					$query = substr($query, 0, strlen($query)-1);
					$query .= " WHERE `id`='".$esc_id."' AND `usr`='".$esc_sesh."'";
					mysqli_query($con, $query);
					return 200;
				}
				else
				{
					return 302;
				}
			}
		}
	}

	private function create($fields, $requiredFields, $forbidden)
	{
		global $con,  $available_condition, $available_category;

		$fields["citytown"]		= "Worcester";
		$fields["state"]		= "MA";
		$fields["stadd2"]		= "100 Institute Road";

		$pass = true;
		$hasforbidden = false;

		foreach($requiredFields as $v)
		{
			if(!array_key_exists($v, $fields))
			{
				return 401;
			}
			else
			{
				if(trim($fields[$v])=="")
				{
					return 401;
				}
			}
		}

		foreach($forbidden as $v)
		{
			if(array_key_exists($v, $fields))
			{
				return 402;
			}
		}

		$userInfo = array();
		if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			$curUser = new User(array("action"=>"get", "id"=>$_SESSION["userid"]));
			$userInfo = $curUser->run(true);
		}

		if($userInfo[0]["status"]!="2")
		{
			return 403;
		}
		else
		{
			$itemid = random_key(256);
			$fields["adddate"] 		= time();

			$emv = $this->calculateEMV($fields["name"]);
			$fields["emv"] = $emv;

			$base_array = array("usr"=>trim($_SESSION["userid"]), "id"=>$itemid);
			if(isset($fields)&&(is_array($fields)))
			{
				$item_fields = array_merge($base_array, $fields);
			}
			else
			{
				$item_fields = $base_array;
			}

			addRow($con, "item", $item_fields);

			$pronoun = "his/her";
			switch(intval($userInfo[0]["gender"]))
			{
				case 1:
					$pronoun = "his";
					break;

				case 2:
					$pronoun = "her";
			}

			$feed = new Feed();
			$feed->add($_SESSION["userid"], "posted $pronoun item: {$item_fields["name"]}", time(), "/view.php?itemid=$itemid&userid={$_SESSION["userid"]}");

			return $itemid;
		}
	}

	private function get($filter, $sort, $order, $forbidden)
	{
		global $con;

		$query = "SELECT * FROM item ";
		if(!$sort)
		{
			$sort = "adddate";
		}

		if(!$order)
		{
			$order = "ASC";
		}

		if($filter&&is_array($filter))
		{
			$query .= "WHERE ";

			foreach($filter as $k=>$v)
			{
				if((isset($k)&&isset($v))&&trim(strtolower($k))!="status")
				{
					$query .= " `".mysqli_real_escape_string($con, trim($k)) ."`='".mysqli_real_escape_string($con, trim($v))."' AND";
				}
			}
			$query = substr($query, 0, strlen($query)-3);
		}

		$query .= "ORDER BY " . mysqli_real_escape_string($con, $sort) . " " . mysqli_real_escape_string($con, $order);

		$ret_array = sqlToArray($con, $query, $forbidden);
		$fin_array = array();

		foreach($ret_array as $v)
		{
			$v["status"] = "1";
			$esc_id = mysqli_real_escape_string($con, $v["id"]);

			$oq = mysqli_query($con, "SELECT * FROM `item`");
			while($r = mysqli_fetch_array($oq))
			{
				try
				{
					$decode_offers = json_decode($r["offers"], true);
					if(is_array($decode_offers))
					{
						foreach($decode_offers as $a)
						{
							if($a["id"]==$v["id"])
							{
								$v["status"]="2";
							}
						}
					}
				}
				catch(Exception $e)
				{

				}
			}

			$q = mysqli_query($con, "SELECT * FROM exchange WHERE (`item1`='".$esc_id."' OR `item2`='".$esc_id."')");
			while($r = mysqli_fetch_array($q))
			{
				if(is_array(json_decode($r["who_ranked"], true)))
				{
					if(in_array($v["usr"], json_decode($r["who_ranked"], true)))
					{
						$v["status"] = "1";
					}
					else
					{
						$v["status"] = "0";
					}
				}
				else
				{
					$v["status"] = "0";
				}
			}
			array_push($fin_array, $v);
		}

		if(isset($filter["status"]))
		{
			$ret_arr = array();
			foreach($fin_array as $i)
			{
				if($i["status"]==$filter["status"])
				{
					array_push($ret_arr, $i);
				}
			}
			return $ret_arr;
		}

		return $fin_array;
	}

}
?>
