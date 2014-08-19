<?php

	function purify_array($array)
	{
		$return_array  = array();

		if(is_array($array))
		{
			foreach($array as $key=>$value)
			{
				if(is_array($value))
				{
					$value = purify_array($value);
				}

				if(array_key_exists($key, $array))
				{
					$return_array = array_merge($return_array, array($key=>$value));
				}
				else
				{
					array_push($return_array, $value);
				}
			}
		}
		return $return_array;
	}

	function sqlToArray($connection, $query, $blockedFields=array())
	{
		if(trim($query)=="")
		{
			return array();
		}
		
		mysqli_set_charset($connection, "utf8");
		
		$sql_query = mysqli_query ($connection, $query);
		$masterArray = array();

		$columnArray = array ();
		while ( $fld = mysqli_fetch_field ( $sql_query ) ) 	// While there are fields in the table (there are)
		{
			$fieldname = $fld->name; // The field name of the current column
			if(!in_array($fieldname, $blockedFields)) {
				array_push($columnArray, $fieldname);
			}
		}

		while ( $row = mysqli_fetch_array ( $sql_query ) ) 	// While there are rows in info (there's only 1)
		{
			$newArray = array();
			for($i = 0 ; $i < count($columnArray); $i++)
			{
				$newArray = array_merge($newArray, array($columnArray[$i]=>""));
			}

			foreach ( $newArray as $k=>$v ) 		// While there are fields in the table (there are)
			{
				$key = trim(strtolower($k));
				//All json arrays or blob objects
				$nono_list = array("profile_pic", "image", "offers", "who_ranked", "messages", "availability", "privacy", "rank", "last_location");
				if(in_array($key, $nono_list))
				{
					if(is_array(json_decode($row[$k], true)))
					{
						$newArray[$k] = json_encode(purify_array(json_decode($row[$k], true)));
					}
					else
					{
						$newArray[$k] = $row[$k];
					}
				}
				else
				{
					$newArray[$k] = htmlentities($row[$k]);
				}

				if(isset($newArray["fname"])&&isset($newArray["lname"]))
				{
					$newArray["fname"] = ucwords($newArray["fname"]);
					$newArray["lname"] = ucwords($newArray["lname"]);
				}
			}

			array_push($masterArray, $newArray);
		}

		return $masterArray;
	}

	function addRow($connection, $table, $fields)
	{
		$query = "INSERT INTO `".mysqli_real_escape_string($connection, $table)."`(";
		foreach($fields as $k=>$v)
		{
			$query .= "`" . mysqli_real_escape_string($connection, $k) . "`,";
		}

		$query =  substr($query, 0, strlen($query)-1);

		$query .= ") VALUES(";
		foreach($fields as $k=>$v)
		{
			$query .= "'" . mysqli_real_escape_string($connection, $v) . "',";
		}

		$query =  substr($query, 0, strlen($query)-1);

		$query .= ")";
		
		mysqli_query($connection, $query);
	}

	//Get the number of bytes given any size
	function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);

	    switch($last) {
	        // The 'G' modifier is available since PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }

	    return $val;
	}
?>
