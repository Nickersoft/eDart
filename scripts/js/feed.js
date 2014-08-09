/*
 * Page Name: Feed
 * Purpose: Supporting JavaScript for the feed
 * Last Updated: 6/6/2014
 * Signature: Tyler Nickerson
 * Copyright 2014 eDart
 *
 * [Do not remove this header. One MUST be included at the start of every page/script]
 *
 */

//Currently selected category
var curcat = "";

/*
 *	  Name: display_load
 * Purpose: Display the loading icon in the feed window
 * Returns: Void
 */
function display_load()
{
	document.getElementById("postCont").innerHTML="<div id=\"waitload\">Please wait while we get the latest content...</div>";
}

/*
 *	  Name: adjust_feed
 * Purpose: Resize and animate the feed
 * Returns: Void
 */
function adjust_feed()
{
	$("#feed_center").animate({"height":$("#postCont").height()},
		function()
		{
			//Get all of the posts via a query selector
			$(".hidden").animate({opacity:1},"slow");
		}
	);
}

/*
 *	  Name: change_style
 * Purpose: Change the style of an object given a CSS style string
 * Returns: Void
 */
function change_style(obj, style)
{
	try
	{
		obj.setAttribute("style", style);
	}
	catch(e)
	{
		obj.style = style;
	}
}

/*
 *	    Name: get_items
 *   Purpose: Gets a list of items based on category name
 * Arguments:
 *		- 	  category: The category name
 *		- highlight_el: The object to highlight in the navigation
 *   Returns: Void
 */
function get_items(category_id, category_name, highlight_el)
{
	try
	{
		//If the category is not currently selected...
		if(curcat!=category_id)
		{
			//...display the loader
			display_load();

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

						//The new post HTML
						var newPost = "";

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

							//Put together the HTML
						    newPost += "<div class=\"post hidden\">" +
						    				"<div class=\"img\">" +
												"<img alt=\"Item Photo\" src=\"/imageviewer/?id=" + a[i]["id"] + "&size=small\" style=\"cursor:pointer;\" onclick=\"window.location='/view.php?itemid=" + a[i]["id"] + "&userid=" + a[i]["usr"] + "';\">" +
											"</div>" +
											"<div class=\"cocnt\">" +
												"<div style=\"cursor:pointer;\" onclick=\"window.location='/view.php?itemid=" + a[i]["id"] + "&userid=" + a[i]["usr"] + "';\" class=\"hdr\">" +
													a[i]["name"] +
												"</div>" +
												"<div class=\"txt\">" +
													description +
												"</div>" +
											"</div>" +
										"</div>";
						}

						//Set the HTML
						document.getElementById("postCont").innerHTML = newPost;

						adjust_feed();

				});

				//Highlight the element
				highlight_element(highlight_el);

				//Set the current category
				curcat = category_id;

				push_title(category_name);
		}
	}
	catch(e) {}
}

/*
 *	    Name: clear_lists
 *   Purpose: Deselects items in every list
 *   Returns: Void
 */
function clear_lists()
{
	//Deselect the 'all activity' link
	$("#all").removeClass("active");

	//Get all category bullets
	var bullets = document.getElementById("categories").getElementsByTagName("li");

	//Loop through all of them
	for(var i = 0; i<=bullets.length-1;i++)
	{
		$(bullets[i]).removeClass("active"); //Reset their color
	}
}

/*
 *	    Name: highlight_element
 *   Purpose: Highlight an element in the list and deselect all others
 * Arguments:
 *		- highlight_el: Element to highlight
 *   Returns: Void
 */
function highlight_element(highlight_el)
{
	//Deselect all links
	clear_lists();

	//Select the given element
	$(highlight_el).addClass("active");
}

/*
 *	    Name: get_recent_activity
 *   Purpose: Get recent activity for feed
 *   Returns: Void
 */
function get_recent_activity()
{
		try
		{
			display_load(); //Display the loader
			curcat="all";	//Change the category
			clear_lists();	//Deselect all links
			$("#all").addClass("active"); //Highlight the element

			//Make an API call and start the help
			$.get("/api/", { "lib" : "feed", "action" : "get" } , function(data)
			{
				if(data==parseInt(data))
				{
					data = "<p style='margin-top:0px;'>No content to display</p>";
				}
				document.getElementById("postCont").innerHTML = data;
				adjust_feed();
			});

			push_title("What's New");
		}
		catch(e){}
}

/*
 *	    Name: push_title
 *   Purpose: Display a new title for the feed
 *   Returns: Void
 */
function push_title(title)
{
		try
		{
			var first_title  = document.getElementById("inner_title");
			var parent_title = document.getElementById("feed_title");


			$("#inner_title").animate({marginTop : "-32px"}, 250, function()
					{
						first_title.innerHTML = title;
						first_title.style.marginTop = "32px";
						$("#inner_title").animate({marginTop : "0px"}, 250);
					}
				);
		}
		catch(e){alert(e);}
}

/*
 *	    Name: display_post_irequest
 *   Purpose: Displays panel for posting item requests
 *   Returns: Void
 */
function display_post_irequest()
{
	$("#request_list").fadeOut(function() {
		$("#request_post").fadeIn();
	});
}

/*
 *	    Name: display_list_irequest
 *   Purpose: Displays panel for listing item requests
 *   Returns: Void
 */
function display_list_irequest()
{
	$("#request_post").fadeOut(function() {
		$("#request_list").fadeIn();
	});
}

/*
 *	    Name: post_irequest
 *   Purpose: Post an item request
 *   Returns: Void
 */
function post_irequest()
{
	$.get("/api/", { "lib" : "feed", "action" : "post-request", "name" : $("#item-request-name").val() }, function(data) {
		$("#request_post").fadeOut(function() {
			$("#request_thanks").fadeIn(function() {
				$("#request_thanks").delay(2000).fadeOut(function() {
					$("#request_list").fadeIn();
				});
			});
		});
	});
}

try
{
	//Get recent activity on load
	addEvent(window, "load", function()
	{
		get_recent_activity();
	});

	//Allow static scrolling (so it sticks to top of page)
	addEvent(window, "scroll", function(e)
	{
		$("#feed #left").stick_in_parent(
			{
				offset_top : 70
			});
	});

}
catch(e){console.log(e);}

