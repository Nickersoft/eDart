<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
				include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Include core functionality

			/* * * UPLOAD ITEM PICTURE * * */

				//If we're trying to upload an item image
				if(isset($_FILES["item_upload"]))
				{

						//Make sure it's not too big...
						if($_FILES['item_upload']['error'] === UPLOAD_ERR_INI_SIZE) 
						{
							//If it is, throw an error and exit
							echo "<script>alert(\"Now now, don't be a bandwidth hog! This file is pretty big. Got something smaller?\");</script>";
							exit;
						}

						//An array of the allowed image formats
						$allowedexts 	= array("gif", "jpeg", "jpg", "png", "bmp");

						//Split the filename by '.' and get its extension
						$temp		= explode(".", $_FILES["item_upload"]["name"]);
						$ext		= strtolower(end($temp)); 

						//If we're allowed to upload the image...
						if(in_array($ext, $allowedexts))
				        {
				        	//Get the temporary file name
				        	$img_data = $_FILES["item_upload"]["tmp_name"];

				        	//Get the image size
				        	$img_size = getimagesize($_FILES["item_upload"]["tmp_name"]);

				        	//Use WideImage to resize it to 500x500
							$img_contents = WideImage::load("item_upload")->resize(500)->asString('jpg');

							//Get its base 64 code
							$base64 = base64_encode($img_contents);

							//Use JavaScript to change the item image in the div
				        	$item_js =  <<<IJS
				        			<script>
				        			try
				        			{
				        				parent.document.getElementById('itemupload_code').value='$base64';
				        				parent.document.getElementById('wz_picture').style.background='url(data:image/jpg;base64,$base64) no-repeat center center';
										parent.document.getElementById('wz_loader').style.display='none';
									}
									catch(e){}
				        	</script>
IJS;
							echo $item_js;
							exit;
				       }
				}
		?>
	</body>
</html>