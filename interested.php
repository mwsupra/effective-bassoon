<?php
$arrInterested = $actions->getInterested();

echo "<table>";

foreach ($arrInterested as $skin) {
	echo "<tr>";
	foreach ($skin as $attr) {
		echo "<td>$attr</td>";
	}
	echo "</tr>";
}

echo "</table>";