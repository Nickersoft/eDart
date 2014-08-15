<?php

/*
 * Page Name: About
 * Purpose: Tell the nice user about what we do
 * Last Updated: 6/4/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

//Start the session
if(!isset($_SESSION)){session_start();}

//Stock function for counting code lines in a directory
function countLines($filepath)
{
	/*** open the file for reading ***/
	$handle = fopen( $filepath, "r" );
	/*** set a counter ***/
	$count = 0;
	/*** loop over the file ***/
	while( fgets($handle) )
	{
		/*** increment the counter ***/
		$count++;
	}
	/*** close the file ***/
	fclose($handle);
	/*** show the total ***/
	return $count;
}

//Get all files in root
function getFiles()
{
	$items = glob($_SERVER["DOC_ROOT"]."/*"); //Get all subdirectories

	//Loop through directories
	for($i = 0; $i < count($items); $i++)
	{
	//If it's not the library folder
		if(strpos($items[$i], "lib")===false)
		{
			//And is a directory
			if(is_dir($items[$i]))
			{
				$add = glob($items[$i] . "/*"); //Get all folders in subdirectory
				$items = array_merge($items, $add); //Add it to the master array
			}
		}
	}

					return $items; //Return the master array
}

$total = 0; //Total line count
$files = getFiles(); //Get all of the directories throughout the entire directory

//Loop through
foreach($files as $f)
{
$total += countLines($f); //and add the line count
}

//We'll print this out all later.

HTML::begin();
Head::make("About Us");
Body::begin();
?>
		<style>

		#code_count { border-top:1px solid green; font-family:TitilliumrRegular,Trebuchet MS, sans-serif;font-size:45px;color:green; padding:25px !important; text-align:center;}

		</style>
			<div style="min-height:520px;" class="layout-978 uk-container-center">
				<div class="uk-grid">
					<div class="uk-width-1-5">
						<ul class="uk-nav uk-nav-side" data-switcher-parent="#panel_parent">
						    <li><a data-switcher-id="#story">Story</a></li>
						    <li><a data-switcher-id="#team">Team</a></li>
						    <li><a data-switcher-id="#backend">Backend</a></li>
						    <li><a data-switcher-id="#contact">Contact</a></li>
						    <hr/>
						    <li class="uk-text-center" style="letter-spacing:1px;color:#B8B8B8;"><span><?php echo number_format($total); ?> Lines of Awesome</span></li>
						</ul>
					</div>
					<div id="panel_parent" class="uk-width-4-5">
					
						<div id="story" class="panel">
							<h1 class="text_large">The Story</h1>
	
							<p>
								The eDart Trading Initiative was started as a side-project of WPI freshman Tyler Nickerson during the August of 2013 in the bedroom of his parent's house. You can read his letter to the user (circa October 2013) <a href="./letter.php">here</a>.
								After long wondering the effects of society without monetary transaction, Nickerson set to develop a microcosm society in which
								the items being bought <i>are</i> the monetary transactions. Unlike other trading or bartering sites, you will never see a price tag on an eDart item.<super>*</super>
								There is no "sell or trade," like you might seen on sites like Craigslist or TradeGrouper. Just trade. As it was intended to be. With help from long time computer science associate and friend Preston Mueller, eDart
								was launched under the domain www.wewanttotrade.com in early 2014, a domain registered by Nickerson's aunt back in 2011.
							</p>
							<p>
								eDart enforces a principle called "reverse networking," which is revolutionizing the realm of online social networking. In today's modern age, it is incredibly common to be sucked into the inner realm of social media,
								making it easy to sustain your entire social life solely on the web. Believe it or not, that's not how it used to be. We here at eDart believe in people-to-people connections, not socket-to-socket connections (if you know what sockets are,
								we applaud you). That's why all of our transactions occur in person, and why we only show you the items closest to you. No shipping costs. Only friendly interactions. Who knows? Maybe you'll make a new friend in the process.
							</p>
							<p>
								Now, why eDart? What kind of name is that? Apart from using the 'e' prefix such as the sites eBay and eTrade use, the word 'eDart' is actually the word 'trade' backwards.
								And that's exactly what eDart is. Backwards trade. Google the term 'trade' and you will find online bartering sites and stock exchanges. eDart is neither of those things.
								eDart is what trade should be. Exchanging goods for goods. And that's exactly what we're trying to accomplish.
							</p>
	
							<p style="font-size:11px;text-align:right;">
								<super>*</super>With the exception of Estimated Market Value (EMV), shown next to all applicable items.
							</p>
						</div>
						
						<div id="team" class="panel">
							<h1 class="text_large">The Team</h1>
							<p>
								<table class="data_table valign_top">
									<tr>
										<td><img src="/profile.php?id=1&load=image&size=small"></td>
										<td><strong><a href="/profile.php?id=1">Tyler Nickerson</a></strong></td>
										<td>Founder, head programmer, and commander-in-chief.</td>
									</tr>
								</table>
								<table class="data_table valign_top">
									<tr>
										<td><img src="/profile.php?id=3&load=image&size=small"></td>
										<td><strong><a href="/profile.php?id=3">Preston Mueller</a></strong></td>
										<td>Server genius, Git wizard, and head of mobile development.</td>
									</tr>
								</table>
							</p>
						</div>
						
						<div id="contact" class="panel">
							<h1 class="text_large">Contact Us</h1>
							<p>Have some super cool suggestions that you think will improve the site? Just feel like sending us fan mail? Here's the best ways to get in contact with us here eDart:</p>
							<dl>
								<dd><i class="uk-icon-map-marker"></i> P.O. Box 100 Institute Rd, Worcester, MA 01609</dd>
								<dd><i class="uk-icon-phone"></i> 508-207-6765</dd>
								<dd><i class="uk-icon-envelope"></i> edart@wpi.edu</dd>
							</dl>
						</div>
						
						<div id="backend" class="panel">
							<h1 class="text_large">The Nerdy Stuff</h1>
	
							<p>
								eDart was (originally) built on an old <a href="http://dell.com/">Dell</a> PC running <a href="http://www.ubuntu.com/">Ubuntu Linux</a>, then later moved to a 2013 <a href="http://www.apple.com/macbook-pro/">MacBook Pro</a>. Written in PHP and JavaScript,
								the site was built entirely in the <a href="http://www.eclipse.org/kepler/">Eclipse Kepler IDE</a>. eDart is currently hosted on a computer running the light, high-performance HTTP server <a href="http://nginx.org/">Nginx</a> running <a href="http://php.net/">PHP5</a>. We use <a href="http://www.mysql.com/">MySQL</a> as a backend and sometimes <a href="http://www.barebones.com/products/textwrangler/">TextWrangler</a> for on-the-fly editing.
								We also use a <i>ton</i> of libraries, and we'd be nothing without them, so you can check those out below.
							</p>
	
							<p>
								<table class="data_table">
									<tr style="color:dimgray;">
										<td style="width:200px;">Library</td>
										<td>Description</td>
									</tr>
									<tr>
										<td><a href="http://jquery.com/">jQuery</td>
										<td>The crazy-popular JavaScript library for doing everything from UI design to insane animation. Us? We just use it to make AJAX calls (and some UI stuff, too).</td>
									</tr>
									<tr>
										<td><a href="http://jsanim.com/">jsAnim</a> <span>(Depreciated)</span></td>
										<td>A small, compact JavaScript animation library that we use to make panels slide in and out in your control panel (/me). However, the project is no longer supported by its creator, so we have since moved to jQuery for this effect. </td>
									</tr>
									<tr>
										<td><a href="http://wideimage.sourceforge.net/">WideImage</a></td>
										<td>Used for image sizing and modification.</td>
									</tr>
									<tr>
										<td><a href="http://deepliquid.com/content/Jcrop.html">JCrop</a></td>
										<td>JavaScript plugin for an interactive "cropbox" for image cropping.</td>
									</tr>
									<tr>
										<td>The <a href="http://go.developer.ebay.com/developers/ebay/products/finding-api">eBay Finding API</a></td>
										<td>One of eBay's many officially-supported APIs, this one allows us to scan eBay to generate a rough estimated market value for items.</td>
									</tr>
									<tr>
										<td>The <a href="https://developers.facebook.com/docs/graph-api">Facebook Graph API</a></td>
										<td>Facebook's official PHP and JavaScript SDKs to allow you to share this site with your friends.
									</tr>
	
									<tr>
										<td><a href="http://css3pie.com/">CSS3 PIE</a></td>
										<td>Short for "Progressive Internet Explorer", this HTC engine allows older versions of IE to render some of our high-quality CSS3 animations.</td>
									</tr>
									<tr>
										<td><a href="http://getbootstrap.com/">Bootstrap</a></td>
										<td>Released by Twitter, Inc, Bootstrap is a collection of high-end CSS designs and sleek jQuery animations. We use it for some of our <a href="http://getbootstrap.com/components/#glyphicons">icons</a>, our <a href="http://silviomoreto.github.io/bootstrap-select/">select boxes</a>, and a little animation.</td>
									</tr>
									<tr>
										<td><a href="http://phpmailer.worxware.com/">PHPMailer</a></td>
										<td>An open-source mailing library for PHP that allows us to send you those annoying emails.</td>
									</tr>
									<tr>
										<td><a href="http://fontsquirrel.com/">FontSquirrel</a></td>
										<td>Although not technically a library, we still feel we should give credit where credit is due. We use FontSquirrel to generate our custom fonts for use on the site.</td>
									</tr>
									<tr>
										<td><a href="http://glyphicons.com/">GLYPHICONS</a> <span>(Depreciated)</span></td>
										<td>The same icons that come with Boostrap, only in PNG form. That way we can load them into custom elements as background images.</td>
									</tr>
									<tr>
										<td><a href="https://code.google.com/p/minify/">Minify</a></td>
										<td>We're actually pretty pumped about this one. It compressed all of the CSS and JavaScript files on our server to deliver content to you faster.</td>
									</tr>
									<tr>
										<td><a href="http://aws.amazon.com/ses/">Amazon Web Services (AWS) Simple Email Sending (SES)</a></td>
										<td>Amazon's extensive API for sending outbound emails.</td>
									</tr>
									<tr>
										<td><a href="http://leafo.net/sticky-kit/">Sticky-Kit</a></td>
										<td>It was a simple way to make DIVs stick to the top of the screen when you scroll, okay?</td>
									</tr>
									<tr>
										<td><a href="http://fortawesome.github.io/Font-Awesome/">Font Awesome</a></td>
										<td>We're slowing phasing out Glyphicons to make way for the far superior, more expansive, way cooler, and indeed more awesome-er icon font kit that is Font Awesome.</td>
									</tr>
									<tr>
										<td><a href="http://geocoder-php.org/">Geocoder PHP</a></td>
										<td>A neat little replacement for the Google Maps API which allows us to do all location-based lookups on the server side instead of using JavaScript.</td>
									</tr>
									<tr>
										<td><a href="http://eonasdan.github.io/bootstrap-datetimepicker/">Bootstrap 3 Date/Time Picker</a></td>
										<td>A slick looking date/time picker for our "Add Item Wizard". Thanks, Internet!</td>
									</tr>
									<tr>
										<td><a href="http://momentjs.com/">Moment.js</a></td>
										<td>Required to run the date/time picker.</td>
									</tr>
									<tr>
										<td><a href="http://harvesthq.github.io/chosen/">Chosen.js</a></td>
										<td>What makes our selectboxes look freaking killer.</td>
									</tr>
	
								</table>
	
							</br>
	
							Don't try to hack us. We like our jobs.
	
							</p>
						
						</div>
					</div>
	
				</div>
				
			</div>
			</div>
			<?php
				Body::end();
				HTML::end();
			?>
