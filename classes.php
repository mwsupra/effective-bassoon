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
}

class Actions extends Functions {
	public $functions;
	public $arrTracked;
	
	function __construct() {
		$this->functions = new Functions();
		$this->arrTracked = $this->getTracked();
	}
	
	protected function getTracked() {
		$sql = "SELECT * FROM `$GLOBALS['listTable']` WHERE active = 1";
		return $this->fetch_all($this->query($sql));
	}
	
	public function getExcluded() {
		$sql = "SELECT link FROM `$GLOBALS['listTable']` WHERE market_id IS NOT NULL AND 30day_price IS NOT NULL AND 30day_count IS NOT NULL";
		return $this->fetch_all($this->query($sql));
	}
}