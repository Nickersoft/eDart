/*
 *	    Name: get_items
 *   Purpose: Gets a list of items based on category name
 * Arguments:
 *		- 	  category: The category name
 *		- highlight_el: The object to highlight in the navigation
 *   Returns: Void
 */

var curcat = "";

function select_category(category_id, category_name, menu_item)
{
	try
	{
		//If the category is not currently selected...
		if(curcat!=category_id)
		{
			//...display the loader
			//display_load();

			//Make a call to the API to get a list of items matching the category
			$.get("/api/index.php",
				  { "lib" 		: "item",
				    "action" 	: "get",
				    "filter"	: { "category" : category_id },
				    "sort"		: "adddate", "order":"desc"} ,
				    function(data)
				    {
				    	//Get the data and convert it to a JSON array
						var a = eval("(" + data + ")");

						//The new board HTML
						var new_board = "";

						//Loop through the returned items
						for(var i = 0; i <= a.length - 1; i++)
						{
							//Set the description to a variable
							var description = a[i]["description"];

							//Unless it's empty
							if(description == "")
							{
								//Then create a default
								description ="No description available.";
							}

							var item_content = "";
							item_content += ((a[i]["emv"])!="") ? "Worth: $" + a[i]["emv"] + ".00<br/>" : "";
							item_content += ((a[i]["duedate"])!=0) ? "Due Date: " + moment(parseInt(a[i]["duedate"])).format("MMMM Do, YYYY") + "<br/>" : "";
							item_content += "Expires: " + moment(parseInt(a[i]["expiration"])).format("MMMM Do, YYYY") + "<br/><br/>";
							item_content += "Posted On: " + moment(parseInt(a[i]["adddate"])).format("MMMM Do, YYYY");
						    
						    new_board += "<div class=\"uk-width-1-5\">" + 
											"<div class=\"item\">" +
												"<div class=\"thumbnail\" style=\"background:url('/imageviewer/?id=" + a[i]["id"] + "&size=thumbnail' ) center center no-repeat;\">" + 
							 						"<div class=\"overlay\" onclick=\"window.location='/view.php?itemid=" + a[i]["id"] + "&userid=" + a[i]["usr"] + "';\">" +
							 							"<p>" + 
							 								item_content +
							 							"</p>" + 
													"</div>" +
												"</div>" + 
												"<div class=\"subtitle\">" + a[i]["name"] + "</div>" +
											"</div>" +
										"</div>";
						}

						//Set the HTML
						document.getElementById("main_board").innerHTML = new_board;

						//adjust_feed();

				});

				//Highlight the element
				$(menu_item).closest(".uk-nav").find("li").removeClass("uk-active");
				$(menu_item).closest("li").addClass("uk-active");

				//Set the current category
				curcat = category_id;

				//push_title(category_name);
		}
	}
	catch(e) {}
}

function show_panel(parent, id)
{
	try {
		$(parent + " .panel").hide();
		$(parent + " " + id).show();
	}catch(e){
		console.log(e);
	}
}

function display_menu(menu, icon)
{
	$(".icon").css("border-bottom", "");
	$(".icon").removeClass("active");
	$(".menu_link").removeClass("active");
	$(".icon").find(".badge").show();

	if($(menu).is(":visible"))
	{
		$(menu).hide();
		$(icon).find(".badge").show();
	}
	else
	{
		$(".menu").hide();
		$(menu).show();
		$(icon).find(".badge").hide();
		$(icon).addClass("active");
	}

	$('html').unbind('click');
	$('html').click(function(e) {
   		if(!$(e.target).hasClass('menu')&&!$(e.target).hasClass('icon')&&!$(e.target).hasClass("menu_link"))
   		{
       		$('.menu').hide();
       		$(".icon .badge").show();
			$(".icon").removeClass("active");
			$(".menu_link").removeClass("active");
   		}
   		else
   		{
   			console.log(e.target.className);
   		}
	});
}

function init_home()
{
	var speed = 40;
	var $home_cover = $("#home_cover[data-height]");
	var $motio = new Motio($home_cover[0],
			{
				speedY : (speed*-1), 
				fps : 30 
			});
	
	$motio.on("frame", function() { 
		var $bg_pos   = $home_cover.css("background-position");
		var $bg_pos_y = $bg_pos.split(" ")[1];
		$bg_pos_y 	  = parseInt($bg_pos_y.replace("px",""));
		
		if(($bg_pos_y*-1)==(parseInt($home_cover.attr("data-height")) - $home_cover.height()))
		{
			$motio.set("speedY",speed);
		}
		else if($bg_pos_y==0)
		{
			$motio.set("speedY",speed*-1);
		}
	});
}

window.fbAsyncInit = function() {
	FB.init({
	appId      : '1410963979147478',
	status     : true, // check login status
	cookie     : true, // enable cookies to allow the server to access the session
	xfbml      : true  // parse XFBML
		});
	}

function facebook_login()
{
	try
	{
		FB.ui({
				method: 'send',
				link: 'http://wewanttotrade.com/',
		});
	}
	catch(e)
	{
		FB.login();
	}
}

    (function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
	  	ref.parentNode.insertBefore(js, ref);
  }(document));

$(document).ready(function() {
	$(".panel").hide();
	$("ul[data-switcher-parent]").each(function() {
		var $parent = $(this).attr("data-switcher-parent");
		$(this).find("li a[data-switcher-id]").each(function() {
			$(this).click(function() {
				$(this).closest("ul").find("li").removeClass("uk-active");
				$(this).closest("li").addClass("uk-active");
				show_panel($parent, $(this).attr("data-switcher-id"));
			});
		});
	});
	$(".uk-nav li a").eq(0).click();
});

addEvent(window, "load", function() { init_home(); });