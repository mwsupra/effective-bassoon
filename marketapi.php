<?php
include('header.php');
if (isset($_POST['link']) && isset($_POST['market_id']) && isset($_POST['type'])) {
	$link = $_POST['link'];
	$id = $_POST['market_id'];
	$type = $_POST['type'];
	if ($type == 'lowest') {
		try {
			$lowest = $actions->getLowestPrice($link);
			$arrReturn['market_id'] = $id;
			if (isset($lowest['lowest_price'])) {
				$arrReturn['lowest_price'] = $lowest['lowest_price'];
			} else {
				$arrReturn['lowest_price'] = ' ';
			}
			echo json_encode($arrReturn);
		} catch (Exception $e) { }
	} elseif ($type == 'bo') {
		try {
			$bo = $actions->getBuyOrders($id);
			$arrReturn['market_id'] = $id;
			if (isset($bo['highest_buy_order'])) {
				$bo = $bo['highest_buy_order'];
				$bo = '$'.substr_replace($bo,'.',-2,0);
				$arrReturn['highest_buy_order'] = $bo;
			} else {
				$arrReturn['highest_buy_order'] = ' ';
			}
			echo json_encode($arrReturn);
		} catch (Exception $e) { }
	}
	
}

//
if (isset($_GET['link']) && isset($_GET['market_id'])) {
	$link = $_GET['link'];
	$id = $_GET['market_id'];
	$lowest = $actions->getLowestPrice($link);
	// $opts = array(
				// 'http'=>array(
					// 'method'=>"GET",
					// 'header'=>"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n" .
							// "Accept-Encoding: gzip, deflate, sdch\r\n" .
							// "Accept-Language: en-US,en;q=0.8\r\n" .
							// "Cache-Control: max-age=0\r\n" .
							// "Connection: keep-alive\r\n" .
							// "Cookie:sessionid=32acffc7cf49ac3a0c19b600; __utma=268881843.606518315.1429848623.1461608224.1461698816.216; __utmc=268881843; rgDiscussionPrefs=%7B%22cTopicRepliesPerPage%22%3A30%7D; steamCountry=US%7C928ff5f637c832354a62bbf07d27f23f; youtube_accesstoken=%7B%22access_token%22%3A%22ya29.GlziA6Jz6fFukCApZrs8HsD5geWp1kW9hz-SNFXvJe86yzc7PlLp1FKSm5PUivDb_RN2DS_DuRihb0UBxoOw25bM8Qgj2K5RGiOLfBgSrKE7Qxaw6ZnhudW631YFYA%22%2C%22expires_in%22%3A3599%2C%22token_type%22%3A%22Bearer%22%2C%22created%22%3A1485712303%7D; youtube_authaccount=Mitch+Wilson; 730_17workshopQueueTime=1485712347; 765_6workshopQueueTime=1485712415; webTradeEligibility=%7B%22allowed%22%3A1%2C%22allowed_at_time%22%3A0%2C%22steamguard_required_days%22%3A15%2C%22sales_this_year%22%3A123%2C%22max_sales_per_year%22%3A-1%2C%22forms_requested%22%3A0%2C%22new_device_cooldown_days%22%3A7%7D; steamRememberLogin=76561197967283008%7C%7C1045da031855a497de34fd506d999c5e; steamparental=1486315064%7C%7CwROaitaVBAU7P8pbnGzPccMj5pdMFSCtydqA4vcWxkY0ZE6lOEf5GBzfiQetYD4%2B; recentlyVisitedAppHubs=49520%2C377160%2C219740%2C105600%2C353370%2C108600%2C203160%2C8930%2C251570%2C447040%2C211820%2C221680%2C305620%2C730%2C346110; strInventoryLastContext=730_2; timezoneOffset=-18000,0; _ga=GA1.2.606518315.1429848623; steamLogin=76561197967283008%7C%7C59C5D9C7BDC4F00DB938352BE8962210DE6B83DC\r\n" .
							// "Host: steamcommunity.com\r\n" .
							// "If-Modified-Since: Mon, 06 Feb 2017 19:51:00 GMT\r\n" .
							// "Upgrade-Insecure-Requests: 1\r\n" .
							// "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36\r\n"
						// )
			// );

// $context = stream_context_create($opts);
	//echo $data;

	$success = false;
	while ($success == false) {
		try {
			$lowest = $actions->getLowestPrice($link);
			if (is_array($lowest)) {
				echo json_encode($lowest);
				$success = true;
				// $json = json_decode($data);
				// $array['market_id'] = $id;
				// $array['link'] = $link;
				// $array['lowest_price'] = $json->lowest_price;
				// echo json_encode($array);
			}		
		} catch (Exception $e) { }
	}
}