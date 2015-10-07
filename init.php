<?php

/** Masterファイルのパス **/
define('MASTER_PATH','/home/sudos127/public_html/omc');

function cpdir($src,$dst) { 
    $dir = opendir($src); 
    
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                cpdir($src . '/' . $file,$dst . '/' . $file) . " <br />"; 
            } 
            else {
            	if($file != 'wp-config.php')
				copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}
