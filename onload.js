$(document).ready(function() {
	getLowestPrice(arrPage);
});

function getBuyOrder(id) {
	var url = 'http://steamcommunity.com/market/itemordershistogram?country=US&language=english&currency=1&item_nameid='+id+'&two_factor=0';
	$.getJSON(url, function(data) {
		var items = [];
		$.each(data, function(key, val) {
			items[key] = val;
		});
	});
	console.log(items);
}

function getLowestPrice(arrPage) {
	var items = [];
	$.each(arrPage,function(key, val) {
		$.post('marketapi.php', {
			market_id: key,
			link: val
		}, function(data) {})
		.done(function(data) {
			console.log(data);
		});
	});	
	//console.log(items);
}
