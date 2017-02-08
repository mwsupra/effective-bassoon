<?php

class Functions {
	protected $marketdb;
	
	function __construct() {
		$this->marketdb = new MarketDB();
	}
	
	public function str_clean($str) { 
		return $this->marketdb->conn->real_escape_string($str);
	}
	
	public function singleInsertUpdate($table,$array) {
		if (is_array($array)) {
			$dup = array();
			$keys = array_keys($array);
			$sql = "INSERT INTO `$table` (".implode(',',$keys).") VALUES (".implode(',',$array).") ON DUPLICATE KEY UPDATE ";
			foreach ($keys as $key) {
				$dup[] = "$key = VALUES ($key)";
			}
			$sql .= implode(', ',$dup);
			
			if (!($this->functions->marketdb->conn->query($sql))) { 
				$this->db_error($sql);
			} else { 
				//$this->test($this->functions->marketdb->conn->affected_rows);
				return true;
			}
		} else { $this->db_error(); }
	}
	
	protected function query($sql) {
		if ($result = $this->functions->marketdb->conn->query($sql)) {
			return $result;
		} else { $this->db_error($sql); }
	}
	
	protected function fetch_all($result) {
		$array = array();
		if (isset($result->num_rows) && $result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$array[] = $row;
			}
		}
		return $array;
	}
	
	public function test($var = '') {
		if ($var != '') {
			if (is_array($var) || is_object($var)) {
				print_r($var);
			} else {
				echo $var;
			}
		}
		echo "\nTesting\n";
		exit;
	}
	
	private function db_error($sql='') {
		echo 'Error in query: '.$this->functions->marketdb->conn->error."\n\n$sql";
		exit;
	}
	
	public function cURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

class Actions extends Functions {
	public $functions;
	public $arrTracked;
	
	function __construct() {
		$this->functions = new Functions();
		//$this->arrTracked = $this->getTracked();
		//$this->updateInterested();
	}
	public function getSkinList() {
		return json_decode(file_get_contents($this->functions->marketdb->api));
	}
	public function getBuyOrders($id) {
		//return json_decode(file_get_contents("http://steamcommunity.com/market/itemordershistogram?country=US&language=english&currency=1&item_nameid=$id&two_factor=0"));
		try {
            $url = "http://steamcommunity.com/market/itemordershistogram?country=US&language=english&currency=1&item_nameid=$id&two_factor=0";
			$response = $this->functions->cURL($url);
            $json = json_decode($response, true);
            if (isset($json['success']) && $json['success']) {
                return $json;
            } else {  }
        } catch (\Exception $ex) {  }
	}
	public function getLowestPrice($link) {
		try {
            $url = "http://steamcommunity.com/market/priceoverview/?appid=730&county=US&currency=1&market_hash_name=$link";
			$response = $this->functions->cURL($url);
            $json = json_decode($response, true);
            if (isset($json['success']) && $json['success']) {
                return $json;
            } else {  }
        } catch (\Exception $ex) {  }
	}
	public function getInterested() {
		$sql = "SELECT * FROM `$GLOBALS[listTable]` WHERE `30day_price` >= $GLOBALS[lowPrice] AND `30day_price` <= $GLOBALS[highPrice] ORDER BY 30day_price ASC";
		return $this->fetch_all($this->query($sql));
	}
	
	public function getExcluded() {
		$sql = "SELECT link FROM `$GLOBALS[listTable]` WHERE market_id IS NOT NULL AND 30day_price IS NOT NULL AND 30day_count IS NOT NULL";
		$results = $this->fetch_all($this->query($sql));
		foreach ($results AS $result) {
			$arrReturn[] = $result['link'];
		}
		return $arrReturn;
	}
	
	protected function updateInterested() {
		$sql = "UPDATE `skin_list` SET `active` = 0";
		$this->query($sql);
		$sql = "UPDATE `skin_list` SET `active` = 1 WHERE `30day_price` >= $GLOBALS[lowPrice] AND `30day_price` <= $GLOBALS[highPrice]";
		$this->query($sql);
	}
}