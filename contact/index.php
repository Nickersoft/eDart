<?php 

/* 
 * Page Name: Contact eDart
 * Purpose: Allows users to contact us
 * Last Updated: 6/4/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

HTML::begin();
Head::begin("Contact Us");
?>

<style>
.norm{
color:#2b2b2b;
text-align:center;
font-size:50px;
margin-top:20px;
}

p {
text-indent:20px;
color:black;
margin:0px;
margin-top:5px;
margin-bottom:5px;
text-align:justify;
padding:0px 50px 0px 50px;
}

#block { text-transform:uppercase; color:black; margin-left:100px;text-indent:0px; display:block; }

a{text-decoration:underline;color:black;}

</style>

<?php 
Head::end();
Body::begin();
?>

<div id="mc_cont">
	<div id="mc">
		<div class="align" style="margin-bottom:50px;">
			<div class="norm" >Need Us?</div>
		
				<p>
					Please send all fan mail or suggestions to:

					<div id="block">
						P.O. Box #3678</br>
						100 Institute Road</br>
						Worcester, MA 01609
					</div>
				</p>
			</br>

			<p>
				Want to talk to us one-on-one? Contact us at 508-207-6765 or at <a href="mailto:nickersoft@gmail.com">nickersoft@gmail.com</a> (don't worry- we're working on a corporate email!).
			</p>
		</div>
	</div>
</div>


<?php 
Body::end();
HTML::end();
?>