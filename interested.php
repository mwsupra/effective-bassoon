<?php
$arrInterested = $actions->getInterested();
$skinperpage = 10;
$p = 1;
$x = 0;
foreach ($arrInterested as $skin){	
	if ($x >= $skinperpage) {
		echo "<a href='index.php?a=i&p=$p'>$p</a> | ";
		$p++;
		$arrPaged[$p][] = $skin;
		$x = 0;
	} else {
		$arrPaged[$p][] = $skin;
		$x++;
	}
}
if (isset($_GET['p'])) {
	$page = $_GET['p'];
	if (!is_numeric($page) || $page > $p || $page < 1) {
		$page = 1;
	}
} else { $page = 1; }
$arrJS = array();
echo "<table>";
foreach ($arrPaged[$page] as $skin) {
	$BO = $actions->getBuyOrders($skin['market_id']);
	//$lowest = $actions->getLowestPrice($skin['link']);
	//$lowest = $lowest['lowest_price'];
	echo "<tr>
			<td><a href='http://steamcommunity.com/market/listings/730/$skin[link]' target='_blank'>$skin[name]</a></td>
			<td>".$skin['30day_price']."</td>
			<td>".round($skin['30day_count']/30,0)."</td>
			<td></td>
			<td>$BO->highest_buy_order</td>
		</tr>";
	$arrJS[$skin['market_id']] = $skin['link'];
}
echo "</table>";
$json = json_encode($arrJS);
echo "<script type='text/javascript'>var arrPage = $json;</script>";