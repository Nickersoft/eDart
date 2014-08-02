<?php
include_once "call.php";

if(!isset($_SESSION)){try{session_start();}catch(Exception $e){}}
error_reporting(1);

class Search
{	
	private $con, $table;
	
	function __construct($type)
	{
		global $con, $table;
		$con = mysqli_connect(host(), username(), password(), mainDb());

		switch($type)
		{
			case ITEM:
				$table = "item";
				break;
				
			case USER:
				$table = "usr";
				break;
		}
	}
	
	public function find($filter, $sort="id", $order="ASC")
	{
		global $con, $table;

		$query = "SELECT * FROM `".mysqli_real_escape_string($con, $table)."` ";
		
		if(isset($filter)&&is_array($filter))
		{
			$query .= "WHERE ";
			
			foreach($filter as $k=>$v)
			{
				if(isset($k)&&isset($v))
				{
					$query .= " `".trim($k)."`='".trim($v)."' AND";
				}
			}
			$query = substr($query, 0, strlen($query)-3);
		}
		
		$query .= "SORT BY `".mysqli_real_escape_string($con, $sort)."` ORDER ".mysqli_real_escape_string($con, $order);
		
		$imp_query = mysqli_query($con, $query);
		$id_array  = array();
		
		while($row = mysqli_fetch_array($imp_query))
		{
			array_push($id_array, $row["id"]);
		}
		
		return $id_array;	
	}

}
?>