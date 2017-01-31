<?php

include 'header.php';
ini_set("allow_url_fopen", 1);

/* */
//$json = file_get_contents('csgo-skins.txt');
$arrSkins = json_decode($json);
foreach ($arrSkins->items AS $skin) {
	$name = $conn_market->real_escape_string($skin->market_name);
	$url = urlencode($name);
	$url = str_replace('+','%20',$url);
	if ((substr($url, -3) == '%29' || substr($url,0,9) != '%E2%98%85') && !strpos($name, 'Souvenir')) {  // These cases should separate out only non souvenir gun skins
		$arrClean[$url] = $name;
	} 
}
$arrNotEmpty = array();
$sql = "SELECT link FROM id_ref WHERE market_id IS NOT NULL AND 30day_price IS NOT NULL AND 30day_count IS NOT NULL";
$result = $conn_market->query($sql);
while($row = $result->fetch_assoc()) {
	$arrNotEmpty[] = $row['link'];
}

foreach ($arrClean AS $url => $name) {
	if (!in_array($url, $arrNotEmpty)) {
		$success = false;
		while ($success == false) {
			$ch = curl_init();
			$file = $GLOBALS['csgo_item_link'].$url;
			curl_setopt($ch, CURLOPT_URL, $file);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$store = curl_exec($ch);
			curl_close($ch);
			$doc = new DOMDocument();
			libxml_use_internal_errors(true);
			$doc->loadHTML($store);
			$text = $doc->textContent;
			if ($n = strpos($text,'var g_rgAppContextData')) {
				$a = substr($text,$n);
				$raw = explode('var ',$a);
				//print_r($raw);
				$avg = 0;
				foreach ($raw AS $var) {
					$expl = explode('=',$var);
					if (isset($expl[0]) && isset($expl[1])) {
						if ($decode = json_decode(rtrim(rtrim($expl[1]),';'))) {
							$jsScrape[$expl[0]] = $decode;
						} else {
							$jsScrape[$expl[0]] = $expl[1];
						}				
						if ($expl[0] == 'line1') {
							$history = explode('],[',substr($expl[1],2,(strpos($expl[1],']]')-2)));
							$price = $count = 0;
							foreach ($history AS $entry) {
								$array = explode(',',$entry);
								$date = substr($array[0],1,(strpos($array[0],':')-3));
								//echo $date.' '.$array[2];
								if (strtotime($date) >= strtotime('- 30 days')) {
									$c = trim($array[2],'"');
									$price += $array[1]*$c;
									$count += $c;
								}
							}
							$avg = '$'.number_format(round(($price/$count),2), 2, '.', ',');
						}
					}	
				}
				$n = strpos($text,'Market_LoadOrderSpread'); 
				$x = $n + 24;
				$y = (strpos($text,')',$n) - 1) - $x;
				$market_id = substr($text,$x, $y);
				$sql = "INSERT INTO id_ref (name, link, market_id, 30day_price, 30day_count, load_dt) VALUES ('$name', '$url', $market_id, '$avg', $count, CURDATE()) ON DUPLICATE KEY UPDATE link = VALUES (link), market_id = VALUES (market_id), 30day_price = VALUES (30day_price), 30day_count = VALUES (30day_count), load_dt = CURDATE()";
				if (!$conn_market->query($sql)) {
					echo $conn_market->error;
					die();
				}
				$success = true;
			} else {
				echo "Page failed to load! Retrying ... \n";
			}
			sleep(5);
		}
	}
}
//*/



//print_r($arrClean);

