<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

class Meetup
{
	private $date;

	function __construct($pickup_date)
	{
		global $date;

		$date = $pickup_date;

		return;
	}

	public function output()
	{
		global $date;
		$dd = date("l, F jS, Y", $date);
		$reldt = getRelativeDT($date, time());
		$html = <<<EOD
				<div class="hdr btxt">Meeting Date</div>
				<div class="head">$dd</div>
				<div id="cntdwn" class="cdlt">$reldt</div>
				<div style="color:white;text-align:center;font-size:25px;" class="ltxt">until meetup</div>
EOD;
		echo $html;
		return;
	}
}

?>