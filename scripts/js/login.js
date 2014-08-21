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

function return_login(e, login_button_id)
{
	if(e.keyCode == 13)
	{
		//console.log(document.getElementById(login_button_id));
		console.log(document.getElementById(login_button_id).click());
	}
}

function print_login_error()
{
	var login_errors = new Array('Oh bother... looks like your credentials are incorrect!', 
								 'Whoops... wrong credentials... try again?', 
								 'Are you sure you remember your credentials? Because they\'re incorrect.', 
								 'Oh man... can\'t log you in. Try again?', 
								 'Incorrect username or password... we\'d tell you which one, but that would just make it easier for hackers to hack us.', 
								 'Incorrect credentials (please don\'t hate us!)');
				
	var greeting = login_errors[Math.floor(Math.random()*(login_errors.length-1))+1];

	document.getElementById('cntlgnm').innerHTML = greeting;
}
