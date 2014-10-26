try {
var auto = completely(document.getElementById('headsearch'));
$.get("/api/", { "lib":"item", "action" : "get" }, function(data) {
	var json = eval("(" + data + ")");
	var item_array = [];
	
	for(var i = 0; i < json.length; i++)
	{
		item_array.push(json[i]["name"]);
	}
	auto.options = item_array;
});
}catch(e){}