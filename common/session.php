<?php
// check whether this is a logout request
if(isset($_GET['logout']))
{
    session_unset();
    session_destroy();
    
    echo "<script language='JavaScript'>window.alert('Thank you for not wasting food!\\nSee you again soon...');window.location='index.php'</script>";
}

// verify that the session exists
if (!isset($_SESSION['musteatnow-UserID']) || $_SESSION['musteatnow-UserID'] == "" || $_SESSION['musteatnow-UserID'] == "0") {
    
    session_unset();
    session_destroy();
        
    echo "<script language='JavaScript'>window.alert('Your session has expired.\\nPlease login again...');window.location='index.php'</script>";
}

//********** Session Variables **********//
$userID = $_SESSION['musteatnow-UserID'];
$userName = $_SESSION['musteatnow-Name'];
$userHousehold = $_SESSION['musteatnow-Household'];
?>