<?php
//-------------------- directory and file IO
// check for single flie upload
function CheckUploadFile($folder, $files, $isImage, $checkExist)
{
    // get the temp file path
    $tmp_name = $files["tmp_name"];
    $name = $files["name"];
    
    // destination file
    $targetFile = $folder . "/" . $files['name'];
    $targetFile = strtolower($targetFile);
        
    //---------- validate
    $errorMessage = "";

    if ($files['name'] == "") {
        $errorMessage .= "Please select a new File to upload...\\n";
    }
    
    $tmpFilePath = $files['tmp_name'];
    if ($tmpFilePath != "") {
        // first level of checks
        // 1. check for proper filename syntax
        // 2. check for accepted file extensions / MIME types
        // 3. second level of checks for file size
        if($isImage)
        {
            if (!IsImgFileExt(GetFileExtension($name))) {
                $errorMessage .= "This is not an acceptable image file...\\n";
            }
        }
        else
        {
            if (!IsAcceptedFileExt(GetFileExtension($name))) {
                $errorMessage .= "This file has an unaccepted extension...\\n";
            }
        }

        if ($files['size'] > UploadFileSize()) {
            $errorMessage .= "The file is too large to upload...\\n";
        }

        // final level of checks
        // 1. check for existing file
        if ($checkExist) {
            if (file_exists($targetFile)) {
                $errorMessage .= "This file already exists...\\n";
            }
        }
    }
    
    return $errorMessage;
}

// if multiple files are uploaded at one time
function CheckUploadFiles($folder, $files, $isImage, $checkExist)
{
    // count number of uploaded files in array
    $totalFiles = count($files['name']);
    
    //---------- validate
    $errorMessage = "";

    if ($files['name'][0] == "") {
        $errorMessage .= "Please select a new File to upload...\\n";
    }
    
    for ($i = 0; $i < $totalFiles; $i++) {
        
        // get the temp file path
        $tmp_name = $files["tmp_name"][$i];
        $name = $files["name"][$i];

        $tmpFilePath = $files['tmp_name'][$i];
        if ($tmpFilePath != "") {
            
            // destination file
            $targetFile = $folder . "/" . $files['name'][$i];
            $targetFile = strtolower($targetFile);
            
            // first level of checks
            // 1. check for proper filename syntax
            // 2. check for accepted file extensions / MIME types
            // 3. second level of checks for file size
            if($isImage)
            {
                if (!IsImgFileExt(GetFileExtension($name))) {
                    $errorMessage .= "File [" . $files['name'][$i] . "] is not an acceptable image file...\\n";
                    continue;
                }
            }
            else
            {
                if (!IsAcceptedFileExt(GetFileExtension($name))) {
                    $errorMessage .= "File [" . $files['name'][$i] . "] has an unaccepted extension...\\n";
                    continue;
                }
            }
            
            if ($files['size'][$i] > UploadFileSize()) {
                $errorMessage .= "File [" . $files['name'][$i] . "] is too large to upload...\\n";
                continue;
            }
            
            // final level of checks
            // 1. check for existing file
            if ($checkExist) {
                if (file_exists($targetFile)) {
                    $errorMessage .= "File [" . $files['name'][$i] . "] already exists...\\n";
                    continue;
                }
            }
        }
    }
    
    return $errorMessage;
}

function GetFileExtension($filepath) {
    
    $arr = explode(".", $filepath);
    return array_pop($arr);
}

function GetFolderSize($dir) {
    $size = 0;
    foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : GetFolderSize($each);
    }
    return $size;
}

function DisplayFileSize($filepath) {
    $fileSize = filesize($filepath);
    return ReadableFilesize($fileSize);
}

function ReadableFilesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function FormatFileSize($size, $rounding) {
    $mod = 1024;
    $units = explode(' ', 'B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }

    $arr = array(
        "value" => round($size, $rounding),
        "units" => $units[$i]
    );

    return $arr;
}

// sorts directories and files in a glob array
function SortFiles($a, $b) {
    $aIsDir = is_dir($a);
    $bIsDir = is_dir($b);
    if ($aIsDir === $bIsDir)
        return strnatcasecmp($a, $b); // both are dirs or files
    elseif ($aIsDir && !$bIsDir)
        return -1; // if $a is dir - it should be before $b
    elseif (!$aIsDir && $bIsDir)
        return 1; // $b is dir, should be before $a
}

function FileTypeName($ext) {
    $fileType = "Unknown File Extension";

    switch (strtolower($ext)) {
        case "bmp":
            $fileType = "Bitmap Image";
            break;

        case "jpg":
            $fileType = "JPEG Image";
            break;

        case "jpeg":
            $fileType = "JPEG Image";
            break;

        case "png":
            $fileType = "PNG Image";
            break;

        case "gif":
            $fileType = "GIF Image";
            break;

        case "doc":
        case "docx":
            $fileType = "Word Document";
            break;

        case "xls":
        case "xlsx":
            $fileType = "Excel Spreadsheet";
            break;

        case "ppt":
        case "pptx":
            $fileType = "Powerpoint Presentation";
            break;

        case "pdf":
            $fileType = "PDF Document";
            break;

        case "txt":
            $fileType = "Text Document";
            break;

        case "html":
            $fileType = "HTML Document";
            break;

        case "htm":
            $fileType = "HTML Document";
            break;

        case "mp3":
            $fileType = "MP3 Audio File";
            break;

        default:
            $fileType = "Unknown File Extension";
            break;
    }

    return $fileType;
}

function DoesDirectoryExist($dir) {
    if (is_dir($dir)) {
        return true;
    } else {
        return false;
    }
}

function CreateDirectory($dir)
{
    if(!DoesDirectoryExist($dir))
    {
        mkdir($dir);
    }
}

function CopyDirectoy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);

    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                CopyDirectoy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }

    closedir($dir);
}

function RemoveDirectory($dir, $isDelete) {
    if (!$dh = @opendir($dir))
        return;

    while (false !== ($obj = readdir($dh))) {
        if ($obj == '.' || $obj == '..')
            continue;
        if (!@unlink($dir . '/' . $obj))
            RemoveDirectory($dir . '/' . $obj, true);
    }

    closedir($dh);

    if ($isDelete) {
        @rmdir($dir);
    }
}

function SaveCompareFile($filename, $data) {
    if (file_put_contents($filename . "~", $data) === strlen($data)) {
        return rename($filename . "~", $filename);
    }

    unlink($filename . "~");
    return false;
}

function DeleteFile($folder, $file)
{
    return unlink($folder.$file);
}

function GenerateRandomFileName($originalFileName)
{
    $ext = GetFileExtension($originalFileName);

    $newName = GenerateRandomString();
    $newName = strtolower(substr($newName, 0, 10));
    $newName .= ".$ext";
    
    return $newName;
}

function GenerateRandomString($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//-------------------- string masking functions
function GetFirstLetters($string)
{
    $words = explode(" ", $string);
    $acronym = "";

    foreach ($words as $w) {
      $acronym .= $w[0];
    }
    
    return $acronym;
}

function GetFirstLetters_UTF($string)
{
    $var = preg_match_all('/./u', $string, $matches);

    $arr = $matches[0];
    if(count($arr) > 1)
    {
        return $arr[0].$arr[1];
    }
    else
    {
        return $arr[0];
    }
}

function MaskNumber($text, $numTextToShow = 4){
    
    $maskText =  str_repeat("*", strlen($text)-$numTextToShow) . substr($text, -$numTextToShow);
    return $maskText;
}

function MaskEmail($email)
{
    $em   = explode("@",$email);
    $name = implode('@',array_slice($em, 0, count($em)-1));
    $len  = floor(strlen($name)/2);

    return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);   
}

//-------------------- general functions
function RemoveAllWhitespace($string) {
    return preg_replace('/\s+/', '', $string);
}

function TimeArray($start, $end, $interval = '30 mins', $format = '12')
{
    $startTime = strtotime($start); 
    $endTime   = strtotime($end);
    $returnTimeFormat = ($format == '12')?'g:i A':'G:i';

    $current   = time(); 
    $addTime   = strtotime('+'.$interval, $current); 
    $diff      = $addTime - $current;

    $times = array(); 
    while ($startTime < $endTime) { 
        
        $timeVal = date($returnTimeFormat, $startTime);
        $times[$timeVal] = $timeVal; 
        
        $startTime += $diff; 
    }
    
    $timeVal = date($returnTimeFormat, $startTime);
    $times[$timeVal] = $timeVal; 

    return $times;
}

function DayDifferenceInDates($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    return $end->diff($start)->format("%a");
}

function FormatUnitSize($size, $rounding) {
    $mod = 1000;
    $units = array(
        "0" => "",
        "1" => "K",
        "2" => "M",
        "3" => "G",
    );

    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }

    $arr = array(
        "value" => round($size, $rounding),
        "units" => $units[$i]
    );

    return $arr;
}

?>