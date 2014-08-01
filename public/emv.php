<!DOCTYPE html>
<html>
	<head>
		<title>How We Calculate Item Worth</title>
	</head>

	<body>
		<h1>About Estimated Market Value (EMV)</h1>

		<p style="width:50%;">
			Here at eDart, we have something special. And that something special is called Estimated Market Value, or EMV for short. 
			EMV is our way of calculating the worth of an item, so if it gets busted, the owner can be compensated an amount that reflects
			the worth of their item. Keep in mind, EMV isn't always 100% accurate, but we try.</br>We calculate the EMV using the eBay Finder API. All that means, is we connect to eBay to estimate how much items are worth. 
			Although, we're not stupid. We know eBay is for bidding and sometimes items are sold for less than they are worth. That's why 
			when we search for a price, we run a search where "Buy It Now" is enabled for the item, and the item's condition is marked as "New".
			Like we said, not perfect, but it's a start.
		</p>

		<div style="cursor:pointer;" onclick="window.close();">
			Close this window
		</div>
		
	</body>
</html>