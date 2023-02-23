<?php
include 'includes/app_fcts.php';
include 'includes/connection.php';
include 'includes/sql_queries.php';
$page_title = "Manage Holidays";
if ($conn->connect_error) {
  die("Connection to database failed: " . $conn->connect_error);
}
page_header();
?>

<body>
<h1>Manage Holidays</h1>

<p>
<button class="navigation" onclick="window.location.href='index.php'">Back to Dashboard</button>
</p>

<!-- Task: Add Holiday -->

<h3>Add Holiday</h3>
<p>
<form id="addHdForm" action="manage_holiday.php" method="post">
  <label for="holidayName">Holiday Name:</label>
  <input type="text" id="holidayName" name="holidayName">
  <input type="submit" name="holidayButton" value="Add To List"/>
</form>
</p>

<?php
$hdButtonClicked = $_POST["holidayButton"] ?? null;
if (isset($hdButtonClicked) and $hdButtonClicked)
{
    $name = $_POST["holidayName"] ?? null;
    if (isset($name))
    {
        // verify that the name is valid
        $trimmedName = trim($name);
        $invalidName = $trimmedName == "";
        // verify that the name isn't taken
        $query = getCountHolidayName($trimmedName);
        $result = $conn->query($query);
        $value = $result->fetch_assoc();
        $nameTaken = $value["nb"] > 0;
        $shouldAdd = true;
        if ($invalidName)
        { ?>
            <span style="color:red">Holiday Name is blank, please try again.</span>
            <br></br>
        <?php
            $shouldAdd = false;
        }
        if ($nameTaken)
        { ?>
            <span style="color:red">Holiday Name is already in use, please try again.</span>
            <br></br>
        <?php
            $shouldAdd = false;
        }
        if ($shouldAdd)
        {
            $query = addHoliday($name);
            if (!$conn->query($query))
            { ?>
                <span style="color:red">Error adding holiday to list.</span>
                <br></br>
            <?php
            } else
            {
                // FIXME is there a better way to get this holiday into the list?
                header("Refresh:0");
            }
        }
    }
} ?>

<h3>Add Holiday Occurance</h3>
<p>
<form id="addHdOccForm" action="manage_holiday.php" method="post">
<select name="holidayResponse">
  <option value = "">Select Holiday</option>
  <?php
  $result = $conn->query($SQL_HOLIDAYS);
  while($row = $result->fetch_assoc()) {
  ?>
  <option value="<?php echo $row['HolidayID'];?>"><?php echo $row['HolidayName']; ?></option>
<?php } ?>
</select>
  <label for="dateSelect">Date:</label>
  <input type="date" id="dateSelect" name="dateSelect">
  <input type="submit" name="holidayOccuranceButton" value="Add To List"/>
</form>
</p>

<?php
$hdOccButtonClicked = $_POST["holidayOccuranceButton"] ?? null;
if (isset($hdOccButtonClicked) and $hdOccButtonClicked)
{
    $holidayId = $_POST["holidayResponse"] ?? null;
    $date = $_POST["dateSelect"] ?? null;
    if (isset($holidayId, $date))
    {
        // verify that the ID is valid
        $invalidId = $holidayId == "";
        // verify that the date is valid
        $dateTaken = false;
        $invalidDate = false;
        if (!strtotime($date))
        {
            $invalidDate = true;
        } else
        {
            list($year, $month, $day) = explode('-', $date);
            if(!checkdate($month, $day, $year))
            {
                $invalidDate = true;
            } else
            {
                // verify id/date combo doesn't exist
                $query = getCountHolidayDate($holidayId, $date);
                $result = $conn->query($query);
                $value = $result->fetch_assoc();
                $dateTaken = $value["nb"] > 0;
            }
        }
        $shouldAdd = true;
        if ($invalidId)
        { ?>
            <span style="color:red">Holiday not selected, please try again.</span>
            <br></br>
        <?php
            $shouldAdd = false;
        }
        if ($invalidDate)
        { ?>
            <span style="color:red">Invalid date selection, please try again.</span>
            <br></br>
        <?php
            $shouldAdd = false;
        }
        if ($dateTaken)
        { ?>
            <span style="color:red">Selected date already exists for that holiday, please try again.</span>
            <br></br>
        <?php
            $shouldAdd = false;
        }
        if ($shouldAdd)
        {
            $query = getCountCalendarDate($date);
            $result = $conn->query($query);
            $value = $result->fetch_assoc();
            if ($value["nb"] == 0)
            {
                // add the date if it doesn't exist in the DB
                $query = addCalendarDate($date);
                $conn->query($query);
            }
            $query = addHolidayOccurance($holidayId, $date);
            if (!$conn->query($query))
            { ?>
                <span style="color:red">Error adding holiday occurance.</span>
                <br></br>
            <?php
            } else
            { ?>
                <span style=>Success!</span>
                <br></br>
            <?php
            }
        }
    }
} ?>

<!-- Task: View Holidays -->

<h3>Current Holiday List</h3>
<table border="1">
<tr>
<th>Date</th>
<th>Holiday Name</th>
</tr>
<?php
$result = $conn->query($SQL_HOLIDAYS_WITH_DATES);
while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?=$row["HolidayDate"]?></td>
<td><?=$row["HolidayName"]?></td>
</tr>

<?php
}
?>

</table>
</body>
</html>
