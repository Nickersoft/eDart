<?php
include_once "call.php";
	
function gen_time_key()
{
	return md5(strval(time()));
}

function hash_password($password,$salt=null)
{
	$rice="";
	
	if(trim($salt)!="")
	{
		$rice = trim($salt);
	}
	else 
	{
		$rice = random_key(SALT_LEN);
	}
	
	$hash = hash("sha256", $password.$rice);
	$hash .= $rice;

	return $hash;
}

function return_salt($password)
{
	$pepper = "";
	for($i = strlen($password)-SALT_LEN; $i< strlen($password);$i++)
	{
		$pepper.= $password[$i];
	}
	
	return $pepper;
}

function random_key($len)
{
	$lo_ar = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
	$ca_ar = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$nu_ar = array("1","2","3","4","5","6","7","8","9","0");
	$final = "";
	for($i = 0; $i <= $len - 1; $i++)
	{
		$arpicki = rand(0,2);
		$arpick  = array();
		switch($arpicki)
		{
			case 0:
				$arpick = $lo_ar;
				break;
			case 1:
				$arpick = $ca_ar;
				break;
			case 2:
				$arpick = $nu_ar;
				break;
		}
		$random = rand(0,count($arpick)-1);
		$final.=$arpick[$random];
	}
	return $final;
}
	

?>