/*
 *	    Name: get_items
 *   Purpose: Gets a list of items based on category name
 * Arguments:
 *		- 	  category: The category name
 *		- highlight_el: The object to highlight in the navigation
 *   Returns: Void
 */

var curcat = "";

/*
 *	    Name: post_item_request
 *   Purpose: Post an item request
 *   Returns: Void
 */
function post_item_request()
{
	$("#request_win .uk-modal-footer").fadeOut();
	$("#request_win .uk-modal-dialog").animate(
			{
				height :  ($("#request_win .uk-modal-dialog").height() - $("#request_win .uk-modal-footer").height())
			}, function() {
				$("#request_win .uk-modal-content").html("<img class=\"posting uk-align-center\" src=\"/img/add_loader.gif\">");

				$.get("/api/", { "lib" : "feed", "action" : "post-request", "name" : $("#item-request-name").val() }, function(data) {
				});

				setTimeout(function() {
					$("#request_win .uk-modal-title").html("Item requested!");
					$("#request_win .uk-modal-content").html("<h1 class=\"uk-text-center\" style=\"margin-top:60px;\">Thanks!</h1>");
				}, 1000);

				setTimeout(function() {
					var modal = $.UIkit.modal("#request_win");
					modal.hide();
				}, 2000);

			});
}

function populate_home(filter, menu_item, sort_title, limit)
{

	//Highlight the element
	$(menu_item).closest(".uk-nav").find("li").removeClass("uk-active");
	$(menu_item).closest("li").addClass("uk-active");
	$("#main_board").fadeOut(300, function() {
		$("#main_board").parent().addClass("uk-placeholder");
		
		//Make a call to the API to get a list of items matching the category
		$.get("/api/index.php",
			  { "lib" 		: "item",
			    "action" 	: "get",
			    "filter"	: filter,
			    "limit"		: limit,
			    "sort"		: sort_title, "order":"desc"} ,
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
						var offer_array = new Array();

						try
						{
							offer_array = eval("(" + a[i]["offers"] + ")");
						}catch(e){}

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

					    new_board += "<div class=\"uk-width-1-1\">" +
										"<div class=\"item\" onclick=\"window.location='/view.php?itemid=" + a[i]["id"] + "&userid=" + a[i]["usr"] + "';\">" +
											"<div class=\"uk-grid uk-grid-preserve reset_padding\">" +
												"<div class=\"uk-width-4-6 info\">" +
													"<div class=\"header\">" + a[i]["name"] + "</div>" +
														"<div class=\"description\">" + a[i]["description"] + "</div>" +
														"<div class=\"overview uk-grid\">" +
															"<div class=\"uk-width-1-3\" title=\"Number of Offers\">" +
																"<span class=\"uk-icon-cube\"></span> " + offer_array.length +
															"</div>" +
															"<div class=\"uk-width-1-3\" title=\"View Count\">" +
																"<span class=\"uk-icon-eye\"></span> " + a[i]["views"] +
															"</div>" +
															"<div class=\"uk-width-1-3\" title=\"Estimated Market Value (EMV)\">" +
																"<span class=\"uk-icon-usd\"></span> " + ((a[i]["emv"].length == 0) ? "0" : a[i]["emv"]) +
															"</div>" +
														"</div>" +
												"</div>" +
												"<div class=\" uk-width-2-6\">" +
													"<div style=\"background:url('/imageviewer/?id=" + a[i]["id"] + "&size=thumbnail') no-repeat center center;\" class=\"thumbnail\">" +
														"<div class=\"gradient\"></div>" +
													"</a>" +
												"</div>" +
											"</div>" +
										"</div>" +
									"</div>";
				}

					//Set the HTML
					document.getElementById("main_board").innerHTML = new_board;
					$("#main_board").parent().removeClass("uk-placeholder");
					
					$("#main_board").fadeIn(300, function() {align_items();});

			});
	});
}

function select_recent(menu_item)
{
	try
	{
		populate_home({}, menu_item, "adddate", 10);
	}
	catch(e) {}
}

function select_category(category_id, menu_item)
{
	try
	{
		//If the category is not currently selected...
		if(curcat!=category_id)
		{
			populate_home({"category" : category_id}, menu_item, "adddate", 0);
			curcat = category_id;
		}
	}
	catch(e) {}
}

function select_popular(menu_item)
{
	try
	{
		populate_home({}, menu_item, "views");
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

jQuery.fn.extend({
	  show_menu: function() {
	    return this.each(function() {
	    	$(this).show();
	    	var set_top = parseInt($(this).css("top").replace("px",""));

	    	$(this).css("opacity", "0");
	    	$(this).css("top", set_top - 10 + "px");
	    	$(this).animate({ top : set_top, opacity : 1 });
	    });
	  },
	  hide_menu: function() {
		    return this.each(function() {
		    	if($(this).is(":visible"))
		    	{
		    		var set_top = parseInt($(this).css("top").replace("px",""));

			    	$(this).css("top", set_top + "px");
			    	$(this).animate({ top : (set_top - 10), opacity : 0 }, function() {
				    	$(this).css("top", set_top);
				    	$(this).hide();
			    	});
		    	}
		    });
	  }
	});

function display_menu(menu, icon)
{
	$(".icon").css("border-bottom", "");
	$(".icon").removeClass("active");
	$(".menu_link").removeClass("active");
	$(".icon").find(".badge").show();

	if($(menu).is(":visible"))
	{
		if($(menu).hasClass("fade_load"))
		{
			$(menu).hide_menu();
		}
		else
		{
			$(menu).hide();
		}
		$(icon).find(".badge").show();
	}
	else
	{
		$(".menu").each(function() {
			if($(this).hasClass("fade_load"))
			{
				$(this).hide_menu();
			}
			else
			{
				$(this).hide();
			}
		});

		if($(menu).hasClass("fade_load"))
		{
			$(menu).show_menu();
		}
		else
		{
			$(menu).show();
		}
		$(icon).find(".badge").hide();
		$(icon).addClass("active");
	}

	$('html').unbind('click');
	$('html').click(function(e) {
   		if(!$(e.target).hasClass('menu')&&!$(e.target).hasClass('icon')&&!$(e.target).hasClass("menu_link"))
   		{
   			$(".menu").each(function() {
   				if($(this).hasClass("fade_load"))
   				{
   					$(this).hide_menu();
   				}
   				else
   				{
   					$(this).hide();
   				}
   			});
       		$(".icon .badge").show();
			$(".icon").removeClass("active");
			$(".menu_link").removeClass("active");
   		}
   		else
   		{
   			//console.log(e.target.className);
   		}
	});
}

function init_home()
{
	var speed = 10;
	var $home_cover = $("#home_cover[data-height]");
	var $motio = new Motio($home_cover[0],
			{
				speedY : (speed*-1),
				fps : 60
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

	//$motio.play();

	try
	{
		$("#category_select").change(function() {
			switch(this.selectedIndex)
			{
				case 0:
					select_recent(this);
					break;

				case 1:
					select_popular(this);
					break;

				default:
					select_category(this.options[this.selectedIndex].value, this);
					break;
			}

		});
	}
	catch(e){}
}

function align_items()
{
//	$(".thumbnail img").each(function() {
//		console.log($(this).height() < $(this).closest(".thumbnail").height());
//		if($(this).height() < $(this).closest(".thumbnail").height())
//		{
//			$(this).css("height","100%");
//			$(this).css("width", "auto");
//			console.log($(this).width());
//		}
//	});
}

(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
  	ref.parentNode.insertBefore(js, ref);
}(document));

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
				link: 'http://wewanttotrade.com/'
		});
	}
	catch(e)
	{
		FB.login();
	}
}

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

	$("#category_list li a").eq(0).click();
	$("#about_nav li a").eq(0).click();
});

addEvent(window, "load", function() { init_home(); });
