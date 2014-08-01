<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

$js_arr = array();
foreach (glob($_SERVER["DOC_ROOT"]."/scripts/js/*.js") as $filename) {
  array_push($js_arr, $filename);
}

foreach (glob($_SERVER["DOC_ROOT"]."/lib/bootstrap/js/*.js") as $filename) {
  array_push($js_arr, $filename);
}

$css_arr = array();

foreach (glob($_SERVER["DOC_ROOT"]."/scripts/css/*.css") as $filename) {
  array_push($css_arr, $filename);
}

array_push($css_arr, $_SERVER["DOC_ROOT"] . "/lib/glyphicon/icon.css");
array_push($css_arr, $_SERVER["DOC_ROOT"] . "/lib/datetimepicker/css/bootstrap-datetimepicker.css");
array_push($css_arr, $_SERVER["DOC_ROOT"] . "/lib/jquery-ui/css/south-street/jquery-ui-1.10.4.custom.min.css");

array_push($js_arr, $_SERVER["DOC_ROOT"] . "/lib/moment/moment.min.js");
array_push($js_arr, $_SERVER["DOC_ROOT"] . "/lib/jquery-ui/js/jquery-ui-1.10.4.custom.min.js");
array_push($js_arr, $_SERVER["DOC_ROOT"] . "/lib/datetimepicker/js/bootstrap-datetimepicker.js");
array_push($js_arr, $_SERVER["DOC_ROOT"] . "/lib/jcrop/js/jquery.Jcrop.js");
array_push($js_arr, $_SERVER["DOC_ROOT"] . "/lib/jquery-sticky/jquery.sticky-kit.min.js");

return array(
    'js'  => $js_arr,
    'css' => $css_arr
);

?>
