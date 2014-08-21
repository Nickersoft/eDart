//The following uses various javascript functions to resize elements on the page at runtime

var w = document.body.offsetWidth;
var h = document.body.offsetHeight;

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function showNotify(text)
{
	var notify = document.createElement("div");
	notify.id = "notification";
	notify.innerHTML = "<div id=\"inner\">"+text+"</div>";
	var close = document.createElement("div");
	close.className = "glyphicon glyphicon-remove";
	close.id = "close";
	close.onclick = function() { hideNotify(this.parentNode); }
	notify.appendChild(close);
	document.body.appendChild(notify);
}

function hideNotify(obj)
{
	obj.style.display = "none";
	document.cookie = "notify = off;"
}


function addEvent(target, eventName, handler) //Adds event listening based on browser
{
	try{
	if (target.addEventListener) {
	    target.addEventListener(eventName, handler, false);
	} else if (target.attachEvent) {
	    target.attachEvent("on" + eventName, handler);
	} else {
	    target["on" + eventName] = handler;
	}
	}catch(e){}
}

function resetSize()
{
	w = document.body.offsetWidth;
	h = document.body.offsetHeight;
}

function matchMain() //Resize main panel in /me to fill remaining space
{
	try{
		document.getElementById("main").style.width=(document.body.offsetWidth-450)+"px";
	}catch(e){}
}


	//Try to add event listening for these methods
	addEvent(window, "resize", function(e){resetSize();});
	addEvent(window, "resize", function(e){matchMain();});
	addEvent(window, "resize", function(e){resizeExchangeChat();});

	//Call required methods on page load
	addEvent(window, "load", function(e){matchMain();});
	addEvent(window, "load", function(e){resizeExchangeChat();});

//Now template the input boxes

var dimgray = "#696969";

function trim(str)
{

    var l = 0;
    while(l < str.length && str[l] == ' ')
    {
    	l++;
    }
    var leftTrim = str.substring(l, str.length);

    var r = str.length - 1;
    while(r > 0 && str[r] == ' ')
    {
		r--;
	}

    var rightTrim = leftTrim.substring(0, r+1);

    return rightTrim;
}

function curColor(obj)
{
	var color = "";
	if(obj.currentStyle)
		{
			color = obj.currentStyle.color;
		}
		else if(window.getComputedStyle)
		{
		    color = document.defaultView.getComputedStyle(obj, null).getPropertyValue("color");
		}
	return color;
}

function getComputedWidth(obj)
{
	var owidth = "";
	if(obj.currentStyle)
		{
			owidth = obj.currentStyle.height;
		}
		else if(window.getComputedStyle)
		{
		    owidth = document.defaultView.getComputedStyle(obj, null).getPropertyValue("width");
		}
	return parseInt(owidth.replace("px",""));
}

function getComputedHeight(obj)
{
	var oheight = "";
	if(obj.currentStyle)
		{
			oheight = obj.currentStyle.height;
		}
		else if(window.getComputedStyle)
		{
		    oheight = document.defaultView.getComputedStyle(obj, null).getPropertyValue("height");
		}
	return parseInt(oheight.replace("px",""));
}

function is_empty(textbox)
{
		var color = convert_to_hex($(textbox).css("color"));
		var case1 = (color==dimgray);
		var case2 = (trim(textbox.value)=="");

		return (case1)||(case2);
}

function resizeExchangeChat()
{
	try
	{
		document.getElementById("msgc").style.height = (h - 60 - 40 - getComputedHeight(document.getElementById("chtxt")) - getComputedHeight(document.getElementById("msgtxt")))+"px";
	}catch(e){}
}

function clear_incomplete(form)
{
	var form_arr = form.getElementsByTagName("input");
	for(var i = 0; i < form_arr.length; i++)
	{
		if(is_empty(form_arr[i]))
		{
			form_arr[i].value = "";
		}
	}

	var form_arr_t = form.getElementsByTagName("textarea");
	for(var i = 0; i < form_arr_t.length; i++)
	{
		if(is_empty(form_arr_t[i]))
		{
			form_arr_t[i].value = "";
		}
	}
}

function rang_submit(element, button)
{
	try
	{
		if(element.checked)
		{
			button.disabled = false;
		}
		else
		{
			button.disabled = true;
		}
	}
	catch(e)
	{

	}
}

function init_chosen()
{
    try {
        $(".chosen-select").each(function() {
            if($(this).hasClass("chosen-search"))
            {
                $(this).chosen({ inherit_select_classes: true });
            }
            else
            {
                $(this).chosen({ inherit_select_classes: true, disable_search: true });
            }
        });
    } catch(e) {}
}

/* * * LOADING PRESENTS * * */

	/*
	 *	  Name: pre_control_panel
	 * Purpose: Default methods to run on the /me page
	 * Returns: Void
	 */
	function pre_control_panel()
	{
		//Load the dates into the DOB selector
		loadDates();

		//
		try
		{
			//Use Google to load the current state the user resides in
			loadstate();
		}
		catch(e){}

		try
		{
			//Try to show the error box if there is an error
			$('#error_display').modal();
		}
		catch(e){}
	}

	/*
	 *	  Name: pre_home
	 * Purpose: Default methods to run on the home page
	 * Returns: Void
	 */
	function pre_home()
	{
		try
		{
			//Try to animate the background image
			$("main").addClass("background");
		}
		catch(e){alert(e);}

		try
		{
			//Display any queued error messages
			checkError();
		}catch(e){}
	}

	/*
	 *	  Name: pre_feed
	 * Purpose: Default methods to run on the feed page
	 * Returns: Void
	 */
	function pre_feed()
	{
		//If the user hasn't removed the notification
		if(getCookie("notify")!="off")
		{
			//Show it
			//showNotify("You can now view the changes we make to the website under 'Options / View Changelog'! Click the 'X' to resume.");
		}
	}

	function fade_loader()
	{
		$("#loader").fadeTo(150, 0, function() { $("#loader").css("display","none"); });
	}

	addEvent(window, "load", function() { $('input, textarea').placeholder(); });
	addEvent(window, "load", function() { fade_loader(); });
	addEvent(window, "load", function() { init_chosen(); });
