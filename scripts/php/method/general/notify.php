<?php
/*
 * Page Name: Notify
 * Purpose: Send notifications and emails
 * Last Updated: 6/6/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

//DON'T TOUCH THIS. LET TYLER TAKE CARE OF IT.//

include_once $_SERVER["DOC_ROOT"]."/scripts/php/core.php"; //Import core functionality

function sendNotify($userid, $msg, $link, $subject=null)
{
	$umail = "noreply@wewanttotrade.com";
	$ufname = "eDart";
	$ulname = "User";
	$domail = 1;

	//PART I: Write to the database
	$con = mysqli_connect(host(), username(), password(), mainDb());
	$q = "INSERT INTO notify(`usr`,`date`,`message`,`link`) VALUES('" . mysqli_real_escape_string($con, $userid) . "','" . mysqli_real_escape_string($con, time()) . "','" . mysqli_real_escape_string($con, $msg) . "','" . mysqli_real_escape_string($con, $link) . "')"; //Insert a new row into the author's notifications
	mysqli_query($con, $q); //Execute

	$user_call = new User(array("action"=>"get","id"=>$userid));
	$user_info = $user_call->run(true);
	if(count($user_info)!=0)
	{
		$user_info = $user_info[0];
		$umail  = $user_info["email"];
		$ufname = ucwords($user_info["fname"]);
		$ulname = ucwords($user_info["lname"]);
		$domail = $user_info["do_mail"];
	}

	$greetings = array("Just wanted to let you know that:<br><br> %s. <br><br>That is all. Have a good rest of your day!",
		"In case you didn't know: <br><br>%s<br><br> Better go check it out.",
		"We hope you're having a good day! Just thought you might you want to know:<br><br> %s. <br><br>That is all. Carry on!",
		"Don't mean to break your flow, but we just thought you might want to know that<br><br> %s. <br><br>If you get the chance, you can check it out back at eDart. For now, live long and prosper!",
		"Hope your day is going splendidly! Just thought we'd let you know that:<br><br> %s. <br><br>When you have the time, check it out on eDart. Cool. For now, bye.");

	$fullmsg = sprintf($greetings[rand(0, (count($greetings)-1))], $msg);

	if($subject==null)
	{
		$subject=$msg;
	}

	//PART II: Send them an email
	if($domail==1){
		sendMail($umail, $ufname, $ulname, $subject, $fullmsg, $link, "View on eDart");
	}
}

function sendMail($to, $fname, $lname, $subject, $msg, $link, $btnTxt){

$from = 'eDart <no-reply@wewanttotrade.com>';
$date = date("r");

if(strpos($link, "http://")===false) {
	$link = "http://wewanttotrade.com/" . $link;
}
$html  = "<html><head><title>$subject</title></head><body style=\"padding-bottom:10px;background:white;\"><style type=\"text/css\">* { margin:0px; padding:0px; }
		</style>";
$html .= "<div style=\"position:relative;overflow:hidden;max-height:60px;height:60px;border:0px;display:block;background:#2ECC71;border-bottom:1px solid #269F58;\">";
$html .= "<table><tr><td style=\"padding:10px;\">";
$html .= "<img style=\"position:absolute;z-index:2;width:150px;top:12px;left:10px;\" width=\"150\" height=\"44\" alt=\"eDart Logo\" src=\"http://wewanttotrade.com/img/logo.png\">";
$html .= "</td></tr></table>";
$html .= "</div>";
$html .= "<p style=\"color:#2b2b2b;font-size:14px;margin:0px;padding-top:10px;padding-left:25px;padding-right:25px;padding-bottom:5px;\">Hey $fname,<br><br>". $msg ."<br><br>Sincerely,<br>The eDart Team</p>";
$html .= "<p style=\"color:dimgray;font-size:14px;margin:0px;padding-top:0px;padding-left:25px;padding-right:25px;padding-bottom:10px;\">Click the button below to view this on eDart</p>";

$html .= "<table><tr><td style=\"width:50px;\"></td><td style=\"cursor:pointer;background:#93f580;padding:1px;height:50px;width:150px;\">";

$html .= "<div style=\"background-color:#2ecc71;text-decoration:none;width:150px;max-height:50px;min-height:50px;margin:0 auto;display:block;\">";
$html .= "<a href=\"".$link."\" style=\"text-decoration:none;\"> <div style=\"color:white;font-size:18px;text-decoration:none;text-align:center;width:100%;height:100%;padding-top:15px;padding-bottom:15px;\">$btnTxt</div></a>";
$html .= "</div>";

$html .= "</td></tr></table>";
$html .= "<p style=\"color:#808080;font-size:10px;margin:0px;padding-top:10px;padding-left:25px;padding-right:25px;padding-bottom:10px;\">By the way, you can disable these emails on your eDart control panel</p>";

$html .= "</body></html>";

//Now we do some PHPMailer stuff

$crl = curl_init();
$timeout = 5;

$post = array(
		"to"=>$to,
		"fname"=>$fname,
		"lname"=>$lname,
		"subject"=>$subject,
		"msg"=>$msg,
		"link"=>$link,
		"btnTxt"=>$btnTxt
	);

$post_string = "";
foreach($post as $k=>$v)
{
	$post_string .= $k . '=' . $v . '&';
}
rtrim($post_string, '&');

curl_setopt ($crl, CURLOPT_URL, "http://38.109.218.64/edart-support/sendmail.php");
curl_setopt ($crl, CURLOPT_HTTPHEADER, array("Host: prestonmueller.com"));
curl_setopt ($crl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt($crl, CURLOPT_POST, count($post));
curl_setopt($crl, CURLOPT_POSTFIELDS, $post_string);
curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
$ret = curl_exec($crl);

curl_close($crl);

/*
$mail = new PHPMailer();

$mail->isSMTP();
//$mail->Host = 'tls://email-smtp.us-west-2.amazonaws.com';
$mail->Host = '54.213.255.181';
$mail->Port = 25;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Username = 'AKIAJWC6X6ZPXAAL7C6A';
$mail->Password = 'Aou7Q/ARNkxiP3i4Jy7+elBjQZBU/5I6aP+wydcsr0ig';

$mail->Sender = 'noreply@wewanttotrade.com';
$mail->From = 'noreply@wewanttotrade.com';
$mail->FromName = "eDart";
$mail->addAddress($to, $fname . " " . $lname);  // Add a recipient
$mail->SMTPDebug = 2;
$mail->Subject = $subject;
$mail->Body    = $html;
$mail->AltBody = $msg;
$mail->SmtpClose();

$mail->XMailer = "";
$mail->Encoding = "base64";

if(!$mail->send()) {
	    $info = 'Failed to send the message!';
	}
	else{
	    $info = 'Message has been forwarded to sendmail.';}
	    echo $info;
*/
}
//sendMail("tjnickerson@live.com", "Tyler", 'Nickerson', "New eDart Notification", "Tyler Nickerson DJs like a mad cunt", "http://www.wewanttotrade.com/", "Click here");
?>
