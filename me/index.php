<?php
include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";
header("Location:/profile.php?id=".$_SESSION["userid"]);
?>
