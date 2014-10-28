<?php 
/* 
 * Page Name: Report a Bug
 * Purpose: Allows users to file bug reports for open beta
 * Last Updated: 6/4/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include all core functionality

//If there form has been submitted, send an email to the webmaster
if(isset($_POST["bug_desc"]))
{
	sendMail("nickersoft@gmail.com", "Tyler", "Nickerson", "Bug Report", "A user has found the following bug (oh no!):<br/><br/>" . $_POST["bug_desc"], "/", "Go to eDart");
	header("Location: ./?sent=true"); //Redirect to 'sent' version of page
}

HTML::begin();
Head::begin("Report a Bug");

?>

<style type="text/css">

.align { width:800px; display:block; margin:0px auto; margin-bottom:50px; }

#bug_desc { resize:none; width:300px; height:100px; font-size:12px; margin-bottom:10px; }

a{text-decoration:underline;color:black;}

</style>

<?php 
Head::end();
Body::begin();
?>

<div id="mc_cont">

	<div id="mc">
		
		<div class="align">

			<?php
				$title = "Report a Bug"; //Default title
				$body  = "Okay, we know we're not perfect. So drop the act. By being an active community member of eDart Beta, 
			you can help us by submitting a report for any bugs you may find in our system. Just type a brief description below, 
			and click \"Send\". Completely anonymous. Promise. Although our servers keep a record of your IP address, it is not associated with your account and we do not check the logs unless a legal obligation requires us to do so."; //Default body

				$noform = false; //Boolean as to whether to show the submit form or not

				if(isset($_GET["sent"])&&$_GET["sent"]=="true") //If the 'sent' flag has been set...
				{
					//...we already submitted the form. Change what will be displayed.
					$title = "Report Sent!";
					$body  = "Thanks a bunch, friendly user!";

					$noform = true; //Hide the form
				}
			?>

			<h1 style="font-size:3.5em;" class="uk-text-center"><?php echo $title; ?></h1>
		
			<p>
				<?php
					echo $body;
				?>
			</p>

			<?php

				//Here is our form
				$form = <<<EOD
				<form id="bug_form" name="bug_form" method="POST" class="uk-text-center" action="./">
					<textarea name="bug_desc" class="inpt uk-align-center" id="bug_desc"></textarea>
					<input type="submit" class="button_primary blue" value="Send" />
				</form>
EOD;

				//Whether or not it is displayed is up to the boolean we set earlier
				if(!$noform)
				{
					echo $form;
				}
			?>
	
		</div>
	</div>
</div>

<?php 
Body::end();
HTML::end();
?>