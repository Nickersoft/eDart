<?php
/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */
include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$targ_w = $_POST['w'];
	$targ_h = $_POST['h'];
	$jpeg_quality = 100;

	$src   = base64_decode($_POST["img"]);

	$img_r = imagecreatefromstring($src);
	$dst_r = ImageCreateTrueColor($targ_w, $targ_h );

	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
	$targ_w,$targ_h,$_POST['w'],$_POST['h']);

	$img_data = WideImage::load($dst_r)->resize(200)->asString('jpg');

	$thisUser = new User(array("action"=>"update", "fields"=>array("profile_pic"=>$img_data)));
    $thisUser->run(true);

	header('Location: /me');

	exit;
}
?>
