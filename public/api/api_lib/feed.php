<?php
include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

if(!isset($_SESSION)){try{session_start();}catch(Exception $e){}}
error_reporting(1);

class Feed
{	
	private $con; 
	private $argv;
	
	function __construct($parameters = array())
	{
		global $con, $argv;
		$con = mysqli_connect(host(), username(), password(), mainDb());
		$argv = $parameters;
	} 

	//Add a post to the feed
	public function add($usr, $string, $date, $link)
	{
		global $con;

		//Load all the variables into an array
		$key_array = array("usr"=>$usr, "string"=>$string, "date"=>$date, "link"=>$link);

		//Add the row to the table
		addRow($con, "feed", $key_array);

		//Return success
		return 200;
	}

	public function get($id)
	{
		global $con;

		$post_html = "";
		$query = mysqli_query($con, "SELECT * FROM `feed` ORDER BY `date` DESC");

		while($post = mysqli_fetch_array($query))
		{
			$getUser 	= new User(array("action"=>"get", "id"=>$post["usr"]));
			$userInfo	= $getUser->run(true);

			$date_relative = getRelativeDT(time(), $post["date"]);

			if($userInfo!=404)
			{
				$post_html .= <<<POST
					<div class="post hidden">
						<div class="img"> 
							<a href="/profile.php?id={$post["usr"]}"> 
								<img alt="Profile Picture" src="/profile.php?id={$post["usr"]}&load=image&size=small" style="cursor:pointer;">
							</a>
						</div>

						<div class="cocnt">

							<div style="cursor:pointer;" class="hdr">
								<a href="/profile.php?id={$post["usr"]}">
									{$userInfo[0]["fname"]} {$userInfo[0]["lname"]}
								</a>
							</div>

							<div class="txt">
								<a href="{$post["link"]}">
									{$post["string"]} 
								</a>
							</div>

						</div>

						<div class="date">
							$date_relative ago
						</div>

					</div>

POST;
			}

		}

		return str_replace("\t", "", str_replace("\n", "", $post_html));
	}

}
?>