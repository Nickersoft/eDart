<?php

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

$onload = null;

abstract class Body
{
	public function add_action($javascript, $debug = false)
	{
		global $onload;
		$debug_string = ($debug) ? "alert(e);" : "";
		$onload = ($onload) ? ($onload . "try\{$javascript;\}catch(e){$debug_string}") : ($javascript . ";");
	}

	public function begin($include_banner = true)
	{
		global $onload;

		$body_tag = ($onload) ? "<body onload=\"$onload\">" : "<body>";

		if($include_banner)
		{
			include_once $_SERVER["DOC_ROOT"] . "/scripts/php/html/banner.php";
		}

		$body_tag .= "<main>";

		$body_tag .= "<div id=\"loader\"></div>";

		echo minify($body_tag);
	}

	public function end($include_footer = true)
	{
		$body_end = <<<END
					</main>
				<script type="text/javascript" src="/lib/jquery-1.10.2/jquery-1.10.2.min.js"></script>
				<script type="text/javascript" src="/lib/chosen/chosen.jquery.min.js"></script>
				<script type="text/javascript" src="/lib/jquery-placeholder/jquery.placeholder.js"></script>
				<script type="text/javascript" src="/lib/purl/purl.js"></script>
				<script type="text/javascript" src="/lib/toastr/toastr.min.js"></script>
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
