<?php
$arrInterested = $actions->getInterested();
$skinperpage = 10;
$p = 1;
$x = 0;
echo "<div id='pages'>";
$prev = '';
$next = '';
if (isset($_GET['p'])) {
	$page = $_GET['p'];
} else { $page = 1; }
if ($page > 1) {
	$prev = $page - 1;
	$prev = "<a href='index.php?a=i&p=$prev'>Prev</a> | ";
}
echo $prev;
foreach ($arrInterested as $skin){	
	if ($x >= $skinperpage) {
		if ($p == $page) {
			echo "$p | ";
		} else {
			echo "<a href='index.php?a=i&p=$p'>$p</a> | ";
		}	
		$p++;
		$arrPaged[$p][] = $skin;
		$x = 0;
	} else {
		$arrPaged[$p][] = $skin;
		$x++;
	}
}
if (!is_numeric($page) || $page > $p || $page < 1) {
	$page = 1;
}
if ($page < $p) {
	$next = $page + 1;
	$next = "<a href='index.php?a=i&p=$next'>Next</a>";
}
echo $next;
echo "</div>";

$arrJS = array();
echo "<div id='refresh'><a href='javascript:void(0)' onclick='missingLowest()'>Refresh Missing Lowest</a> || <a href='javascript:void(0)' onclick='getBuyOrders(arrPage)'>Refresh All Buy Orders</a></div>";
echo "<table id='interested'>
		<tr>
			<th>Name</th>
			<th>Avg. Price</th>
			<th>Avg. Count<br />per 24hr</th>
			<th>Lowest Price</th>
			<th>Highest Order</th>
			<th>Diff After<br />Fees</th>
		</tr>";
foreach ($arrPaged[$page] as $skin) {
	if ($testing == false) {
		// $BO = $actions->getBuyOrders($skin['market_id']);
		// $BO = $BO->highest_buy_order;
		$BO = ' ';
		$lowest = ' ';
		$diff = ' ';
	} else {
		$BO = 1234;
		$lowest = '$12.34';
		$diff = '$1.23';
	}
		
	//$lowest = $actions->getLowestPrice($skin['link']);
	//$lowest = $lowest['lowest_price'];
	echo "<tr><td><a href='http://steamcommunity.com/market/listings/730/{$skin['link']}' target='_blank'>{$skin['name']}</a></td>
			<td>{$skin['30day_price']}</td>
			<td>".round($skin['30day_count']/30,0)."</td>
			<td id='low-{$skin['market_id']}' class='lowest'>$lowest</td>
			<td id='bo-{$skin['market_id']}'>$BO</td>
			<td id='diff-{$skin['market_id']}'>$diff</td>
		</tr>";
	echo "<span style='display:none;' id='{$skin['market_id']}'>{$skin['link']}</span>";
	$arrJS[$skin['market_id']] = $skin['link'];
}
echo "</table>";
$json = json_encode($arrJS);
echo $testing == false ? "<script type='text/javascript'>var arrPage = $json;</script>" : '';