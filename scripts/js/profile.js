function open_exchange(id)
{
	
}

function delete_item(id)
{
	$.get("/api/",
	{
		"lib":"item",
		"action":"delete",
		"id":id
	}, function() {
		window.location = document.URL.split("&")[0]  + "&alert=602";
	});
}

function open_item_edit(id)
{
	$.get("/api/",
		{
			"lib" : "item",
			"action" : "get",
			"filter" : {"id" : id}
		}, function(data) {
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

			$("#postbox").modal();
		});
}

$(".edit_button[data-id]").each(function() {
	$(this).click(function(e) {
		open_item_edit($(this).attr("data-id"));
	});
});

$(".delete_button[data-id]").each(function() {
	$(this).click(function(e) {
		delete_item($(this).attr("data-id"));
	});
});

function show_profile_tab(tab)
{
	$(".profile_tab_content").css("display","none");
	$(".profile_tab").removeClass("active");
	$(tab).addClass("active");
	$("#title").html($(tab).attr("value"));
	$("#"+$(tab).attr("data-link")).css("display","block");
}

$(".profile_tab, input[type='button']").each(function() {
	$(this).click(function() {
		show_profile_tab(this);
	})
});

function init_dob_picker()
{
	try
	{
		$('#user_dob').datetimepicker({ maskInput: true, pickTime: false });
	}catch(e) { }
}

function pp_change_begin()
{
	$("#user_image_upload").click();
}

function password_send_data()
{
	var current_password = $("#user_pw").val();
	var new_password	 = $("#user_npw").val();
	var new2_password 	 = $("#user_rpw").val();

	$.post("/scripts/php/ajax/me/change_password.php",
		{
			"current"  		  : current_password,
			"password" 		  : new_password,
			"repeat_password" : new2_password
		},
		function(data)
		{
			console.log(data);
		});
}

function user_send_data()
{
	var fname  = $("#user_fname").val();
	var lname  = $("#user_lname").val();
	var bio	   = $("#user_bio").val();

	var gender = $("#user_gender")[0].selectedIndex;
	var domail = $("#user_domail").is(":checked") ? 1 : 0;
	var dob    = Date.parse($("#user_dob").val()) / 1000;

	var fields = {
		"gender" 	: gender,
		"bio"		: bio,
		"dob"		: dob,
		"do_mail"	: domail,
		"fname"   	: fname,
		"lname"		: lname
	};

	$.get("/api/", { "lib" : "user", "action" : "update", "fields" : fields }, function(r){
		window.location = document.URL.split("&")[0] + "&alert=603";
	});
}

function privacy_send_data()
{
	var $privacy_checkboxes = $("#privacy_checkboxes").find("input");
	var array_contents = "";

	$privacy_checkboxes.each(function() {
		if(!$(this).is(":checked"))
		{
			array_contents += "\"" + $(this).attr("name") + "\",";
		}
	});

	array_contents = array_contents.substring(0,array_contents.length - 1);

	var json_string = "[" + array_contents + "]";

	$.get("/api/", { "lib" : "user", "action" : "update", "fields" : {"privacy" : json_string} }, function(r){
		window.location = document.URL.split("&")[0] + "&alert=604";
	});
}

addEvent(window, "load", function() { init_dob_picker(); });
