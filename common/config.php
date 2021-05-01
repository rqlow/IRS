<?php

//----- database functions
function SetDatabaseConnection() {
    $host = '';
    $dtbs = 'musteatnow';
    $user = '';
    $pass = '';
    $char = 'utf8';

    $dsn = "mysql:host=$host;dbname=$dtbs;charset=$char;port=25060";

    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    $conn = new PDO($dsn, $user, $pass, $options);    
    return $conn;
}

//----- file path functions
function PATH_Server()
{
    return "";
}

function PATH_Root() {
    return "";
}

function PATH_Host() {
    return "";
}


//----- file size
function UploadFileSize()
{
    return 5120000;
}

?>