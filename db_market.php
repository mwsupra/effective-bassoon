<?php

$conn_market = mysqli_connect('localhost', 'root', 'Il2g2DW!', 'market');

if ($conn_market->connect_error) {
	die('Connection Failed:'.$conn_market->connect_error);
}