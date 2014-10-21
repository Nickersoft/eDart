function push_alert(code)
{
	$.post("/scripts/php/ajax/home/get_alert.php", { "code" : code }, function(text) {
		toastr.success(text);
	});
}

function push_error(code) {
	$.post("/scripts/php/ajax/home/get_error.php", { "code" : code }, function(text) {
		toastr.error(text);
	});
}

function init_alert()
{
	var $code = $.url(document.URL).param("alert");
	if($code&&!isNaN($code))
	{
		push_alert($code);
	}
}

function init_error()
{
	var $code = $.url(document.URL).param("error");
	if($code&&!isNaN($code))
	{
		push_error($code);
	}
}

function push_confirm(message, yes_callback, no_callback)
{
    vex.defaultOptions.className = 'vex-theme-default';
    vex.dialog.buttons.YES.text = "Yes";
    vex.dialog.buttons.NO.text = "No";
	vex.dialog.confirm({
		  message: message,
		  callback: function(value) {
			  if(value)
			  {
				  yes_callback(); 
			  }
			  else
			  {
				  if(no_callback)
					 {
					  	no_callback();
					 }
			  }
		  }
		});	
}

addEvent(window, "load", function() { init_alert(); });
addEvent(window, "load", function() { init_error(); });
