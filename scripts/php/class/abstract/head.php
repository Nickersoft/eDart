<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

$title_string = "";

abstract class Head
{
	public static function begin($title, $use_prefix = true)
	{
		global $title_string;
		$title_string = ($use_prefix) ? ("eDart Beta | " . $title) : $title;

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
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/uikit-2.9.0/css/uikit.almost-flat.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/uikit-2.9.0/css/addons/uikit.addons.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/fonts/Titillium/stylesheet.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/chosen/chosen.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/toastr/toastr.min.css">
					<link rel="stylesheet" type="text/css" media="screen" href="/lib/min/?g=css">
							
					<!--[if gte IE 9]>
					  <style type="text/css">
					    .gradient {
					       filter: none;
					    }
					  </style>
					<![endif]-->
							
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
