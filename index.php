<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="onload.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="style.css">
<?php

include 'header.php';
echo "<div id='content'>";
echo "<table id='tabs'><tr><td><a href='index.php?a=t'>TRACKED</a></td><td><a href='index.php?a=i'>INTERESTED</a></td></tr></table>";

if (isset($_GET['a'])) {
	if ($_GET['a'] == 't') {
		include('tracked.php');
	} elseif ($_GET['a'] == 'i') {
		include('interested.php');
	}
}


echo "</div>";
// echo "<table>
		// <tr>
			// <th>NAME</th>
			// <th>30 DAY PRICE</th>
			// <th>COUNT</th>
			// <th>ACTIVE?</th>
		// </tr>";
		


?>