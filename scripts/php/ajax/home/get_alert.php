<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

if(isset($_POST["code"]))
{
	echo Lookup::Alert($_POST["code"]);
}

?>
