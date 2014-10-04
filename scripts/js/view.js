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
function confirm_accept(itemid, name)
{
	push_confirm("Are you sure you wish to accept the offer: " + name + "?",
			function()
			{
				offer_accept(itemid);
			});
}

//Show the withdraw alert box
function confirm_withdraw(itemid)
{
	push_confirm("Are you sure you wish to withdraw your offer?",
			function()
			{
				offer_withdraw(itemid);
			});
}

//Submit the withdrawl form
function offer_withdraw(itemid)
{
	$("#withdraw_item").val(itemid);
	$("#po_form").submit();
}

//Submit the acceptance form
function offer_accept(itemid)
{
	$("#accept_item").val(itemid);
	$("#po_form").submit();
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

$(document).ready(function() {
	$("#offerbox.chosen-select").change(function() {
		$("#po_form").submit();
	});
});