<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

error_reporting(1);

class Login
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
		else
		{
			$return;
			switch(strtolower(trim($action)))
			{
				case "login":
					$return = $this->login($argv["email"], $argv["password"]);
					break;
				case "logout":
					$return = $this->logout();
					break;
			}

			
			return $return;
		}
		mysqli_close($con);
	}
	
	private function login($email, $password)
	{
		global $con;

		if(isset($email)&&(isset($password)))
		{
			$loggedin = false;
			$user_query = mysqli_query($con, "SELECT * FROM usr");

			while($user = mysqli_fetch_array($user_query))
			{
				if(strlen(trim($user["password"]))>=SALT_LEN)
				{
					$current_user_salt = return_salt(trim($user["password"]));
					$given_pw_hashed   = hash_password($password, $current_user_salt);

					if((trim($user["password"])===trim($given_pw_hashed))&&(trim($email)===trim($user["email"])))
					{
						$loggedin = true;

						$_SESSION["userid"] = $user["id"];
						
						$userUpdate = new User(array("action"=>"update", "fields"=>array("last_login"=>time(), "active"=>"1", "last_location"=>json_encode(get_location()))));
						$userUpdate->run(true);

						switch(trim($user["status"]))
						{
							case 0:
								return 101;
								break;
							case 1:
								return 102;
								break;
							case 2:
								return 100;
								break;
							
						}
					}
				}
			}
			
			if(!$loggedin)
			{
				return 0;
			}
		}
		else
		{
			return 401;
		}
	}
	
	private function logout()
	{
		//Deactivate the user
		$userUpdate = new User(array("action"=>"update", "fields"=>array("active"=>"0")));
		$userUpdate->run(true);

		//Unset the global variable
		unset($_SESSION["userid"]);

		//Return success
		return 200;
	}
}
?>