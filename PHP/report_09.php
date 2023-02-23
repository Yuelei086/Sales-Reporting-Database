<?php
include 'includes/app_fcts.php';
include 'includes/connection.php';
include 'includes/sql_queries.php';
$page_title = "Report 9 - Advertising Campaign Analysis";
if ($conn->connect_error) {
	die("Connection to database failed: " . $conn->connect_error);
}
page_header();
?>

<body>
<h1>Report 9 - Advertising Campaign Analysis</h1>

<p>
<button class="navigation" onclick="window.location.href='index.php'">Back to Dashboard</button>
</p>

<!-- Task: Advertising Campaign Analysis Report -->

<table border="1">
<tr>
<th>Product ID</th>
<th>Product Name</th>
<th>Sold During Campaign</th>
<th>Sold Outside Campaign</th>
<th>Difference</th>
</tr>

<?php

$result = $conn->query($SQL_ADVERT_ANALYSIS);
$total_records = $result->num_rows;
$count1 = 1;
$count2 = $total_records - 9;

while($row = $result->fetch_assoc()) {
	if ($count1 <= 10 or $count1 >= $count2){
?>
<tr>
<td class="numeric"><?=$row["PID"]?></td>
<td><?=$row["ProductName"]?></td>
<td class="numeric"><?=$row["SoldDuringCampaign"]?></td>
<td class="numeric"><?=$row["SoldOutsideCampaign"]?></td>
<td class="numeric"><?=$row["Diff"]?></td>
</tr>
<?php
};
$count1++;
};
?>


</table>
</body>
</html>
