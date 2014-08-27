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

/* * * * * * * * * * * * * * *
 * ADD JAVASCRIPT TO THE MIX *
 * * * * * * * * * * * * * * */

$js_arr = array();

//Custom JS
foreach (glob($_SERVER["DOC_ROOT"]."/scripts/js/*.js") as $filename) {
  array_push($js_arr, $filename);
}

//Bootstrap
foreach (glob($_SERVER["DOC_ROOT"]."/lib/bootstrap/js/*.js") as $filename) {
  array_push($js_arr, $filename);
}

//Vex
foreach (glob($_SERVER["DOC_ROOT"]."/lib/vex/js/*.js") as $filename) {
	array_push($js_arr, $filename);
}

/* * * * * * * * * * * * * * *
 *    ADD CSS TO THE MIX     *
* * * * * * * * * * * * * * */

$css_arr = array();

//Custom CSS
foreach (glob($_SERVER["DOC_ROOT"]."/scripts/css/*.css") as $filename) {
  array_push($css_arr, $filename);
}

//Vex
foreach (glob($_SERVER["DOC_ROOT"]."/lib/vex-2.2.1/css/*.css") as $filename) {
	array_push($css_arr, $filename);
}

//Add it to the config
return array(
    'js'  => $js_arr,
    'css' => $css_arr
);

?>
