<?php
include_once("common/start.php");
include_once("common/session.php");

// consume food
if (isset($_POST['consumeItem'])) {

    CheckOutItem($conn, $userID, true);
}

// dispose food
if (isset($_POST['disposeItem'])) {

    CheckOutItem($conn, $userID, false);
}

function CheckOutItem($conn, $userID, $isConsumed)
{
    $stockID = PrepareDBVariables('stockID');
    $orgQty = PrepareDBVariables('originalServing');
    $leftQty = PrepareDBVariables('remainingServing');

    $checkoutQty = PrepareDBVariables('foodQty');

    // validate
    $errorMessage = "";
    if($checkoutQty == 0 || IsBlankField($checkoutQty))
    {
        $errorMessage .= "Please enter a valid checkout quantity\\n";
    }

    if($checkoutQty > $leftQty)
    {
        $errorMessage .= "You have entered a quantity greater than what is left!\\n";
    }

    // save to database
    if ($errorMessage == "") {

        $conn->beginTransaction();

        $arr = array(
            "userid" => $userID,
            "stock_id" => $stockID,
            "serving_out" => $checkoutQty,
            "user_dt" => MySQLDateTime("now"),
            "consumed_disposed" => $isConsumed ? 1 : 0
        );

        $query = PDO_Query_Insert('stock_out', $arr);
        $result = PDO_PartialTransaction_Insert($conn, $query, $arr, false);

        if ($result) {
                
            $conn->commit();
            
            $message = $isConsumed ? "Item successfully marked for consumption" : "Item successfully disposed";
            AlertReload($message);
        }
        else
        {
            $conn->rollback();
            AlertWindow("There has been a problem checking out this item...");
        }
    }
    else
    {
        AlertWindow($errorMessage);
    }
}

// dispose expired food
if (isset($_POST['deleteItem'])) {

    $stockID = key($_POST['deleteItem']);

    $query = "SELECT * FROM vw_current_stock WHERE stock_id = ?";
    $result = PDO_PreparedSelect_Single($conn, $query, array($stockID));
    if($result)
    {
        $qtyLeft = $result['serving_left'];

        $arr = array(
            "userid" => $userID,
            "stock_id" => $stockID,
            "user_dt" => MySQLDateTime("now"),
            "serving_out" => $qtyLeft,
            "consumed_disposed" => 0
        );

        $conn->beginTransaction();

        $queryInsert = PDO_Query_Insert('stock_out', $arr);
        $resultInsert = PDO_PartialTransaction_Insert($conn, $queryInsert, $arr, false);

        if($resultInsert)
        {
            $conn->commit();
            ReloadWindow();
        }
        else
        {
            $conn->rollback();
            AlertWindow("We have encountered a problem disposing the item");
        }
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
    Grocery Stock Level
</div>
</div>
</div>
</div>

<div class="container-fluid p-4" style="min-height: 500px;">
<div class="row">
<div class="col-md-6">
<h4 class="pb-3"><strong>Must Throw Now!</strong> <small>(expired, eat at your own risk)</small></h4>
<?php
$query = "SELECT * FROM vw_current_stock WHERE userid = ? AND serving_left > 0 AND days_left <= 0 ORDER BY days_left ASC";
$result = PDO_PreparedSelect_Array($conn, $query, array($userID));
if($result)
{
    echo '<table class="table table-hover" id="dataTable">';
    echo '<thead>';
    echo '<tr class="bg-danger text-white">';
    echo '<th>Category</th>';
    echo '<th>Food Name</th>';
    echo '<th>Description</th>';    
    echo '<th class="text-center"><small>Qty Left</small></th>';
    echo '<th class="text-center"><small>Days Expired</small></th>';
    echo '<th class="text-center" width="50"><i class="fas fa-trash"></i></th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';
    foreach($result as $row)
    {
        $stockID = $row['stock_id'];

        echo '<tr class="table-danger">';

        echo '<td class="align-middle">';
        echo $row['food_cat'];
        echo '</td>';

        echo '<td class="align-middle">';
        echo $row['food_id_name'];
        echo '</td>';

        echo '<td class="align-middle">';
        echo $row['prod_name'];
        echo '</td>';

        echo '<td class="text-center align-middle">';
        echo $row['serving_left'];
        echo '</td>';

        echo '<td class="text-center align-middle">';
        echo abs($row['days_left']);
        echo '</td>';

        // delete button
        echo '<td class="text-center">';
        echo HTML_Button("deleteItem[$stockID]", "<i class='fas fa-times text-danger'></i>", "btn btn-sm", false, "", false, false);
        echo '</td>';

        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
else
{
    echo HTML_Panel("You have no expired food!");
}

?>
</div>
<div class="col-md-6">
<h4 class="pb-3"><i class="fas fa-lemon mr-3 text-warning"></i><strong>The Fresh List</strong> <small>(can eat now)</small></h4>
<?php
$query = "SELECT * FROM vw_current_stock WHERE userid = ? AND serving_left > 0 AND days_left > 0 ORDER BY days_left ASC";
$result = PDO_PreparedSelect_Array($conn, $query, array($userID));
if($result)
{
    echo '<table class="table table-hover" id="dataTable">';
    echo '<thead>';
    echo '<tr class="bg-success text-white">';
    echo '<th>Category</th>';
    echo '<th>Food Name</th>';
    echo '<th>Description</th>';    
    echo '<th class="text-center"><small>Qty Left</small></th>';
    echo '<th class="text-center"><small>Days Left</small></th>';
    echo '<th class="text-center" width="50"><i class="fas fa-utensils"></i></th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';
    foreach($result as $row)
    {
        $daysLeft = abs($row['days_left']);

        echo '<tr';
        if($daysLeft > 3 && $daysLeft <= 8)
        {
            echo " class='table-warning'";
        }
        else if($daysLeft <= 3)
        {
            echo " class='bg-warning'";
        }

        echo '>';

        echo '<td class="align-middle">';
        echo $row['food_cat'];
        echo '</td>';

        echo '<td class="align-middle">';
        echo $row['food_id_name'];
        echo '</td>';

        echo '<td class="align-middle">';
        echo $row['prod_name'];
        echo '</td>';

        echo '<td class="text-center align-middle">';
        echo $row['serving_left'];
        echo '</td>';

        echo '<td class="text-center align-middle">';
        echo abs($row['days_left']);
        echo '</td>';

        echo '<td class="text-center">';

        $attr = array(
            "data-id" => $row['stock_id'],
            "data-food" => $row['food_id_name'],
            "data-food-description" => $row['prod_name'],
            "data-serving-original" => $row['serving'],
            "data-serving-remain" => $row['serving_left']
        );

        echo HTML_AjaxButton("foodStockOut", "<i class='fas fa-edit'></i>", " foodStockOut", false, $attr);
        echo '</td>';

        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
else
{
    echo HTML_Panel("You have no food left!");
}
?>
</div>
</div>
</div>
</main>
</form>
<?php include("footer.php"); ?>
</body>
</html>

<!-- Modal -->
<form method="post">
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title p-0" id="exampleModalLabel">
<strong>Food Check Out!</strong><small style="display:block">consume or throw</small>
</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<div class="pb-4">
<div id="foodName" style="text-transform:capitalize; font-size:18pt; font-weight:bold"></div>
<div id="foodDesc" style="display:block; font-size:12pt;"></div>
</div>
<?php
$formControl = HTML_Input('number', 'foodQty', "1", '', '', true, false, array('maxlength' => 5));
echo HTML_FormGroup('foodQty', 'Quantity', $formControl, false, '', true);
?>
</div>
<div class="modal-footer">
<?php
// hidden inputs
echo HTML_Input('hidden', 'stockID', '', '', '', false, false);
echo HTML_Input('hidden', 'originalServing', '', '', '', false, false);
echo HTML_Input('hidden', 'remainingServing', '', '', '', false, false);

echo HTML_Button("consumeItem", "Eat Now!", "btn btn-warning", false, "", false, false);
echo HTML_Button("disposeItem", "Throw...", "btn btn-danger", false, "", false, false);
?>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
</form>
<?php include("scripts.php"); ?>
<script>
$(".foodStockOut").click(function() {

    var stockID = $(this).attr('data-id');
    var foodName = $(this).attr('data-food');
    var foodDescription = $(this).attr('data-food-description');

    var servingOriginal = $(this).attr('data-serving-original');
    var servingRemainder = $(this).attr('data-serving-remain');

    $('#checkoutModal #stockID').val(stockID);
    $('#checkoutModal #originalServing').val(servingOriginal);
    $('#checkoutModal #remainingServing').val(servingRemainder);

    $('#checkoutModal #foodName').html(foodName);
    $('#checkoutModal #foodDesc').html(foodDescription);

    $('#checkoutModal').modal();
});

</script>