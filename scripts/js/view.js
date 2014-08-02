//Show report item
function showReportItem()
{
	var abx = document.getElementById("alertbox");
	var ay  = document.getElementById("ayes");
	var an  = document.getElementById("ano");
	var atx = document.getElementById("alertinfo");
	abx.style.display="block";
	atx.innerHTML="Are you sure you would like to report this item for abuse?";
	ay.onclick=function(){abx.style.display="none";sendReport();return false};
	an.onclick=function(){abx.style.display="none";};
}

//Show the accept alert box
function acceptConfirm(itemid, name)
{
	var abx = document.getElementById("alertbox");
	var ay  = document.getElementById("ayes");
	var an  = document.getElementById("ano");
	var atx = document.getElementById("alertinfo");
	abx.style.display="block";
	atx.innerHTML="Are you sure you wish to accept the offer: " + name + "?";
	ay.onclick=function(){abx.style.display="none";acceptOffer(itemid);return false};
	an.onclick=function(){abx.style.display="none";};
}

//Show the withdraw alert box
function withdrawConfirm(itemid)
{
	var abx = document.getElementById("alertbox");
	var ay  = document.getElementById("ayes");
	var an  = document.getElementById("ano");
	var atx = document.getElementById("alertinfo");
	abx.style.display="block";
	atx.innerHTML="Are you sure you wish to withdraw your offer?";
	ay.onclick=function(){abx.style.display="none";withdrawOffer(itemid);return false};
	an.onclick=function(){abx.style.display="none";};
}

//Submit the withdrawl form
function withdrawOffer(itemid)
{
	document.getElementById("withdrawitem").value = itemid;
	document.getElementById("itemform").submit();
}

//Submit the acceptance form
function acceptOffer(itemid)
{
	document.getElementById("acceptitem").value = itemid;
	document.getElementById("itemform").submit();
}

//Send an item report
function sendReport()
{
	$.post("/scripts/php/writelog.php", { "url" : document.URL }, function() {
		document.getElementById("repbtn").style.color = "white";
		document.getElementById("repbtn").style.cursor="default";
		document.getElementById("repbtn").onclick=function(){};
		document.getElementById("repbtn").innerText="Report sent!";
		document.getElementById("repbtn").innerHTML="Report sent!";
	});
}

function showImg()
{
	document.getElementById("shadowbox").style.display="block";
	document.getElementById("dimit").style.display="block";

	matchCrop();
	addEvent(window, "resize", function(e){matchCrop();});

}

function hydeImg()
{
	document.getElementById("shadowbox").style.display="none";
	document.getElementById("dimit").style.display="none";
}
