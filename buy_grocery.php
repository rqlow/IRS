<?php
include_once("common/start.php");
include_once("common/session.php");

$defaultDays = 3;
if (isset($_POST['checkStock'])) {

    if (isset($_POST['stockNumDays'])) {

        $defaultDays = $_POST['stockNumDays'];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<?php include("meta.php"); ?>
<title>Must Eat Now | The Smart Way to Managing Your Food</title>
<style>
/* The slider handle (use -webkit- (Chrome, Opera, Safari, Edge) and -moz- (Firefox) to override default look) */
#stockNumDays::-webkit-slider-thumb {
  -webkit-appearance: none; /* Override default look */
  appearance: none;
  background: #04AA6D; /* Green background */
  cursor: pointer; /* Cursor on hover */
}

#stockNumDays::-moz-range-thumb {
  background: #04AA6D; /* Green background */
  cursor: pointer; /* Cursor on hover */
}
</style>
</head>

<body>
<?php include("navbar.php"); ?>

<form method="post" enctype="multipart/form-data">
<main role="main">

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron1">
<div class="container-fluid">
<div class="row">
<div class="col-md-12">
    What Should I Buy Today?
</div>
</div>
</div>
</div>

<div class="container-fluid p-4" style="min-height: 500px;">
<div class="row">
<div class="col-md-12">
<h4 class="pb-3"><strong>My Grocery List</strong> <small>(should buy these items soon...)</small></h4>
</div>
<div class="col-md-6">
<?php
echo '<div class="row pb-5">';
echo '<div class="col-md-10">';
// range slider
echo HTML_RangeSlider("stockNumDays", "Projected Number of Days from Today... <strong><span id='numDays'></span></strong>", "1", "7", $defaultDays);
echo '</div>';
echo '<div class="col-md-2 text-center">';
echo HTML_Button("checkStock", "Check Stock", "btn btn-warning", false, "", false, false);
echo '</div>';
echo '</div>';
?>
</div>
<div class="col-md-8">
<?php
$query = "SELECT * FROM vw_what_will_runout WHERE userid = ? AND days_to_consumed <= ? ORDER BY days_to_consumed ASC";
$result = PDO_PreparedSelect_Array($conn, $query, array($userID, $defaultDays));
if($result)
{
    echo '<table class="table table-bordered" id="dataTable">';
    echo '<thead>';
    echo '<tr class="bg-warning">';
    echo '<th class="align-middle">Category</th>';
    echo '<th class="align-middle">Food Name</th>';  
    echo '<th class="align-middle d-none d-md-table-cell">Favorite Brands</th>';  
    echo '<th width="150" class="text-center align-middle">Qty Remaining</th>';
    echo '<th width="150" class="text-center align-middle">Before Stock Runs Out</th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';
    foreach($result as $row)
    {
        echo '<tr>';

        echo '<td class="align-middle">';
        echo $row['food_cat'];
        echo '</td>';

        echo '<td class="align-middle" style="text-transform:capitalize; font-size: 15pt; font-weight:bold">';
        echo $row['food_id_name'];
        echo '</td>';

        echo '<td class="align-middle d-none d-md-table-cell" style="text-transform:capitalize">';
        echo $row['products'];
        echo '</td>';

        echo '<td class="text-center align-middle">';
        echo $row['serving_left'];
        echo '</td>';

        // days
        $days = abs($row['days_to_consumed']);

        echo '<td class="text-center align-middle">';
        echo $days;
        echo $days == 1 ? " day" : " days";
        echo '</td>';

        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
else
{
    echo HTML_Panel("We are not able to project your Grocery List!");
}

?>
</div>
<div class="col-md-6">

</div>
</div>
</div>
</main>
</form>
<?php include("footer.php"); ?>
</body>
</html>
<script>
    var slider = document.getElementById("stockNumDays");
    var output = document.getElementById("numDays");
    output.innerHTML = slider.value + " days"; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    slider.oninput = function() {
        dayText = " day";
        if(this.value > 1)
        {
            dayText = " days";
        }
        output.innerHTML = this.value + dayText;
    }
</script>