<?php

include 'header.php';

echo "<table><tr><td><a href='index.php?a=t'>TRACKED</a></td><td><a href='index.php?a=i'>INTERESTED</a></td></tr></table>";

if (isset($_GET['a'])) {
	if ($_GET['a'] == 't') {
		include('tracked.php');
	} elseif ($_GET['a'] == 'i') {
		include('interested.php');
	}
}

// echo "<table>
		// <tr>
			// <th>NAME</th>
			// <th>30 DAY PRICE</th>
			// <th>COUNT</th>
			// <th>ACTIVE?</th>
		// </tr>";
		


?>