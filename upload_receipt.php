<?php
include_once("common/start.php");
include_once("common/session.php");

$receiptOutput = "";
if (isset($_POST['receiptSubmit'])) {

    $targetFolder = PATH_Storage($userID, "1");

    $myReceiptFile = $_FILES['receiptFile'];
    $errorMessage = CheckUploadFile($targetFolder, $myReceiptFile, true, false);

    if($errorMessage == "")
    {
        $tmpFilePath = $_FILES['receiptFile']['tmp_name'];
        if ($tmpFilePath != "") {

            // destination file
            $destFilename = GenerateRandomFileName($_FILES['receiptFile']['name']);
            $targetFile = $targetFolder . $destFilename;

            // $targetFile = $targetFolder . $_FILES['receiptFile']['name'];
            // $targetFile = strtolower($targetFile);

            $result = move_uploaded_file($tmpFilePath, $targetFile);
            if($result)
            {
                
            }
            else
            {
                AlertWindow("We have encountered an error uploading the Receipt");
            }

            // generate table
            $receiptOutput = GenerateReceiptTable($conn, $targetFile);

            // delete file
            unlink($targetFile);
        }
    }
    else
    {
        AlertWindow($errorMessage);
    }
}

// add items
if (isset($_POST['submitList1'])) {

    $dataArr = $_SESSION['musteatnow-GroceryList'];
    $foodItems = $dataArr['food_items'];
    $unknownItems = $dataArr['unknown'];

    if(count($unknownItems) > 0)
    {
        $conn->beginTransaction();
        $isSuccess = true;

        $i = 1;
        if(isset($_POST['foodClassifier']))
        {
            foreach($_POST['foodClassifier'] as $classifierID)
            {
                $arrUnknown = $unknownItems[$i];

                // tag to be saved
                if($classifierID != 0)
                {
                    $arrTag = array(
                        "food_id" => $classifierID,
                        "food_tag_raw" => $arrUnknown['item'],
                        "insert_dt" => MySQLDateTime("now")
                    );

                    $queryTag = PDO_Query_Insert('stock_tag', $arrTag);
                    $resultTag = PDO_PartialTransaction_Insert($conn, $queryTag, $arrTag, false);

                    // insert new item to stock_in
                    $arr = array(
                        "stockin_dt" => MySQLDateTime("now"),
                        "userid" => $userID,
                        "food_id" => $classifierID,
                        "prod_name" => $arrUnknown['full_text'],
                        "serving" => $arrUnknown['quantity'],
                        "price" => $arrUnknown['price']
                    );
        
                    $query = PDO_Query_Insert('stock_in', $arr);
                    $result = PDO_PartialTransaction_Insert($conn, $query, $arr, false);

                    if(!$resultTag || !$result)
                    {
                        $isSuccess = false;
                        break;
                    }
                    print_r($arrTag);
                    print_r($arr);
                }

                $i++;
            }
        }

        if($isSuccess)
        {
            $conn->commit();
            AlertReload("Grocery list uploaded!");
        }
        else
        {
            $conn->rollback();
            AlertWindow("We have encountered a problem uploading the receipt items");
        }
    }
    else
    {
        echo "000";
    }
}

if (isset($_POST['submitList'])) {

    if(isset($_SESSION['musteatnow-GroceryList']))
    {
        $dataArr = $_SESSION['musteatnow-GroceryList'];
        $foodItems = $dataArr['food_items'];
        $unknownItems = $dataArr['unknown'];

        $conn->beginTransaction();
        $isSuccess = true;

        // save items
        foreach($foodItems as $dr)
        {
            $arr = array(
                "user_dt" => MySQLDateTime("now"),
                "userid" => $userID,
                "food_id" => $dr['food_id'],
                "prod_name" => $dr['full_text'],
                "serving" => $dr['quantity'],
                "price" => $dr['price']
            );

            $query = PDO_Query_Insert('stock_in', $arr);
            $result = PDO_PartialTransaction_Insert($conn, $query, $arr, false);
            if(!$result)
            {
                $isSuccess = false;
                break;
            }
        }

        // save to corpus
        if(count($unknownItems) > 0)
        {
            $i = 1;
            if(isset($_POST['foodClassifier']))
            {
                foreach($_POST['foodClassifier'] as $classifierID)
                {
                    $arrUnknown = $unknownItems[$i];

                    // tag to be saved
                    if($classifierID != 0)
                    {
                        $arrTag = array(
                            "food_id" => $classifierID,
                            "food_tag_raw" => $arrUnknown['item'],
                            "insert_dt" => MySQLDateTime("now")
                        );

                        $queryTag = PDO_Query_Insert('stock_tag', $arrTag);
                        $resultTag = PDO_PartialTransaction_Insert($conn, $queryTag, $arrTag, false);

                        // insert new item to stock_in
                        $arr = array(
                            "user_dt" => MySQLDateTime("now"),
                            "userid" => $userID,
                            "food_id" => $classifierID,
                            "prod_name" => $arrUnknown['full_text'],
                            "serving" => $arrUnknown['quantity'],
                            "price" => $arrUnknown['price']
                        );
            
                        $query = PDO_Query_Insert('stock_in', $arr);
                        $result = PDO_PartialTransaction_Insert($conn, $query, $arr, false);

                        if(!$resultTag || !$result)
                        {
                            $isSuccess = false;
                            break;
                        }
                    }

                    $i++;
                }
            }
        }

        if($isSuccess)
        {
            $conn->commit();
            AlertReload("Grocery list uploaded!");
        }
        else
        {
            $conn->rollback();
            AlertWindow("We have encountered a problem uploading the receipt items");
        }
    }
}

function GenerateReceiptTable($conn, $filePath)
{
    $output = "";

    $cmd = "python3 -m py.ocr.src.ocr $filePath --save_text --save_img";
    $exec = shell_exec($cmd);

    $exec = str_replace("'", '"', $exec);
    $json = json_decode($exec, true);

    // print_r($json);

    // set session
    $_SESSION['musteatnow-GroceryList'] = $json;

    // get all items from stock master table
    $queryStock = "SELECT * FROM stock_master";
    $resultStock = PDO_PreparedSelect_Array($conn, $queryStock, null);
    $arrStock = SetDropdownOptionArray($resultStock, "food_id", "food_id_name");

    if($json == null || count($json) <= 0)
    {
        AlertWindow("The system is unable to recognize the receipt.\\nPlease re-upload a clearer version");
    }
    else
    {
        foreach($json as $type=>$typeArr)
        {
            if($type == "food_items")
            {
                if(count($typeArr) > 0)
                {
                    $output .= '<h4 class="pb-3"><strong>My Grocery List</strong> <small>(identified products)</small></h4>';
                    $output .= '<table class="table table-hover table-bordered" id="dataTable">';
                    $output .= '<thead>';
                    $output .= '<tr>';
                    $output .= '<th>Product Name</th>';
                    $output .= '<th>Product Tag</th>';
                    $output .= '<th>Description</th>';
                    $output .= '<th>Price</th>';
                    $output .= '<th>Quantity</th>';
                    $output .= '</tr>';
                    $output .= '</thead>';

                    $output .= '<tbody>';
                    foreach($typeArr as $dr)
                    {
                        $output .= '<tr>';

                        $foodID = $dr['food_id'];
                        $output .= '<td style="text-transform:capitalize">'.$arrStock[$foodID].'</td>';

                        $output .= '<td>'.$dr['food_tag'].'</td>';
                        $output .= '<td>'.$dr['full_text'].'</td>';
                        $output .= '<td>$'.number_format($dr['price'],2).'</td>';
                        $output .= '<td>'.$dr['quantity'].'</td>';

                        $output .= '</tr>';
                    }
                    $output .= '</tbody>';
                    $output .= '</table>';
                }
            }

            if($type == "unknown")
            {
                if(count($typeArr) > 0)
                {
                    // get array of food names
                    $query = "SELECT *, CONCAT_WS(' - ', food_cat_name, food_id_name) AS FoodName FROM stock_master 
                            INNER JOIN food_cat_master ON stock_master.food_cat_id = food_cat_master.food_cat_id 
                            ORDER BY food_cat_name, food_id_name ASC";
                    $result = PDO_PreparedSelect_Array($conn, $query, null);

                    $drClassifiers = array("0" => "Do Not Store This Item");
                    $drClassifiers += SetDropdownOptionArray($result, "food_id", "FoodName");

                    // create table
                    $output .= '<h4 class="py-3"><small>Unidentified Products</small></h4>';
                    $output .= '<table class="table table-hover table-bordered" id="dataTable">';
                    $output .= '<thead>';
                    $output .= '<tr>';
                    $output .= '<th>Identifying Tag</th>';
                    $output .= '<th>Description</th>';
                    $output .= '<th>Price</th>';
                    $output .= '<th>Quantity</th>';
                    $output .= '<th>Classification</th>';
                    $output .= '</tr>';
                    $output .= '</thead>';

                    $output .= '<tbody>';
                    foreach($typeArr as $rowID => $dr)
                    {
                        $output .= '<tr>';

                        $output .= '<td>'.$dr['item'].'</td>';
                        $output .= '<td>'.$dr['full_text'].'</td>';
                        $output .= '<td>$'.number_format($dr['price'],2).'</td>';
                        $output .= '<td>'.$dr['quantity'].'</td>';

                        $output .= "<td>";
                        $output .= HTML_Select('foodClassifier['.$rowID.']', "0", '', false, $drClassifiers, array("style" => "text-transform: capitalize"));
                        $output .= "</td>";

                        $output .= '</tr>';
                    }
                    $output .= '</tbody>';
                    $output .= '</table>';
                }
            }
        }

        // submit button
        $output .= HTML_Button("submitList", "Add Items", "btn btn-warning btn-lg", false, "", false, false);
    }

    return $output;
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
    Upload Grocery Receipt
</div>
</div>
</div>
</div>

<div class="container-fluid p-4" style="min-height: 500px;">
<div class="row">
<div class="col-md-4 pb-5">
<div class="form-group">
<label for="receiptFile1">Select your Receipt Image</label>
<div class="custom-file">
  <input type="file"  id="receiptFile" name="receiptFile" accept=".jpg,.jpeg,.png" onChange="makeFileList();">
  <label class="custom-file-label" id="newFileList" for="receiptFile">Choose file</label>
</div>
</div>
<?php
echo HTML_Button("receiptSubmit", "Upload Receipt", "btn btn-warning", false, "", false, false);
// echo HTML_Button("submitList", "Add Items", "btn btn-warning btn-lg", false, "", false, false);
?>
</div>
<div class="col-md-8">
<?php
echo $receiptOutput;
?>
</div>
</div>
</div>
</main>
</form>
<?php include("footer.php"); ?>
</body>
</html>
<script>
    function makeFileList() {
        var input = document.getElementById("receiptFile");
        var label = document.getElementById("newFileList");

        label.innerText = input.files[0].name;
    }
</script>