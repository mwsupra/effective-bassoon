<?php

include 'header.php';
ini_set("allow_url_fopen", 1);

$active = true;
//$active = false;

if ($active == true) {
	$arrSkins = $actions->getInterested();
	foreach ($arrSkins AS $skin) {
		$arrClean[$skin['link']] = $skin['name'];
	}
	$arrNotEmpty = array ();
} else {
	$arrSkins = $actions->getSkinList();
	$arrClean = array();
	foreach ($arrSkins->items AS $skin) {
		$name = $actions->functions->str_clean($skin->market_name);
		$url = urlencode($name);
		$url = str_replace('+','%20',$url);
		if ((substr($url, -3) == '%29' || substr($url,0,9) == '%E2%98%85') && strpos($name, 'Souvenir') != 0 && !strpos($url,'Holo%2FFoil') && !strpos($name,'Graffiti')) {  // These cases should separate out only non souvenir gun skins
			$arrClean[$url] = $name;
		} 
	}

	$arrNotEmpty = $actions->getExcluded();
}

/* */


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
				$price = $count = $avg = 0;
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
							if ($count != 0) {
								$avg = number_format(round(($price/$count),2), 2, '.', ',');
							}					
						}
					}	
				}
				//echo $text;
				if ($n = strpos($text,'Market_LoadOrderSpread')) {
					$x = $n + 24;
					$y = (strpos($text,')',$n) - 1) - $x;
					$market_id = substr($text,$x, $y);
				} else {
					$market_id = 0;
				}
				
				//echo $market_id;
				$insert = array('link'=>"'$url'", 'name'=>"'$name'", 'market_id'=>"'$market_id'", '30day_price'=>$avg, '30day_count'=>$count, 'active'=>0, 'load_dt'=>'CURDATE()');
				if ($actions->singleInsertUpdate($GLOBALS['listTable'],$insert)) {
					echo "$name skin successfully inserted or updated!\n";
					$success = true;
				}				
			} else {
				echo "Page failed to load! Retrying ... \n";
			}
			sleep(11);
		}
	}
}
//*/



//print_r($arrClean);

