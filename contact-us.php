<?php
include_once("common/start.php");

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

<main role="main">

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
<div class="container">
<div class="row">
    
    <h1 class="heading1">Contact Us Today!</h1>
    <h3 class="heading3">And start to manage your grocery consumption and expenditure</h3>
    
</div>
</div>
</div>

<div class="container">
<div class="row py-4">
<div class="col-md-6 py-0">
<h3 class=''>MustEatNow! Inc.</h3>
<p class='text-muted pb-2 mb-0'>
10 Anson Road, #24-09<br>
International Building<br>
Singapore 079903<br><br>
<span class='text-dark'>Opening Hours</span><br>
Mon - Fri: 9am - 5pm<br>
Closed on Weekends and Public Holidays
</p>
</div>
<div class="col-md-6 py-0">
<h5>To find out more about our smart application, please fill in this form and one of our representatives will be in touch as soon as we can.</h5>
<form method='post' class='pt-4'>
<div class="form-group">
<input type="text" class="form-control" placeholder="Your Name" name='enquiryName' id="name" required data-validation-required-message="Please enter your name">
</div>
<div class="form-group">
<input type="email" class="form-control" placeholder="Your Email" name='enquiryEmail' id="email" required data-validation-required-message="Please enter your email address">
</div>
<div class="form-group">
<textarea class="form-control" style="resize: none;" rows='5' placeholder="Your Message" name='enquiryMessage' id="message" required data-validation-required-message="Please enter a message"></textarea>
</div>
<div id="success"></div>
<div class='row'>
<div class="col-md-8">
<div class="g-recaptcha" data-sitekey="6LfnhqEUAAAAABTGoZKp-6cOZjUT9z_KOB1XnOIq" style="margin:0 auto; display:inline-block"></div>
</div>
<div class="col-md-4">
<button type="submit" class="btn btn-warning btn-lg" name='sendEnquiry' style='margin-top: 10px; font-size:12pt;'>Send Message</button>
</div>
</div>
</form>


</div>
</div>

</div>
</main>
<?php include("footer.php"); ?>
</body>
</html>
