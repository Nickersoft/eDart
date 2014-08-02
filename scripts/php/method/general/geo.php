<?php
/* 
 * Page Name: Geolocator
 * Purpose: Finds a user's location given their IP
 * Last Updated: 6/15/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

include_once $_SERVER["DOC_ROOT"] . "/scripts/php/core.php";

function get_location()
{
	$geocoder = new \Geocoder\Geocoder();
	$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();
	$chain    = new \Geocoder\Provider\ChainProvider(array(
	    new \Geocoder\Provider\FreeGeoIpProvider($adapter),
	    new \Geocoder\Provider\HostIpProvider($adapter),
	    new \Geocoder\Provider\GoogleMapsProvider($adapter, 'en_US', 'USA', true),
	    new \Geocoder\Provider\BingMapsProvider($adapter, '<API_KEY>'),
	    // ...
	));
	$geocoder->registerProvider($chain);

	$result = $geocoder->geocode($_SERVER["REMOTE_ADDR"]);

	$dumper = new \Geocoder\Dumper\GeoJsonDumper();
	$geo_ar = $dumper->dump($result);

	return json_decode($geo_ar, true);
}
?>