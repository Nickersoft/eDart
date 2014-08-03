<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

$title_string = "";

abstract class Head
{
	/*
	 *	  Name: in_string
	 * Purpose: Supporting function to tell whether a string is within a string
	 * Returns: Boolean
	 */
	private function in_string($haystack, $needle)
	{
		//Return whether the string's start index can be found
		return (strpos($haystack, $needle)!==false);
	}

	/*
	 *	  Name: is_mobile
	 * Purpose: Determines whether the user is using a mobile phone
	 * Returns: Boolean
	 */
	private function is_mobile()
	{
		//Get the current user agent
		$ua = $_SERVER["HTTP_USER_AGENT"];

		//If it contains any of the following browsers...
		if (
		in_string($ua, "Windows CE") ||
		in_string($ua, "AvantGo") ||
		in_string($ua,"Mazingo") ||
		in_string($ua, "Mobile") ||
		in_string($ua, "T68") ||
		in_string($ua,"Syncalot") ||
		in_string($ua, "Blazer") )
		{
			$DEVICE_TYPE="MOBILE"; //Set the device type to 'mobile'
		}

		//Return whether the device type is mobile
		return (isset($DEVICE_TYPE) && $DEVICE_TYPE=="MOBILE");
	}

	public static function begin($title, $use_prefix = true)
	{
		global $title_string;
		$title_string = ($use_prefix) ? ("eDart Beta | " . $title) : $title;

		$mobile_css = "";

		//If the device is mobile...
		if(is_mobile())
		{
			//...use a mobile stylesheet
			$mobile_css = "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/scripts/css/mob/mobile.css\">";
		}

		$head_tag = <<<HEAD
				<head>
					<title>$title_string</title>

					<link rel="SHORTCUT ICON" href="/favicon.ico" />

					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0" />
					<meta name="description" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students." />
					<meta name="keywords" content="edart,beta,bartering,tradegrouper,trade,trading,tradby,college,worcester,polytechnic,institute,wpi,2013,free,online,database" />
					<meta name="robots" content="index, follow" />
					<meta name="Headline" content="Welcome to eDart!">
					<meta name="CPS_SITE_NAME" content="Welcome to eDart!">
					<meta property="og:title" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students.">
					<meta property="og:type" content="website">
					<meta property="og:description" content="eDart is a first-of-its-kind, completely web-based, universal trading application for WPI students.">
					<meta property="og:site_name" content="eDart">
					<meta charset="UTF-8">

					<noscript>
						<meta http-equiv="refresh" content="0;URL=/noscript.php">
					</noscript>

					<link rel="stylesheet" type="text/css" media="screen" href="/fonts/Vegur/stylesheet.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/fonts/Titillium/stylesheet.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/font-awesome/css/font-awesome.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/chosen/chosen.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/toastr/toastr.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/min/?g=css">

					<script>
						document.cookie='';

						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

						ga('create', 'UA-44057002-1', 'wewanttotrade.com');
						ga('send', 'pageview');
					</script>


HEAD;
		echo minify($head_tag);
	}

	public static function end()
	{
		echo "</head>";
	}

	public static function make($title, $use_prefix = true)
	{
		self::begin($title, $use_prefix);
		self::end();
	}
}

?>
