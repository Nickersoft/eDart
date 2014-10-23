<?php
/*
 * Page Name: Report Abuse
 * Purpose: Allows users to report an abusive item
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Import core functionality

HTML::begin();
Head::begin("Report Abuse");

?>
<style>


	p {
	text-indent:20px;
	color:black;
	margin:0px;
	margin-top:15px;
	margin-bottom:5px;
	text-align:left;
	padding:0px 50px 0px 50px;
	}

	#libtable {display:block; margin:0 auto;  margin-top:20px; }
	#libtable td { padding:10px; }

	.align{width:100%;display:block;margin:0px auto;margin-bottom:50px;}

	ul { margin-left:60px;}

</style>
<?php
Head::end();
Body::add_action("showError()");
Body::begin();
?>
					<div class="layout-1200 uk-container-center">
						<h1 class="uk-text-center" style="font-size:3.5em;">Report Abuse</h1>

						<?php
							//The default welcome HTML if a form has yet to be submitted
							$inner = <<<EOF
								<p>We try to keep it clean around here at eDart. But, unfortunately, now and then some wise-ass will post something that's inappropriate on our site. As a refresher, our idea of "inappropriate items for trade" include, but are not limited to:</p>
								<ul>
									<li>Sexual favors</li>
									<li>Any form of alcohol or intoxicating beverage</li>
									<li>Drugs of any sort, whether they are illegal or not. This is just a bad idea in general.</li>
									<li>Firearms or any harmful medium which can put the lives of others in danger</li>
									<li><b>Hand-burned copies</b> of CDs, DVDs, or other means of piracy</li>
									<li>Items that are suspected to have been tampered with prior (e.g. a toaster that secretly has a bomb planted in it)</li>
								</ul>
								<p>To clarify, this does <b>not</b> include:</p>
								<ul>
									<li>Any religious text you personally don't agree with</li>
									<li>Any political media you don't support personally</li>
									<li>Basically anything that isn't universally frowned upon by society</li>
								</ul>
								<p>If you have come across an item on the site you think may be dangerous to the lives of others or is something we might just not like in general (whilst still matching the above criteria), you can use the below form to report the item to us.</p>
								<p class="uk-text-center"><b>Please note you must click the checkbox below to submit this form.</b></p>

								<form id="report_form" action="./" method="post" style="display:block; margin:20px auto; position:relative; width:500px;">
									Item URL: <input type="text" name="iurl" id="iurl" /><br/>

									Reason for abuse: 

									<select data-default="Item URL" class="chosen-select" name="acat" id="acat" />
										<option>Prostitution/Pornographic</option>
										<option>Alcoholic</option>
										<option>Involves prescription medication</option>
										<option>Involves illegal drugs</option>
										<option>Violates copyright</option>
										<option>It's a prohibited weapon</option>
										<option>Seems suspicious</option>
										<option>Other</option>
									</select>

									<div id="achkhold" >
										<input type="checkbox" onchange="rang_submit(this, document.getElementById('abuse_submit'));" /> <div id="achktxt">I agree that the item in which I am reporting matches the above criteria</div>
									</div>

									<input type="submit" id="abuse_submit" class="button_primary blue" style="margin:20px auto 0px auto; display:block;" value="Submit" disabled />

								</form>
EOF;

							//If the form has been submitted...
							if(isset($_POST["iurl"])&&isset($_POST["acat"]))
							{
								//Make sure nothing is blank
								$url_empty = (trim($_POST["iurl"])=="");
								$cat_empty = (trim($_POST["acat"])=="");
								$not_url = (trim($_POST["iurl"])=="");

								//If it is, print out the modal error box
								if($url_empty||$cat_empty||$not_url)
								{
									$error_box = <<<EOL
									<div id="error_display" class="modal fade">
									  <div class="modal-dialog">
									    <div class="modal-content">
									      <div class="modal-header">
									        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									        <h4 class="modal-title">Could Not Sign Up</h4>
									      </div>
									      <div id="error_body" class="modal-body">
									       		Please enter a valid item URL and category before submitting.
									      </div>
									      <div class="modal-footer">
									        <button type="button" data-dismiss="modal" class="bbtn">Okay</button>
									      </div>
									    </div>
									  </div>
									</div>

									<script>function showError(){\$('#error_display').modal();}</script>
EOL;
									echo $error_box;
								}
								else //If everything looks good...
								{
									//...send the report to the webmaster and change the welcome text
									sendMail("nickersoft@gmail.com", "Great and Powerful", "Man", "Item Reported for Abuse", "An item was reported for the following reason: " . $_POST["acat"], $_POST["iurl"], "Go to Item");
									$inner = "Report sent! Thanks!</br><input type=\"button\" value=\"Go Home\" style=\"margin:20px auto;display:block;\" onclick=\"window.location='/'\" class=\"button_primary blue\" />";
								}

							}

							echo $inner; //Print out the welcome text
						?>
					</div>
		<?php
			Body::end();
			HTML::end();
		?>
