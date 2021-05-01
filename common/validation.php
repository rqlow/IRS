<?php

// basic text and numeral validation via regular expression
function IsValidEmail($input) {
    $regex = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidEmails($input)
{
    $regex = '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25}(;[ ]{0,1}([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})*$/';
    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidURL($input) {
    $regex = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidFullURL($input) {
    $regex = '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/ix';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsAlphanumeric($input) {
    $regex = '/^[0-9a-zA-Z]{0,30}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidPageURL($input) {
    $regex = '/^[0-9a-zA-Z\-_\/]{0,200}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidName($input) {
    $regex = '/^[0-9a-zA-Z ]{0,100}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidCode($input) {
    $regex = '/^[0-9a-zA-Z_\- ]{0,100}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidNameNoSpaces($input) {
    $regex = '/^[0-9a-zA-Z_]{0,100}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidMeta($input) {
    $regex = '/^[a-zA-Z0-9,.\- ]{0,1000}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidPassword($input) {
    $regex = '/^[a-zA-Z0-9]{8,20}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidContactNumber($input) {
    $regex = '/^[0-9+()\- ]{1,30}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidNumeral($input) {
    $regex = '/^[0-9]{1,30}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidNumeralDecimal($input) {
    $regex = '/^[0-9.]{1,30}$/';

    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidDecimal($input, $maxlength, $precision)
{
    $diff = $maxlength - $precision;
    $regex = "/^\d{1,$diff}(\.\d{1,$precision})?$/";
    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

function IsValidImageFile($input)
{
    $regex = '/^.*\.(jpg|jpeg|png|gif)$/i';
    return RegexMatch($regex, $input);
}

function IsValidDateRange($startDate, $endDate)
{
    $cStartDate = new DateTime($startDate);
    $cEndDate = new DateTime($endDate);
    
    if($cStartDate > $cEndDate)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function IsDateWithinDateRange($startDate, $endDate, $compareDate)
{
    $cStartDate = new DateTime($startDate);
    $cEndDate = new DateTime($endDate);

    $cCompareDate = new DateTime($compareDate);
    if($cCompareDate >= $cStartDate && $cCompareDate <= $cEndDate)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function IsExpired($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    return $start > $end ? true : false;
}

function IsToday($date)
{
    $today = date("dmY");
    $thisDate = date("dmY", strtotime($date));

    return $today == $thisDate ? true : false;
}

// the function for matching regex
function RegexMatch($regex, $input)
{
    if (preg_match($regex, $input)) {
        return true;
    } else {
        return false;
    }
}

// validate textboxes which are non-mandatory
function ValidateNames($inputName, $titleName)
{
    $errorMessage = "";
    if($_POST[$inputName] != "" && !IsValidName($_POST[$inputName]))
    {
        $errorMessage .= "The $titleName should only contain alphanumeric characters\\n";
    }
    
    return $errorMessage;
}

function IsBlankField($data)
{
    if(trim($data) == "")
    {
        return true;
    }
    else
    {
        return false;
    }
}

//---------- Files
function CheckFileUploadError($errorCode) {
    $uploadErrors = array(
        UPLOAD_ERR_INI_SIZE => 'This file exceeds maximum file upload size!',
        UPLOAD_ERR_FORM_SIZE => 'This file exceeds maximum file upload size!',
        UPLOAD_ERR_PARTIAL => 'This file was only partially uploaded!',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
    );

    if (isset($uploadErrors[$errorCode])) {
        return $uploadErrors[$errorCode];
    } else {
        return "There was an error in uploading the file";
    }
}

function IsAcceptedUploadFileExt($ext) {
    $extArray = array("bmp", "jpg", "jpeg", "png", "gif", "doc", "xls", "ppt", "pdf", "txt", "docx", "xlsx", "pptx");

    if (in_array(strtolower($ext), $extArray)) {
        return true;
    } else {
        return false;
    }
}

function IsImgFileExt($ext) {
    $extArray = array("bmp", "jpg", "jpeg", "png", "gif");

    if (in_array(strtolower($ext), $extArray)) {
        return true;
    } else {
        return false;
    }
}

function IsAcceptedFileName($filename) {
    $syntaxArray = array('/', '\\', ':', '*', '?', '"', '\'', '<', '>', '|', '&');

    if (in_array($filename, $syntaxArray)) {
        return false;
    } else {
        return true;
    }
}

function IsAcceptedFileExt($ext) {
    $extArray = array("bmp", "jpg", "jpeg", "png", "gif", "doc", "xls", "ppt", "pdf", "txt", "htm", "html", "swf", "mp3", "wma", "docx", "xlsx", "pptx", "js", "xml");

    if (in_array(strtolower($ext), $extArray)) {
        return true;
    } else {
        return false;
    }
}

?>