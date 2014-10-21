<?php

/*

The eDart User API v1.0

*/

include_once "call.php"; //Include the 'header' file of the API

error_reporting(E_ALL); //Turn on error reporting
ini_set("display_errors","1");

//The main User class
class User
{
	private $con; //Will be initialized as the main mysqli connection
	private $argv; //Will be initialized to the parameters to class

	//The main constructor
	function __construct($parameters = array())
	{
		global $con, $argv; //Import the two global variables
		$con = mysqli_connect(host(), username(), password(), mainDb()); //Initialize $con as a database connection
		$argv = $parameters; //Set the given parameters to the argv variable
	}

	//Public run function. Executes main functionality based on parameters.
	public function run($systemByPass = false)
	{
		global $con, $argv; //Import the two global variables

		$action = $argv["action"]; //Assign the primary action parameter to a variable

		if((!isset($action))||(trim($action)=="")) //If an action wasn't provided...
		{
			return 401; //...throw an error
		}
		else //If one was provided...
		{
			$return; //Declare a variable to be returned at the end of the function
			$createBlock = array("fname","lname","password","email");
			$updateBlock = array("id","rank","joindate","password","profile_pic","status","lastlocation");
			if($systemByPass)
			{
				$createBlock = array();
				$updateBlock = array();
			}
			//Depending on what the action was, execute different code
			switch(strtolower(trim($action)))
			{
				case "get": //If get...
					$return = $this->get($argv["id"], $systemByPass); //Get user info for a given ID
					break;
				case "create": //If create...
					$return = $this->create($argv["fields"], $createBlock); //...create a user based off given fields
					break;
				case "update": //If update...
					$return = $this->update($argv["fields"], $updateBlock, $systemByPass); //...update the current user based off given fields
					break;
			}

			return $return;
		}
		mysqli_close($con); //Close the MySQL connection
	}

	//Updates the logged in user's user data
	private function update($fields, $blockedFields, $sb)
	{
		global $con; //Import the global MySQL connection

		if(!isset($_SESSION["userid"])) //If the user isn't logged in...
		{
			return 403; //Throw an error
		}

		$info = $this->get($_SESSION["userid"], true);
		if($info[0]["status"]!="2")
		{
			return 403;
		}
		else //If they are logged in...
		{
			if(!isset($fields)||(!is_array($fields))) //If no field parameters were given...
			{
				return 401; //...throw another error
			}
			else //If there is at least one field parameter...
			{
				foreach($blockedFields as $v)
				{
					if(array_key_exists($v, $fields)&&!$sb)
					{
						return 402;
					}
				}

					$esc_sesh = mysqli_real_escape_string($con, trim($_SESSION["userid"])); //MySQL encode the session user ID

					if(isset($fields["password"])&&$sb)
					{
						$fields["password"] = hash_password($fields["password"]);
					}

					$query = "UPDATE `usr` SET "; //Start a MySQL update query

					foreach($fields as $k=>$v) //Loop through the key value pairs of the given parameters
					{
						if(isset($k)&&isset($v)) //If both a key and value are set...
						{
							//Encode them and add them to the update query
							$query .= "`" . mysqli_real_escape_string($con, trim($k)) . "`='".mysqli_real_escape_string($con, trim($v))."',";
						}
					}

					//Take a substring of the query, removing the left-over comma from the above iteration
					$query = substr($query, 0, strlen($query)-1);

					$query .= " WHERE `id`='".$esc_sesh."'"; //Finish the query string, updating where the user ID = the current user's
					mysqli_query($con, $query); //Execute the query

					return 200; //Return a string, showing completion
			}
		}
	}

	//TODO: Write validation
	//Creates a new user
	private function create($parameters, $requiredFields)
	{
		global $con; //Import the global MySQL connection
		$pass = true;

		foreach($requiredFields as $v)
		{
			if(!array_key_exists($v, $parameters))
			{
				$pass = false;
			}
		}

		$split_by_amp = explode($parameters["email"], '@');
		if((array_key_exists("id", $parameters)||(array_key_exists("join_date", $parameters))))
		{
			return 402;
		}
		else if(!$pass)
		{
			return 401;
		}
		else if($split_by_amp[count($split_by_amp)-1] != "@wpi.edu")
		{
			return 104;
		}
		else
		{
			$user_iteration_q = mysqli_query($con, "SELECT * FROM `usr`");
			$count = 0;
			while($row = mysqli_fetch_array($user_iteration_q))
			{
				if(trim($row["email"])==trim($parameters["email"]))
				{
					return 103;
				}
				$count++;
			}
			$count++;
			$parameters["join_date"] = time();
			$parameters["password"] = hash_password($parameters["password"]);
			//Add an id parameter to the user parameters, where the user ID is the number user it is
			$user_fields = $parameters;

			addRow($con, "usr", $user_fields); //Add the row to the `usr` table

			return 200; //Return a string, showing completion

		}
	}

	//Deletes the current user account
	public function delete()
	{
		global $con;
		if(!isset($_SESSION["userid"]))
		{
			return 403;
		}
		else
		{
			//Get all items owned by the user
			$user_items = new Item(array("action"=>"get", "filter"=>array("usr"=>$_SESSION["userid"])));
			$item_array = $user_items->run(true);

			//Determines whether we can delete or not
			$pass = true;

			foreach($item_array as $item)
			{
				if(intval($item["status"])!=1)
				{
					$pass = false;
				}
			}

			if($pass)
			{
				mysqli_query($con, "DELETE FROM `usr` WHERE `id`=".mysqli_real_escape_string($con, $_SESSION["userid"]));
				return 200;
			}
			else
			{
				return 400;
			}
		}
	}

	//Gets a current users information, given a user ID
	private function get($id, $sb)
	{
		global $con; //Import the global MySQL connection

		if( (isset($id)) && (trim($id)!="") ) //If a user ID was provided and it isn't empty
		{

			//MySQL select query that retrieves the row the user info is on
			$query = "SELECT * FROM usr WHERE id='" . mysqli_real_escape_string($con, $id) . "'";
			$user_p_query = mysqli_query($con, $query); //Store the query in a variable
			$all_permissions = array();
			if(!$sb){
				$base_permissions = array("email","password", "domail", "status", "privacy", "rank"); //Default fields to be hidden upon user output
				$all_permissions = $base_permissions; //Will contain all custom privacy settings as well

				if( ( !isset($_SESSION) ) || ( trim($_SESSION["userid"]) != trim($id) ) ) //If the user is not logged in...
				{
					//Loop through the results
					while($r = mysqli_fetch_array($user_p_query))
					{
						//Add the user's privacy settings to the custom permissions array
						if(is_array(json_decode($r["privacy"])))
						{
							$all_permissions = array_merge($all_permissions, json_decode($r["privacy"], true));
						}
					}
				}
			}

			//If they are logged in, we ignore these privacy settings

			$return_array =  sqlToArray($con, $query, $all_permissions);

			if(isset($_SESSION["userid"])&&($id==$_SESSION["userid"]))
			{
				$notifyArray = sqlToArray($con, "SELECT * FROM notify WHERE `usr`='".$_SESSION["userid"]."' ORDER BY date DESC", array());
				$return_array[0] = array_merge($return_array[0], array("notify"=>$notifyArray));
			}

			if($return_array) //If the user array isn't null...
			{
				return $return_array; //Get the JSON array associated with the query
			}
			else //If it's null...
			{
				return 404; //...then the user doesn't exist
			}
		}
		else //If a user ID wasn't provided...
		{
			return 401; //...throw an error
		}
	}

}
?>
