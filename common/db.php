<?php

//---------- Basic PDO Select Functions
function PDO_Select_Single($conn, $query)
{
    $stmt = $conn->query($query);
    return $stmt->fetch();
}

function PDO_Select_Array($conn, $query)
{
    $stmt = $conn->query($query);
    return $stmt->fetchAll();
}

// prepared single select
function PDO_PreparedSelect_Single($conn, $query, $arrValues)
{
    $stmt = $conn->prepare($query);
    $stmt->execute($arrValues);
    
    return $stmt->fetch();
}

// prepared multi select
function PDO_PreparedSelect_Array($conn, $query, $arrValues) {
    
    $stmt = $conn->prepare($query);
    $stmt->execute($arrValues);
    
    return $stmt->fetchAll();
}

//---------- PDO Prepared Insert, Update and Delete Queries
// generate the query statements from arrays
function PDO_Query_Insert($table, $array)
{
    $i = 0;
    $numItems = count($array);
    
    $query = "INSERT INTO $table";
    $query .= ' (' . implode(', ', array_keys($array)) . ')';
    $query .= " VALUES (";
    foreach ($array as $key => $value) {
        
        $query .= ":$key";

        if (++$i !== $numItems) {
            $query .= ", ";
        }
    }
    $query .= ")";
    return $query;
}

function PDO_Query_Update($table, $array, $condField)
{
    $field = array();
    
    foreach ($array as $key => $value) {
        array_push($field, "$key = :$key");
    }
    
    $text = implode(",", $field);
    $query = "UPDATE $table SET $text WHERE $condField = :$condField";
    
    return $query;
}

function PDO_Query_Delete($table, $condfield) {
    
    $query = "DELETE FROM $table WHERE $condfield = :$condfield";
    return $query;
}

//---------- PDO Full Transaction Statements
// full transaction statements in the event that you want to commit or rollback with a single
// statement.
function PDO_Transaction_Update($conn, $query, $binds, $condField, $condVal)
{
    $conn->beginTransaction();
    $result = PDO_PartialTransaction_Update($conn, $query, $binds, $condField, $condVal);
    
    if($result)
    {
        $conn->commit();
        return true;
    }
    else
    {
        $conn->rollback();
        return false;
    }
}

//---------- PDO Partial Transaction Statements
// partial transaction statements happen when you need to insert, update or delete a few tables
// at once. Please call the beginTransaction, commit and rollback functions separately

function PDO_PartialTransaction_Insert($conn, $query, $array, $returnID = false)
{
    $stmt = $conn->prepare($query);
    
    // create binds
    $binds = array();
    foreach ($array as $key => $value) { $binds[":$key"] = $value; }
    
    $result = $stmt->execute($binds);
    if($result)
    {
        if($returnID)
        {
            // return insert id
            return $conn->lastInsertId();
        }
        else
        {
            return true;
        }
    }
    else
    {
        return false;
    }
}

function PDO_PartialTransaction_Update($conn, $query, $binds, $condField, $condVal)
{
    // bind the conditional variable
    if($condField != "")
    {
        $binds["$condField"] = $condVal;
    }
    
    // prepare update statement
    $stmt = $conn->prepare($query);
    foreach($binds as $name=>$value)
    {
        $bindParam = ":$name";

        if($value == "" || $value == null)
        {
            $stmt->bindValue($bindParam, null, PDO::PARAM_NULL);
        }
        else
        {
            // $stmt->bindParam($bindParam, $var = $value);
            $stmt->bindValue($bindParam, $value);
        }
    }
    
    return $stmt->execute();
}

function PDO_PartialTransaction_Delete($conn, $query, $condField, $condVal)
{
    $stmt = $conn->prepare($query);
    // $stmt->bindParam(":$condField", $var = $condVal);
    $stmt->bindValue(":$condField", $condVal);
    
    return $stmt->execute();
}

function PDO_PartialTransaction_MultiConditionalDelete($conn, $query, $arrConditions)
{
    $stmt = $conn->prepare($query);
    return $stmt->execute($arrConditions);
}

//---------- Custom PDO Functions
// SQL Date Formats for INSERTs and UPDATEs
function MySQLDate($date) {
    return date('Y-m-d', strtotime($date));
}

function MySQLDateTime($date) {
    return date('Y-m-d H:i:s', strtotime($date));
}

function MySQLTime($date) {
    return date('H:i:s', strtotime($date));
}

// display datetime formats
function DisplayDateTime($date)
{
    return date('d M Y g:ia', strtotime($date));
}

function DisplayDate($date)
{
    return date('d M Y', strtotime($date));
}

function DisplayTime($date)
{
    return date('g:ia', strtotime($date));
}

//---------- Preparing and Displaying DB Variables
// prepare db insert, update variables
function PrepareDBVariables($controlName, $isAllLowercase = false) {
    
    $var = (isset($_POST[$controlName]) && $_POST[$controlName] != "") ? trim($_POST[$controlName]) : "";

    if ($isAllLowercase) {
        $var = strtolower($var);
    }

    return $var;
}
function PrepareDBNumeralVariables($controlName) {
    
    $var = "0";
    if(isset($_POST[$controlName]) && trim($_POST[$controlName]) != "")
    {
        $var = trim($_POST[$controlName]);
        if(!is_numeric($var))
        {
            $var = "0";
        }
    }

    return $var;
}

function PrepareINQuery($arr, $colName)
{
    $values = "-1";
    if(count($arr) > 0)
    {
        $values = implode(",", $arr);
    }
    return "$colName IN ($values) ";
}

//---------- Misc DB Functions
// finding all the column names in a table
// exclude columns is case sensitive
function GetTableColumns($conn, $tableName, $excludes) {
    
    $arr = array();
    
    $query = "SHOW COLUMNS FROM $tableName";
    $result = $conn->query($query)->fetchAll();
    
    if($result)
    {
        foreach($result as $row)
        {
            // check for non-data columns
            // i.e. accountID_FK, primary key column etc...
            if(in_array($row['Field'], $excludes))
            {
                continue;
            }
            else
            {
                $arr[] = $row['Field'];
            }
        }
    }
    
    return $arr;
}

// count number of rows in a query
function CountRows($conn, $accountID, $tableName, $condArr)
{
    $arrBind = array();
    
    $query = "SELECT COUNT(*) FROM $tableName WHERE accountID_FK = ? ";
    $arrBind[] = $accountID;
    
    foreach($condArr as $cond=>$value)
    {
        $query .= "AND $cond = ? ";
        $arrBind[] = $value;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute($arrBind);
    
    return $stmt->fetchColumn();
}




?>