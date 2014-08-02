<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

$con = mysqli_connect(host(), username(), password(), mainDb());
$q   = mysqli_query($con, "SELECT * FROM offers ");
$cnt = 0;
while($r=mysqli_fetch_array($q))
{
	$cur_dt = strtotime(date("Y-m-d H:i", time()));
	$meetdt = strtotime($r["meetdt"]);
	$hourinsec = 60*60*24;
	$diff = ($meetdt-$cur_dt)/$hourinsec;
	error_log("HOURS TILL MEET DATE: " . $diff);
	
	if(($diff==1))
	{
		$u1name = "eDart User";
		$u2name = $u1name;
		
		$u1q = mysqli_query($con, "SELECT * FROM usr WHERE id='".mysqli_real_escape_string($con, $r["user1id"])."'");
		$u2q = mysqli_query($con, "SELECT * FROM usr WHERE id='".mysqli_real_escape_string($con, $r["user2id"])."'");
		
		while($ur1=mysqli_fetch_array($u1q))
		{
			$u1name=$ur1["fname"] . " " . $ur1["lname"];
		}
		while($ur2=mysqli_fetch_array($u2q))
		{
			$u2name=$ur2["fname"] . " " . $ur2["lname"];
		} 
	
		$locarr = json_decode($r["meetloc"],true);

		$addstr = $locarr["stadd1"].", ";
		$stadd2  = $locarr["stadd2"];
		if(trim($stadd2)!="")
		{
			$addstr .= $stadd2 . ", ";
		}
		$addstr.=$locarr["citytown"] . ", " . $locarr["state"];
		$gmapslnk = "http://maps.google.com/?q=" . urlencode($addstr);
		$msg = "You have an exchange with %s tomorrow";
		$u1msg = sprintf($msg, $u2name);
		$u2msg = sprintf($msg, $u1name);
		$link = "exchange.php?offerid=".$r["offerid"];
		$gmaplnktxt = " at " . date("g:i A", $meetdt) . " at <a href=\"".$gmapslnk."\">" . $locarr["stadd1"] . "</a>";
	
		 	sendNotify($r["user1id"], $u1msg . $gmaplnktxt, $link, trim($u1msg));	
			sendNotify($r["user2id"], $u2msg . $gmaplnktxt, $link, trim($u2msg));
	}
}
?>