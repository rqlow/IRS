<?php
error_reporting(E_ALL);

date_default_timezone_set('Asia/Singapore');
session_start();

//********** Required Files **********//
require("common/config.php");
require("common/paths.php");

require(PATH_Common() . "db.php");
require(PATH_Common() . "forms.php");
require(PATH_Common() . "functions.php");

require(PATH_Common() . "alerts.php");
require(PATH_Common() . "validation.php");

// start database connection
$conn = SetDatabaseConnection();

?>