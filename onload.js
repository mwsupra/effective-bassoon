$(document).ready(function() {
	getBuyOrders(arrPage);
	getLowestPrice(arrPage);
	//cancelBuyOrder('980369943');
});

function getBuyOrders(arrPage) {
	var items = [];
	$.each(arrPage,function(key, val) {
		$.post('marketapi.php', {
			market_id: key,
			link: val,
			type: 'bo'
		}, function(data) {})
		.done(function(data) {
			var output = $.parseJSON(data);
			id = output['market_id'];
			$("#bo-"+id).text(output['highest_buy_order']);
			updateDiff(id);
		});
	});
}

function getLowestPrice(arrPage) {
	var items = [];
	$.each(arrPage,function(key, val) {
		$.post('marketapi.php', {
			market_id: key,
			link: val,
			type: 'lowest'
		}, function(data) {})
		.done(function(data) {
			var output = $.parseJSON(data);
			var id = output['market_id'];
			var lowest = output['lowest_price'];
			$("#low-"+id).text(lowest);
			updateDiff(id);
			//console.log(data);
		});
	});	
	//console.log(items);
}

function missingLowest() {
	var missing = [];
	$('.lowest').each(function(i, obj) {
		if ($(this).text() == ' ') {
			var id = $(this).attr('id');
			var id = id.substring(4);
			var link = $('#'+id).text();
			missing.push({'market_id': id, 'link': link});	
		}
	});
	//console.log(missing);
	
	$.each(missing,function(key,val) {
		$.post('marketapi.php', {
			market_id: val['market_id'],
			link: val['link'],
			type: 'lowest'
		}, function(data) {})
		.done(function(data) {
			var output = $.parseJSON(data);
			var id = output['market_id'];
			var lowest = output['lowest_price'];
			$("#low-"+id).text(lowest);
			updateDiff(id);
			//console.log(data);
		});
	});
}

function updateDiff(id) {
	var bo = $('#bo-'+id).text();
	var lowest = $('#low-'+id).text();
	if (bo != ' ' && lowest != ' ') {
		bo = bo.replace(/\D/g,'');
		lowest = lowest.replace(/\D/g,'');
		lowest = lowest*0.87;
		diff = lowest - bo;
		diff = Math.round(diff);
		if (diff > 0) {
			sign = '+';
		} else if (diff < 0) {
			sign = '-';
		} else {
			sign = '';
		}
		diff = Math.abs(diff);
		dollars = Math.floor(diff/100);
		cents = diff - (dollars*100);
		if (cents < 10) {
			cents = '0'+cents;
		}
		diff = sign+' $'+dollars+'.'+cents;
		$('#diff-'+id).text(diff);
	}
}

function cancelBuyOrder(id) {
	var sessionID = '32acffc7cf49ac3a0c19b600';
	document.domain = 'steamcommunity.com';
	new Ajax.Request('http://steamcommunity.com/market/cancelbuyorder/', {
                method: 'post',
				headers: {
					"Origin":"steamcommunity.com"
				},
                parameters: {
                    sessionid: sessionID,
                    buy_orderid: id
                },
                onSuccess: function (transport) {
                    console.log('success');
                },
                onFailure: function (transport) {
                    console.log('failed');
                }
            });
}