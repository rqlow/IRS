<?php

function PATH_Relative()
{
    return "/";
}

//---------- common pathing
// path types
function BasePathing($type)
{
    $basepath = "";
    switch($type)
    {
        // 1 - direct path
        case "1":
            $basepath = PATH_Root();
            break;
        
        // 2 - virtual (host) path
        case "2":
            $basepath = PATH_Host();
            break;
        
        // 3 - relative path
        case "3":
            $basepath = PATH_Relative();
            break;
    }
    
    return $basepath;
}


// root and host paths will already have been set in the config.php file
//
//----- root and common folders
function PATH_Common() {
    return PATH_Root() . "common/";
}

function VPATH_Common() {
    return PATH_Host() . "common/";
}

function PATH_Assets() {
    return PATH_Root() . "assets/";
}


//----- storage folders
function PATH_Storage($accountName, $pathType) {
            
    return BasePathing($pathType) . "storage/$accountName/";
}

function PATH_AppStorage($accountName, $pathType, $appName) {
            
    return PATH_Storage($accountName, $pathType) . "$appName/";
}

function PATH_ModStorage($accountName, $pathType, $appName, $modName, $modID)
{
    return PATH_AppStorage($accountName, $pathType, $appName) . "$modName/$modID/";
}

?>