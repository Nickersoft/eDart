<?php
include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php";

if(!$_SESSION["userid"]||!isset($_FILES["pp_upload"])){die;}

//Get the maximum allowed image size by PHP
$maxsize 		= return_bytes(ini_get('post_max_size'));

//An array of allowed image formats
$allowedexts 	= array("gif", "jpeg", "jpg", "png", "bmp");

//Split the file name by '.'
$temp			= explode(".", $_FILES["pp_upload"]["name"]);

//And use this to get its extension
$ext			= strtolower(end($temp));

//If we're allowed to upload the image and it's not too big...
if(($_FILES['pp_upload']['size'] < $maxsize) && in_array($ext, $allowedexts))
{
	//Resize it to w=500
	$img_contents = WideImage::load("pp_upload")->resize(500)->asString('jpg');
    $base64 = base64_encode($img_contents);
}

HTML::begin();
Head::make("Crop Your Image", false);
Body::begin(false);
?>

<div id="cropbox">
  <div class="container">
    <div class="row">
      <div class="span12">
        <div class="jc-demo-box">

          <div class="page-header">
            <h1>Crop Your Image</h1>
          </div>

          <img src="data:image/png;base64,<?php echo $base64; ?>" style="display:inline-block;" id="target" />

          <div id="allcont">

            <div id="preview-pane" >
              <div class="preview-container" style="width:200px;height:200px;">
                <img src="data:image/png;base64,<?php echo $base64; ?>" id="img_preview" class="jcrop-preview" alt="Preview" />
              </div>
            </div>

            <!-- This is the form that our event handler fills -->
            <form action="/scripts/php/ajax/me/crop.php" style="position:absolute;bottom:10px;right:10px;" method="post" onsubmit="return check_coordinates();">
              <input type="hidden" id="x" name="x" />
              <input type="hidden" id="y" name="y" />
              <input type="hidden" id="w" name="w" />
              <input type="hidden" id="h" name="h" />
              <input type="hidden" id="img" name="img" value="<?php echo $base64; ?>" />
              <input type="submit" value="Crop Image" class="gbtn" style="display:inline-block;" />
              <input type="button" onclick="window.location='/me';" value="Cancel" class="bbtn" style="display:inline-block;" />
            </form>
          </div>

          <div class="clearfix"></div>

        </div>
      </div>
    </div>
  </div>
</div>
<div id="dimit" style="display:block;"></div>
<?php
Body::end(false);
HTML::end();
?>
