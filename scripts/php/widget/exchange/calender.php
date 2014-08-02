<?php

/*************************************************
	
	   		 Name: Calender Widget
		  Purpose: Date calender for exchange.php
	Last Modified: 3/20/2014

	Copyright 2014 eDart

*************************************************/
	

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php"; //Include the core library dependencies and configuration information
error_reporting(1); //Enable error reporting

class Calender
{

	private $id;

	/*
		Constructor : string -> void
		Accepts exchange ID and assigns it to global ID variables
	*/
	function __construct($exchange_id)
	{
		global $id;

		$id = $exchange_id;
		return;
	}

	/*
		Output: void -> void
		Outputs the calender widget
	*/
	function output()
	{
		global $id;

		//Initialize output html and write header to variable
		$html = <<<WAT
		<div class="hdr btxt">Meeting Date</div>
		<div class="head">When are you free?</div>
WAT;

		//Initialize empty set of day columns for the table
		$days = "";

		for($x = 1; $x <= 7; $x++) //Loop through the next seven days
		{
			$day_of_week = date("l", strtotime("+" . $x . " day")); //Calculate the day of week
			$date = date("n/j", strtotime("+" . $x . " day")); 		//Calculate the date
			$days .= "<td>$day_of_week<span>$date</span></td>";		//Write html and append to the master date header
		}

		//Initialize empty set of time cells
		$times = "";

		//Get the information for this exchange
		$thisExchange = new Exchange(array("action"=>"get", "id"=>$id));
		$exchangeInfo = $thisExchange->run();

		//Decode the available dates between users
		$availableArray = json_decode($exchangeInfo[0]["availability"], true);

		//If there is no date array currently on the server...
		if(!is_array($availableArray))
		{
			$availableArray = array(); //Initialize an empty arreay
		}

		//Loop through the next 13 hours, starting at 9 AM
		for($i = 9; $i<22; $i++)
		{

			$class  = (($i%2)==0) ? "odd" : "even"; //Allows for alternating row colors

			$time_str = date("g A", strtotime($i . ":00")); //Calculate a 12-hour time string based on index

			//Begin html for each row, beginning with a time header
			$times .= <<<EOF
						<tr class="$class">
							<td class="time_hcell">$time_str</td>
EOF;
		
			//Loop through the next seven days (again) per row
			for($j = 1; $j <= 7; $j++)
			{
				//Generate a string in the form Y-m-d H:i:s using the given date/time information
				$calculated_timestring = date("Y-m-d", strtotime("+" . $j . "day")) . " " . date("H:i:s", strtotime($i . ":00"));

				//Generate a timestamp from that string
				$calculated_timestamp  = strtotime($calculated_timestring);

				$cell_class = ""; //By default, the cell will have no additional classes

				$visited_dates = array(); //An array of dates that the iterator has gone through

				foreach($availableArray as $user=>$dates) //Iterate through all available dates
				{
					foreach($dates as $d) //Iterates through the set of dates per user
					{
						if($d==$exchangeInfo[0]["date"]) //If the date is the meeting date...
						{
							$cell_class = "selected"; //...select it
						}
						else if($d==$calculated_timestamp) //If not...
						{
							//Make it bright if it belongs to the current user,
							//and dull if it belongs to the other user
							$cell_class = ($user==$_SESSION["userid"]) ? "bright" : "faded";
						}
					}
				}

				$dt_cell = "<td data-timestamp=\"$calculated_timestamp\" class=\"timecell $cell_class\"></td>"; //Write an html string for the cell
				$times .= $dt_cell; //Append it to the times row

			}

			$times .= "</tr>"; //End the times row
		}

		//Finish writing the HTML
		$html .= <<<EOD
				<table class="btxt" style="font-size:12px;border-spacing:0px;">
					<tr style="text-align:center;">
						<td></td>
						$days
					</tr>
					$times
				</table>
				<script>
				</script> 
EOD;
		echo $html; //Output the HTML
		return; //Return null
	}
}

?>