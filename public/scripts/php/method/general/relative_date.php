<?php
/* 
 * Page Name: Relative Date
 * Purpose: Get the relative date given a timestamp
 * Last Updated: 6/6/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

//If a POST request was made... 
if(isset($_POST["d1"])&&($_POST["d2"]))
{
	//...call the function
	echo getRelativeDT($_POST["d1"],$_POST["d2"]);
}

//Return the relative date
function getRelativeDT($d1, $d2)
{
	$d1s = $d1; 				//First timestamp
	$d2s = $d2;					//Second timestamp
	$diff = abs($d1s-$d2s);		//The difference between the two
	
	$sec = 1;					//How many seconds are in a second
	$min = $sec  * 60;			//How many seconds are in a minute
	$hour  = $min * 60;			//How many seconds are in an hour
	
	$day = $hour  * 24;			//How many seconds are in a day
	$month = $day * 30.4375;	//How many seconds in a month
	$year = $month * 12;		//How many seconds in a year
	
	$mo = floor($diff / $month);	//The difference in months
	$da = floor($diff / $day);		//The difference in days
	$yr = floor($diff / $year);		//The difference in year
	$hr = floor($diff / $hour);		//The difference in hours
	$mi = floor($diff / $min);		//The difference in minutes
	$se = floor($diff / $sec);		//The difference in seconds

	//Strings for each interval of time
	$wet = "week";
	$mot = "month";				
	$dat = "day";
	$yrt = "year";
	$hrt = "hour";
	$mit = "minute";
	$set = "second";

	//Whether the printed string is formatted
	$abt = true;

	//The number and string printed out
	$mnum = "";
	$mtxt = "";
	
	//Figure out what what's to find the largest interval of time
	if($yr=="0")
	{
		if($mo=="0")
		{
			if($da=="0")
			{
				if($hr=="0")
				{
					if($mi=="0")
					{
						if($se=="0")
						{
							$dispstr="Just Now";
							$abt = false;
						}
						else
						{
							if($se!="1"){$set.="s";}
							$mnum=$se;
							$mtxt=$set;
						}
					}
					else
					{
						if($mi!="1"){$mit.="s";}
						$mnum=$mi;
						$mtxt=$mit;
					}
				}
				else
				{
					if($hr!="1"){$hrt.="s";}
					$mnum=$hr;
					$mtxt=$hrt;
				}
			}
			else if($da>7)
			{
				$wk = round($da/7);
				if($wk!="1"){$wet.="s";}
				$mnum=$wk;
				$mtxt=$wet;
			}
			else
			{
				if($da!="1"){$dat.="s";}
				$mnum=$da;
				$mtxt=$dat;
			}
		}
		else
		{
			if($mo!="1"){$mot.="s";}
			$mnum=$mo;
			$mtxt=$mot;
		}
	}
	else
	{
		if($yr!="1"){$yrt.="s";}
		$mnum=$yr;
		$mtxt=$yrt;
	}
	
	if($abt){
		$dispstr=strval(intval($mnum)) . " " . $mtxt;
	}
	
	return $dispstr;
}
?>
