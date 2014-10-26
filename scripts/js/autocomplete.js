try {
var placeholder = document.getElementById('headsearch').getAttribute("placeholder");
var auto = completely(document.getElementById('headsearch'));

$.get("/api/", { "lib":"item", "action" : "get" }, function(data) {
	var json = eval("(" + data + ")");
	var item_array = [];
	
	for(var i = 0; i < json.length; i++)
	{
		item_array.push(json[i]["name"]);
	}

	auto.onChange = function (text) {
		if (text.length == 0) {
			auto.options = [];
			auto.repaint();
			auto.hint.value = placeholder;
			return; 
		}
		else {
			auto.options = item_array;
			auto.repaint();
			return;
		}
	};
		
	auto.options.sort();
});
}catch(e){alert(e);}