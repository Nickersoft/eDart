function start_upload()
{
	document.getElementById('wz_loader').style.display	= 'block';
	document.getElementById('overlay').style.display	= 'none';

	document.getElementById('itemupload_image_form').submit();
}

var moving = false;

function push_slide()
{
	if(!moving)
	{
		moving = true;
		$('#add_wizard').animate({
	          scrollLeft: $("#add_wizard").scrollLeft() + $(".add_slide").width()
	        }, 250, function()
	        		{
	        			check_buttons();
	        			moving = false;
	        		}
	    );
	}
}

function check_dodue()
{
	if($("#wz_dodue").is(":checked"))
	{
		$("#wz_duedate").data('DateTimePicker').enable();
	}
	else
	{
		$("#wz_duedate").data('DateTimePicker').disable();
	}
}

function pull_slide()
{
	if(!moving)
	{
		moving = true;
		$('#add_wizard').animate({
	          scrollLeft: $("#add_wizard").scrollLeft() - $("#add_wizard").width()
	        }, 250, function()
	       			 {
	        			check_buttons();
	        			moving = false;
	        		 }
	    );
	}
}

function activate_next()
{
	switch($("#add_next").attr("data-action"))
	{
		case "post":
			show_add_loader();
			submit_item();
			break;

		case "next":
			push_slide();
			break;
	}
}

function show_add_loader()
{
	$("#wz_title").html("Posting your item...");
	$("#postbox .modal-footer").css("display","none");
	$("#done").html("<img class=\"posting\" src=\"/img/add_loader.gif\">");
}

function check_buttons()
{
	try{
		if($("#add_wizard").scrollLeft()==0)
		{
			document.getElementById("add_back").disabled = true;
		}
		else
		{
			document.getElementById("add_back").disabled = false;
		}

		var scrollRight = $("#add_wizard")[0].scrollWidth - $("#add_wizard").scrollLeft();

		//A little funky, but this gets us to the second-to-last slide
		if(scrollRight==$(".add_slide").width())
		{
			var button_text = ($("#itemupload_id").val()=="") ? "Post" : "Update";
			document.getElementById("add_next").innerHTML = button_text;
			document.getElementById("add_next").setAttribute("data-action","post");
		}
		else
		{
			document.getElementById("add_next").innerHTML = "Next";
			document.getElementById("add_next").setAttribute("data-action","next");
		}

		var slide_count = $("#add_wizard").scrollLeft() / $("#add_wizard").width();
		$("#pacer .bullet").removeClass("active");
		$("#pacer .bullet").eq(slide_count).addClass("active");
	} catch(e) {}
}

function init_dtpickers()
{
	try
	{
		$('#wz_expiration').datetimepicker({ maskInput: true, pickSeconds: false });
		$('#wz_duedate').datetimepicker({ maskInput: true, pickSeconds: false });

		var expiration_date = $("#wz_expiration").data('DateTimePicker');
		expiration_date.setDate(expiration_date.getDate());

		var due_date = $("#wz_duedate").data('DateTimePicker');
		due_date.setDate(due_date.getDate());
	}catch(e){}

}

/*
 *    Name: set_info
 * Purpose: To update the item info in the review at the end of the wizard,
 *          as well as to assign the item data to its form variables
 * Returns: Void
 */
function set_info()
{
	//The item properties that appear at the end of the wizard (the overview)
	var disp_itemname 		= $("#disp_item_name");
	var disp_description 	= $("#disp_item_description");
	var disp_condition 		= $("#disp_item_condition");
	var disp_category 		= $("#disp_item_category");
	var disp_duedate		= $("#disp_item_duedate");
	var disp_expiration 	= $("#disp_item_expiration");
	var disp_pickup	 		= $("#disp_item_pickup");

	//The item properties that are hidden values in the form that will create the item
	var form_itemname		= $("#itemupload_name");
	var form_description	= $("#itemupload_description");
	var form_condition 		= $("#itemupload_condition");
	var form_category 		= $("#itemupload_category");
	var form_duedate 		= $("#itemupload_duedate");
	var form_expiration		= $("#itemupload_expiration");
	var form_pickup			= $("#itemupload_stadd1");

	//The actual values of the item properties
	var name 				= $("#wz_name").val();
	var description 		= $("#wz_desc").val();
	var condition 			= $("#wz_condition").val();
	var category 			= $("#wz_category").val();
	var pickup 				= $("#wz_pickup").val();

	var expiration 			= $("#wz_expiration").data('DateTimePicker').getDate().toString();
	var duedate 			= $("#wz_duedate").data('DateTimePicker').getDate().toString();

	var condition_ind 			= $("#wz_condition")[0].selectedIndex;
	var category_ind 			= $("#wz_category")[0].selectedIndex;

	//Parse the dates and use the timestamps
	var parsed_expiration = Date.parse(expiration) / 1000;
	var parsed_duedate    = Date.parse(duedate) / 1000;

	//If we didn't select a due date...
	if(!$("#wz_dodue").is(":checked"))
	{
		//Reset it
		duedate = "None";
		parsed_duedate = 0;
	}

	//Assign them respectively to the overview at the end of the wizard
	disp_itemname.html(name);
	disp_description.html(description);
	disp_condition.html(condition);
	disp_category.html(category);
	disp_pickup.html(pickup);

	//Get the dates from the date/time picker and add those to the overview as well
	disp_expiration.html(expiration);
	disp_duedate.html(duedate);

	//Assign the properties to the hidden inputs in the form
	form_itemname.val(name);
	form_description.val(description);
	form_condition.val(condition_ind);
	form_category.val(category_ind);
	form_pickup.val(pickup);
	form_expiration.val(parsed_expiration);
	form_duedate.val(parsed_duedate);

	if($.trim(description)=="")
	{
		disp_description.html("None");
	}

}

function reset_add_wizard()
{
	$("#add_wizard").scrollLeft(0);

	$("#wz_title[data-default]").html($("wz_title[data-default]").attr("data-default"));
	$("#wz_dodue").prop("checked", true);
	$("#wz_picture").css("background","");
	$("#add_wizard .uk-modal-footer").css("display","block");

	var inputs_and_textareas = $("#add_wizard").find("input, textarea");
	for(var i = 0; i < inputs_and_textareas.length; i++)
	{
		var node = inputs_and_textareas.eq(i);
		node.css("color","");
		node.val("");
	}

	var selectboxes = $("#add_wizard").find("select");
	for(var i = 0; i < selectboxes.length; i++)
	{
		var node = selectboxes.eq(i);
		node[0].selectedIndex = 0;
	}

	init_dtpickers();
	check_buttons();
	$("#add_next").prop("disabled", true);
}

function add_listener()
{
	wz_validate();
	set_info();
}

function init_overview_listener()
{
	//Get the two datepicker objects
	var expiration_obj		= $("#wz_expiration");
	var duedate_obj			= $("#wz_duedate");

	//Create a listener that will run this update every time the dt picker changes
	expiration_obj.on("dp.change", function() { add_listener(); });
	duedate_obj.on("dp.change", function() { add_listener(); });

	//Create an onchange listener for every other input so they can do the same
	$("#add_wizard").find("input, select").change(function() { add_listener(); });
	$("#add_wizard").find("input, select").prop("tabindex","-1");
	$("#wz_desc").change(function() { add_listener(); });
	$("#wz_desc").prop("tabindex","-1");
}

function pre_add()
{
	init_overview_listener();
	reset_add_wizard();

	$('#postbox').on('uk.modal.show', function (e) {
    	reset_add_wizard();
	});
}

function submit_item()
{
	console.log($("#itemupload_id").val());
	clear_incomplete(document.getElementById("add_wizard"));
	document.getElementById("submit_item_form").submit();
}

function wz_validate()
{
	var itemname = $("#wz_name")[0];
	var itemdesc = $("#wz_desc")[0];

	if(is_empty(itemname)||is_empty(itemdesc))
	{
		document.getElementById("add_next").disabled = true;
	}
	else
	{
		document.getElementById("add_next").disabled = false;
	}
}

addEvent(window, "load", function() { pre_add(); });
