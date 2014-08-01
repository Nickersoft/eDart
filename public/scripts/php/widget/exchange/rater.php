<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

class Rater
{
	private $id;

	function __construct($user_id)
	{
		global $id;

		$id= $user_id;

		return;
	}

	public function output()
	{
		global $id;

		$ten_bars = "";

		for($i=0; $i<=9; $i++)
		{
			$ten_bars .= "<td data-index=\"".$i."\" class=\"bar\"></td>";
		}

		$otherUser = new User(array("action"=>"get", "id"=>$id));
		$otherInfo = $otherUser->run(true);
		$fname = $otherInfo[0]["fname"];
		$query_str = $_SERVER["QUERY_STRING"];
		$html = <<<EOD
					<div class="hdr btxt">Rate $fname <div style="font-size:18px;display:inline-block;">(anonymously)</div></div>
						<form method="post" id="rate_form" action="./exchange.php?$query_str" >
							<table id="ratetable">
EOD;

		$qualities_array = array("Reliability", "Friendliness", "Consistency", "Overall Experience");

		for($i = 0; $i < count($qualities_array); $i++)
		{
			$q = $qualities_array[$i];
			$html .= <<<EOF
							<tr class="rankrow">
								<td class="ltxt">$q</td>
								<td>
									<table class="ranktbl">
										<tr>
											$ten_bars
										</tr>
										<input type="hidden" name="rank_$i" id="rank_val" value="9">
									</table>
								</td>
								<td id="rtstat">

								</td>
							</tr>
EOF;
		}

		$html .= <<<EOA
						<tr>
							<td colspan="2">
								<textarea id="moreinfo" style="" name="rate_desc" placeholder="Write about your experience here"></textarea>
							</td>
							<td style="vertical-align:bottom;">
								<input  type="button"
										class="bbtn"
										style="margin-left:10px;width:100%;"
										onclick="show_rank_thanks();"
										value="Send Rating"
								/>

								<div id="skiptext">Or skip this step</div>
						</tr>
					</table>
				</form>
EOA;

		echo $html;
	}
}

?>
