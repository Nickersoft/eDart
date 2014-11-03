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
Head::begin("Internships");
?>
<style>p { text-indent:25px; text-align:justify; }</style>
<?php 
Head::end();
Body::add_action("showError()");
Body::begin();
?>
					<div class="layout-978 uk-container-center">
						<h1 class="uk-text-center" style="font-size:3.5em;line-height:1.5em;">Work with Us</h1>
						<h2 class="uk-text-center" style="color:gray;font-size:1.4em;"><i>It's time to put the 'you' in future.</i></h2>
						<p>
							So, you want to change the world, huh? Have the desire to help create a future of online trade, a marketplace without price tags?
							Then you've come to the right place. By interning at eDart, you will not only be helping to better the future of 
							society, but also gain valuable development skills that will better your future as an individual. Please note that
							eDart internships at the current time are <b>unpaid</b> and <b>completely voluntary</b>. That being said, we hope that
							by setting up the employment system in this way, we will filter out those who are driven entirely by money and bring in
							those who are truly passionate about our mission and purpose. Those who work should not work for money, but for themselves.
							Create what you want to exist in the world, not what others tell you should exist. Thus idealogy lies at the core of our establishment.
						</p>
						<p>
							In order to apply for a position at eDart, you must be an <b>undergraduate</b> college student pursuing a degree in either
							<b>computer science or any field relating to UX design and concept.</b> In addition, you must be well versed in at least one
							or more of the following skill sets:
						</p>
						<ul>
							<li>PHP 5+</li>
							<li>HTML5</li>
							<li>CSS3 and CSS3 Frameworks (Boostrap, UIKit, Foundation, etc.)</li>
							<li>Laravel, Rails, or other MVC Frameworks</li>
							<li>Photoshop and Graphic Design</li>
							<li>iOS Development (Swift and/or Objective C)</li>
						</ul>
						<p>
							If accepted into an internship, you will be added to the developer's mailing list. 
							Meetings will be held once a week and your public key will be required in order to use the current Git setup. 
						</p>
						<div class="uk-text-center">
						<a class="button_primary green" href="http://goo.gl/forms/YCdUlA3Ngl" target="_blank">Apply Now</a>
						</div>
					</div>
		<?php
			Body::end();
			HTML::end();
		?>
