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
	if(($code)&&($code.length !== +$code.length))
	{
		push_alert($code);
	}
}

function init_error()
{
	var $code = $.url(document.URL).param("error");
	if(($code)&&($code.length !== +$code.length))
	{
		push_error($code);
	}
}

addEvent(window, "load", function() { init_alert(); });
addEvent(window, "load", function() { init_error(); });
