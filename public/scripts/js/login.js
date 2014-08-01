function login(email, password, loginDestination, onFailCallback)
{
	//Make a login API call
	$.get("/api/", { "lib" : "login", "action" : "login", "email" : email, "password" : password }, 
	function(data)
	{
	 	switch(parseInt(data))
		{
			case 100:
				window.location = "/" + loginDestination;
				break;
			case 101:
				window.location = "/signup/email_sent.php";
				break;
			case 102:
				window.location = "/terms.php";
				break;
			default:
				onFailCallback();
				return 0;
		}
	}
	);
}

function logout()
{
	//Make a login API call
	$.get("/api/", { "lib" : "login", "action" : "logout" }, 
	function(data)
	{
		window.location = "/";
	}
	);
}
