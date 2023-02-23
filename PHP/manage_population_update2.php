<?php
include 'includes/app_fcts.php';
include 'includes/connection.php';
include 'includes/sql_queries.php';
$page_title = "Manage Population";
if ($conn->connect_error) {
  die("Connection to database failed: " . $conn->connect_error);
}
page_header();
session_start();
?>

<body>
<h1>Manage Population</h1>

<p>
<button class="navigation" onclick="window.location.href='index.php'">Back to Dashboard</button>
</p>


<!-- Task: Update Population -->

<h3>Update Population</h3>
<p>
<form id="updatePopulation" action="manage_population.php" method="post">
<select name="cityToUpdate">
  <option value = "">Select City</option>
  <?php
  $result = $conn->query($SQL_CITY_LIST);
  while($row = $result->fetch_assoc()) {
  ?>
  <option value="<?php echo $row['CityID'];?>"><?php echo $row['CityName'];?>,<?php echo $row['StateName'];?></option>
<?php
};
?>
</select>
<input type="submit" name="citySelected" value="Confirm"/>
</form>
</p>

<p>Current Population:
<?php
if(isset($_POST["cityToUpdate"])) {
  $cityID = $_POST["cityToUpdate"];
  $_SESSION [ 'cityID' ]= $cityID;
  $query = getCurrentPop($cityID);
  $result = $conn->query($query);
  $current_pop = $result->fetch_assoc();
  $currentPopulation = $current_pop["Population"];
  $_SESSION [ 'currentPop' ]= $currentPopulation;
  echo $currentPopulation;
};
?></p>

<p>
<form id="updatePop" action="manage_population.php" method="post">
  <label for="newPopulation">New Population:</label>
  <input id="newPopulation" name="newPopulation">
  <input type="submit" name="updateButton" value="Save"/>
</form>
</p>

<?php
if(isset($_SESSION [ 'cityID' ])) {
  $ChangeAlertThreshold = 0.1;
  $cityID = $_SESSION [ 'cityID' ];
  $currentPopulation = $_SESSION [ 'currentPop' ];
  if (isset($_POST["newPopulation"])){
    $newPopulation = $_POST["newPopulation"];
    if (floor($newPopulation)==$newPopulation && is_numeric($newPopulation) && $newPopulation >0) {
      $changeRate = ($newPopulation - $currentPopulation)/$currentPopulation;
      if ($changeRate < $ChangeAlertThreshold){
        $query2 = updatePop($newPopulation,$cityID);
        if (!$conn->query($query2)){
          ?>
          <span style="color:red">Error updating population, please try again.</span>
          <br></br>
        <?php
        }
        else {
          $result = $conn->query($query2);
          ?>
          <span style=>Successful updated!</span>
          <br></br>
        <?php
        };
      }
      else {
        ?>
        <span style="color:red">Change is over 10%, please try again.</span>
        <br></br>
        <?php
      };
    }
    else {
      ?>
      <span style="color:red">Invalid input, please try again.</span>
      <br></br>
      <?php
    };
  };
};
?>
<!-- Task: View Population -->

<h3>Cities List</h3>
<table border="1">
<tr>
<th>City Name</th>
<th>State Name</th>
<th>Population</th>
</tr>

<?php
$result = $conn->query($SQL_CITY_LIST);
while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?=$row["CityName"]?></td>
<td><?=$row["StateName"]?></td>
<td><?=$row["Population"]?></td>
</tr>
<?php
};
?>


</table>
</body>
</html>
