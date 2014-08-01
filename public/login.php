<?php
/*
 * Page Name: Generic Login Page
 * Purpose: Let the user log in a possibly redirect to a target landing page
 * Last Updated: 6/5/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

	include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

	HTML::begin();
	Head::make("Login");
	Body::begin();
?>

			<div id="login_cont" style="min-height:400px;">
				<div  id="login_pnl"><h1>You need to log in to do that.</h1>

					<script type="text/javascript">
						var lerr = false;
						function lenter(e)
						{
							if(e.keyCode == 13)
							{
								$('#loginbtn').click();
							}
						}
					</script>

					<table id="loginbx" style="height:auto;width:400px;margin-top:40px;">
						<tr>
							<td>
								<input 	name=		"leaddr"
									type=		"text"
									id=		"leaddr"
									class=		"inpt"
									style=		"font-size:18px;float:left;width:400px;margin-top:0px;"
									autocomplete=	"off"
									placeholder=		"Email Address"
									onkeydown=	"lenter(event);"
								/>
							</td>
						</tr>

						<tr>
							<td>
								<input 	style="color:black;
											width:400px;
											font-size:18px;
											margin-top:0px;"
									class=		"inpt"
									name=		"lpword"
									id=		"lpword"
									placeholder="Password"
									autocomplete=	"off"
									type=		"password"
									onkeydown=	"lenter(event);"
								/>
							</td>
						</tr>

						<tr>
							<td>
								<?php
									$con = mysqli_connect(host(), username(), password(), mainDb());

									$rdr = "me"; //The default place we'll redirect to

									//If we're set to redirect
									if(isset($_GET["redirect"])&&trim($_GET["redirect"]!=""))
									{
										$rdr = $_GET["redirect"]; //Change the redirect location
									}

									//Construct the button with the redirect location
									$button_html = <<<TJN
										<input 	type=	"button"
												style=	"width:100px;font-size:14px;height:30px;padding-top:4px;display:block;margin:0 auto;margin-top:10px;"
												class=	"gbtn"
												id="loginbtn"
												onclick="login(document.getElementById('leaddr').value,document.getElementById('lpword').value, '$rdr',function(){});"
												value=	"Go"
										/>
TJN;

									echo $button_html; //and print it
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		<?php
		Body::end();
		HTML::end();
		?>
