/*************************************************
	
	   		 Name: Exchange Scripting
		  Purpose: Enables core functionalities in exchange.php
	Last Modified: 4/8/2014

	Copyright 2014 eDart

*************************************************/

//Get the current offer ID
var url_q 	= document.location.search;
var q_split = url_q.split("=");
var offerid = q_split[q_split.length-1]; 

var ajaxListener;
var ratings = Array('Utterly disgraceful!', 'Horrible!', 'Pretty bad', 'Mediocre', 'Average', 'Alright, I guess...', 'Pretty cool', 'Good', 'Great', 'Absolutely amazing!');

var override  = false;

//Return all date/time cells on the current page
//This is denoted by the class containing 'timecell'
function getCells()
{
	var cells = document.getElementsByTagName("td");
	var return_cells = [];
	for(var i = 0; i < cells.length; i++)
	{
		if(cells[i].className.indexOf("timecell")!=-1)
		{
			return_cells.push(cells[i]);
		}
	}
	return return_cells;
}

//Initializes the ability to pull/push dates from/to server
function initialize_cell_data()
{
	var cells = getCells();
	for(var i = 0; i < cells.length; i++)
	{
		cells[i].onclick = 
			function()
			{
				var retrn = push_date(this.getAttribute("data-timestamp"));
				
				if(retrn==500)
				{
					showSetDate(this.getAttribute("data-timestamp"));
				}
				else if(this.className.indexOf("bright")!=-1)
				{
					this.className = "timecell";
				}
				else
				{
					this.className += " bright";
				}
			}
	}
}

function push_date(timestamp)
{
	var ret = null;
	$.ajax({
		type: "GET",
		url: "/api/", 
		data: { "lib" : "exchange", "action" : "push", "id" : offerid, "timestamp" : timestamp },
		success: function(data){ ret = data; },
		async: false
	});

	return ret;
}

function set_date(timestamp)
{
	var ret = null;
	$.ajax({
		type: "GET",
		url: "/api/", 
		data: { "lib" : "exchange", "action" : "set", "id" : offerid, "timestamp" : timestamp },
		success: function(data){ ret = data; location.reload(); },
		async: false
	});

	return ret;
}

function showSetDate(timestamp)
{
	var abx = document.getElementById("alertbox");
	var ay  = document.getElementById("ayes");
	var an  = document.getElementById("ano");
	var atx = document.getElementById("alertinfo");
	abx.style.display="block";
	atx.innerHTML="<div style=\"font-size:18px;\">It appears "+fname+" is also available at this time! Would you like to set this as your meeting date?</div>";
	ay.onclick=function(){abx.style.display="none";set_date(timestamp)};
	an.onclick=function(){abx.style.display="none";};
}

function message_send(message)
{
	$.get("/api/", { "lib" : "exchange", "action":"send", "id" : offerid, "message" : message }, function(data) { document.getElementById("msgtxt").value=""; } );
}

function message_listen()
{
	$.ajax({
		type: "POST",
		url: "/scripts/php/ajax/exchange/listen.php", 
		data: { "id" : offerid },
		cache: false,
		async:true,
		success:
			function(data)
			{ 
				try{
					var arr = eval('(' + data + ')');
					load_messages(arr);
				}catch(e){}
			
				setTimeout("message_listen()", 1000);
				
			},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			if(!override){
				setTimeout("message_listen()", 1000);
			}
		},
	});
}

//Allows functionality in the rater
function initialize_rank_table()
{
	try //Catch any errors
	{
		//Get all bars (tables) used for ranking (should be 4)
		var bars = document.getElementById("ratetable").getElementsByTagName("table");
	
		//Loop through each bar
		for(var i = 0; i < bars.length; i++)
		{
			var cells = bars[i].getElementsByTagName("td"); //Get the cells in each bar
			for(var j = 0; j < cells.length; j++) //Loop through the cells
			{
				cells[j].className = "bar highlighted"; //Highlight them all initially

				//Add mouseover function
				cells[j].onmouseover = function(){
					var parentTable = $(this).closest(".ranktbl")[0]; //Get parent table
					var child_cells = parentTable.getElementsByTagName("td"); //Get other cells in the table

					var rank_row  = $(this).closest(".rankrow").get(0); //Get master table
					var rank_cells = rank_row.getElementsByTagName("td"); //Get all cells in master table

					//Look for the dedicated label for that row
					for(var x = 0; x < rank_cells.length; x++)
					{
						if(rank_cells[x].id=="rtstat") 
						{
							//Set the label to the corresponding string representation of the rating
							rank_cells[x].innerHTML = ratings[parseInt(this.getAttribute("data-index"))];
						}
					}
							
					//Loop through all child cells
					for(var h = 0; h < child_cells.length; h++)
					{
						//If its index is less than that of the currently selected cell, highlight it
						if(parseInt(child_cells[h].getAttribute("data-index"))<=this.getAttribute("data-index"))
						{
							child_cells[h].className="bar highlighted";
						}
						else
						{
							//Otherwise, don't.
							child_cells[h].className="bar";
						}
					}

					var input_search = $(this).closest(".ranktbl")[0];
					input_search = input_search.getElementsByTagName("input");
					for(var a = 0; a < input_search.length; a++)
					{
						if(input_search[a].id=="rank_val")
						{
							input_search[a].value = this.getAttribute("data-index");
						}
					}
				}
			}
		}
	}
	catch(e)
	{

	}
}

var len;

//json is a stringz
function load_messages(json)
{
	var ms_arr = json;
	
	for(var i = len; i <=  ms_arr.length - 1; i++)
	{
		var msg = ms_arr[i];

		var new_msg = document.createElement("div");
		new_msg.className = "msg";

		var img_wrap = document.createElement("div");
		img_wrap.className = "img_wrap";

		var prof_pic = document.createElement("img");
		prof_pic.className = "pic";

		var msg_content = document.createElement("div");
		msg_content.className = "holder";

		var msg_title = document.createElement("div");
		msg_title.className = "title";

		var msg_body = document.createElement("div");
		msg_body.className = "body";

		var msg_date = document.createElement("div");
		msg_date.className = "date";

		var msg_inner = document.createElement("div");
		msg_inner.className = "inner";

		$.ajax({
			type: "GET",
			url: "/api/", 
			data: { "lib" : "user", "action" : "get", "id" : msg["user"] },
			async:false,
			success:
				function(data)
				{ 
					data = eval("("+data+")");
					msg_title.innerHTML = data[0]["fname"] + " " + data[0]["lname"];
					msg_title.onclick   = function() { window.location="/profile.php?id=" + data[0]["id"]; };
					prof_pic.src = "/profile.php?id=" + data[0]["id"] + "&load=image&size=small";
				}
		});

		msg_body.innerHTML = msg["message"];
		
		$.ajax({
			type: "POST",
			url: "/scripts/php/method/general/relative_date.php", 
			data: { "d1" : (new Date().getTime()/1000), "d2" : msg["timestamp"] },
			async:false,
			success:
				function(data)
				{ 
					msg_date.innerHTML = data + " ago";
				}
		});


		img_wrap.appendChild(prof_pic);

		msg_content.appendChild(msg_title);
		msg_content.appendChild(msg_body);

		msg_inner.appendChild(img_wrap);
		msg_inner.appendChild(msg_content);
		msg_inner.appendChild(msg_date);

		new_msg.appendChild(msg_inner);

		var cur_msgs = document.getElementById("msgc").getElementsByTagName("div");
		document.getElementById("msgc").insertBefore(new_msg, cur_msgs[0]);
	}

	len = ms_arr.length;
}

function show_rank_thanks()
{
	var alert_box  = document.getElementById("alertbox");
	var alert_body = document.getElementById("alertinfo");
	var alert_yes  = document.getElementById("ayes");
	var alert_no   = document.getElementById("ano");
	var rank_form  = document.getElementById("rate_form");

	alert_body.innerHTML 	= "We are glad you are taking the time to rate your fellow user. Keep in mind, submitting this review will permanantly close all access to this page, however, you will still be able to view the history of this exchange under your profile. Do you wish to continue?"; 
	alert_box.style.display = "block";
	alert_no.onclick 		= function() { alert_box.style.display = "none"; }
	alert_yes.onclick 		= function() { rank_form.submit(); }
}
	initialize_cell_data();
	initialize_rank_table();

	try
	{
		document.getElementById("map-txt").onmouseover = function() { document.getElementById("map-overlay").style.display="block"; };
		document.getElementById("map-txt").onmouseout  = function() { document.getElementById("map-overlay").style.display="none"; };
	}catch(e){}

//GOOGLE CODE FROM HERE ON OUT
//DO NOT TOUCH

var map;
function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var mapOptions = {
      zoom: 1,
      center: latlng,
	  mapTypeId: google.maps.MapTypeId.HYBRID
    }
    
	map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
}

function codeAddress(address) {
	geocoder.geocode( { 'address': address}, function(results, status) {
	  if (status == google.maps.GeocoderStatus.OK) {
	    map.setCenter(results[0].geometry.location);
	    map.setZoom(16);
	    var marker = new google.maps.Marker({
	        map: map,
	        position: results[0].geometry.location
	    });
	  } else {}
	});
}

try{
	initialize();
	google.maps.event.trigger(map, 'resize');
}catch(e){}
