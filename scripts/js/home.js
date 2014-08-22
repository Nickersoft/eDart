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