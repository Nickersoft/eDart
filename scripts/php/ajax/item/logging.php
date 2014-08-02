<?php
if((isset($_POST["url"])."")&&(trim($_POST["url"]))){
$path = $_SERVER["DOC_ROOT"]."/private/report.log";
file_put_contents($path, trim($_POST["url"])."\n\n", FILE_APPEND);
}

?>