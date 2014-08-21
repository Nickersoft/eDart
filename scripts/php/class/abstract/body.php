<?php

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

$onload = null;

abstract class Body
{
	public static function add_action($javascript, $debug = false)
	{
		global $onload;
		$debug_string = ($debug) ? "alert(e);" : "";
		$onload = ($onload) ? ($onload . "try{" . $javascript . ";}catch(e){".$debug_string."}") : ($javascript . ";");
	}

	public static function begin($include_banner = true, $no_padding = false)
	{
		global $onload;

		$body_tag = ($onload) ? "<body onload=\"$onload\">" : "<body>";

		if($include_banner)
		{
			include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/banner.php";
		}

		$body_tag .= "<main";
		
		if($no_padding)
		{
			$body_tag .= " class = \"reset_padding\"";
		}
		
		$body_tag .= ">";

		$body_tag .= "<div id=\"loader\"><span class=\"uk-icon-circle-o-notch uk-icon-spin\"></span></div>";

		echo minify($body_tag);
	}

	public static function end($include_footer = true)
	{
		$body_end = <<<END
					</main>
				<script type="text/javascript" src="/lib/jquery-1.10.2/jquery-1.10.2.min.js"></script>
				<script type="text/javascript" src="/lib/chosen/chosen.jquery.min.js"></script>
				<script type="text/javascript" src="/lib/jquery-placeholder/jquery.placeholder.js"></script>
				<script type="text/javascript" src="/lib/jquery-sticky/jquery.sticky-kit.min.js"></script>
				<script type="text/javascript" src="/lib/purl/purl.js"></script>
				<script type="text/javascript" src="/lib/toastr/toastr.min.js"></script>
				<script type="text/javascript" src="/lib/uikit-2.9.0/js/uikit.min.js"></script>
				<script type="text/javascript" src="/lib/min/?g=js"></script>
END;

		echo $body_end;

		if($include_footer)
		{
			include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/footer.php";
		}

		echo "</body>";
	}
}

?>
