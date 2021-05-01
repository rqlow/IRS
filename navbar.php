<?php
if (isset($_POST['systemLogin'])) {

    // get login variables
    $loginID = trim(strtolower($_POST['systemLoginID']));
    $loginPassword = trim($_POST['systemPassword']);

    if ($loginID == "" || $loginPassword == "") {
        AlertReload("Please key in your Login ID and your Password");
    }
    // check that the login id is a valid email address
    else if (!IsValidEmail($loginID)) {
        AlertReload("Your Login ID format is invalid!");
    } else {

        $query = "SELECT * FROM user_master WHERE email = ? AND password = ?";
        $result = PDO_PreparedSelect_Single($conn, $query, array($loginID, $loginPassword));
        if($result)
        {
            $_SESSION['musteatnow-UserID'] = $result['userid'];
            $_SESSION['musteatnow-Name'] = $result['displayname'];
            $_SESSION['musteatnow-Household'] = $result['household_name'];

            ReloadWindow();
        }
        else
        {
            AlertReload("You have keyed in an invalid ID and Password");
        }
    }
}
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-md fixed-top">
<a class="navbar-brand" href="."><img src="images/logo.png"></a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
</button>

<div class="collapse navbar-collapse" id="navbarsExampleDefault">
<ul class="navbar-nav mr-auto pl-4">
<?php
if(isset($_SESSION['musteatnow-UserID']))
{
?>
<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
App
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <a class="dropdown-item" href="upload_receipt.php">Upload Grocery Receipt</a>
    <a class="dropdown-item" href="grocery_level.php">Check Grocery Stock Level</a>
    <a class="dropdown-item" href="buy_grocery.php">What Should I Buy Today?</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="?logout">Logout</a>
</div>
</li>
<?php
}
else
{
    echo '<li class="nav-item">';
    echo '<a class="nav-link" data-toggle="modal" data-target="#loginModal" style="cursor:pointer">Login</a>';
    echo '</li>';
}
?>

<li class="nav-item active">
<a class="nav-link" href="about-us.php">About Us</a>
</li>
<li class="nav-item active">
<a class="nav-link" href="features.php">Features</a>
</li>
<li class="nav-item">
<a class="nav-link" href="contact-us.php">Contact Us</a>
</li>
</ul>
</div>
</nav>

<!-- Modal -->
<form method="post">
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title p-0" id="exampleModalLabel">
<small>Login To</small> <strong>Must Eat Now!</strong>
</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<?php
$isLock = false;

echo '<div class="form-group mb-2">';
echo '<input type="email" value="" class="form-control prestart-form-control" placeholder="Login ID" name="systemLoginID" maxlength="95" required autofocus';
if ($isLock) {
    echo ' disabled value="Your login has been temporarily disabled..."';
}
echo ' value="">';
echo '</div>';
echo '<div class="form-group mt-2">';
echo '<input type="password" value="" class="form-control prestart-form-control" placeholder="Password" name="systemPassword" maxlength="95" required';
if ($isLock) {
    echo ' disabled';
}
echo ' value="">';
echo '</div>';
?>
</div>
<div class="modal-footer">
<?php
echo '<button type="submit" class="btn btn-warning" name="systemLogin" id="systemLogin"';
if ($isLock) {
    echo ' disabled';
}
echo '>';
if ($isLock) {
    echo '<i class="fas fa-lock fa-fw"></i>&nbsp;&nbsp;&nbsp;Access Disabled';
} else {
    echo '<i class="fas fa-utensils fa-fw"></i>&nbsp;&nbsp;&nbsp;Login';
}
echo '</button>';
?>
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
</form>