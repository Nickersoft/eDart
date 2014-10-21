function open_editor(id)
{
	$.get("/api/",
			{
				"lib" : "item",
				"action" : "get",
				"filter" : {"id" : id}
			}, function(data) {
				var modal = $.UIkit.modal("#postbox");
				modal.show();
				
				var $item = eval('(' + data + ')');
				$item = $item[0];

				//BASIC INFO
				$("#wz_name").val($item["name"]);
				$("#wz_desc").val($item["description"]);
				$("#wz_title").html("Editing: " + $item["name"]);

				//CLASSIFICATIONS
				$("#wz_category")[0].selectedIndex  = (parseInt($item["category"]) - 1);
				$("#wz_condition")[0].selectedIndex = (parseInt($item["condition"]) - 1);

				//PICKUP LOCATION
				$("#wz_pickup option").each(function(){
					if($(this).html()==$item["stadd1"])
					{
						$(this).prop("selected", true);
					}
				});

				//EXPIRATION & DUE DATE
				var expiration_date = $("#wz_expiration").data('DateTimePicker');
				var expiration_ts   = new Date($item["expiration"] * 1000);
				expiration_date.setDate(expiration_ts);

				if(parseInt($item["duedate"])!=0)
				{
					var due_date = $("#wz_duedate").data('DateTimePicker');
					var due_ts   = new Date(parseInt($item["duedate"]) * 1000);
					due_date.setDate(new Date(due_ts));
				}

				$("#itemupload_id").val(id);

				//Forcibly enable "Next button"
				$("#add_next").prop("disabled", false);

			});
}

function delete_item(id)
{
	$.get("/api/",
	{
		"lib":"item",
		"action":"delete",
		"id":id
	}, function() {
		window.location = "/?alert=602";
	});
}

function confirm_delete(id)
{
	push_confirm("Are you sure you want to delete this item?", function() {
		delete_item(id);
	});
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