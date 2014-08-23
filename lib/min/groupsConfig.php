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

return array(
    'js'  => $js_arr,
    'css' => $css_arr
);

?>
