<?php 
require_once(dirname(__FILE__).'/init.php');
$masterinstall = defined("MASTER_INSTALL");

$copied = array();

//If not an installation, then we unserialize the copied files
if(!$masterinstall){
	//Load serialized
	$existing = dirname(__FILE__).'/masterfiles';
	if(file_exists($existing)){
		$tmp = file_get_contents($existing);
		$copied = unserialize($tmp);
	}
}


//Start the update/install process
cpdir_updated(MASTER_PATH,dirname(__FILE__),$masterinstall);

//Delete deleted files in the master
//technically, if this is the first installation, there is no 
//files in the masterfiles, so nothing will happen
delete_deleted();

function cpdir_updated($src,$dst,$include_cached) { 

    $dir = opendir($src); 
	
	global $copied;
	
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                cpdir_updated($src . '/' . $file,$dst . '/' . $file,$include_cached) . " <br />"; 
            } 
            else {
            	//if wp-config, ignore
            	if($file == 'wp-config.php') continue;
            	
            	$dstful = $dst . '/' . $file;
            	$srcful = $src . '/' . $file; 
            	
            	//Ignore master files' cache files.
            	if(!$include_cached){
	            	if(startswith($srcful,MASTER_PATH . '/wp-content/cache/')){
	            		continue;
	            	}
            	}
            	
            	//if new file, copy it
            	if(!file_exists($dstful)){
            		//echo $dstful . ' is a new file<br />';
            		copy($srcful, $dstful);
            		$copied[$srcful] = $dstful;
            		
            		
            	}
            	//if Updated copy it
            	else if(filemtime($dstful) < filemtime($srcful)){
            		//echo $dstful . ' is updated <br />';
            		copy($srcful, $dstful);
            		//echo "Updated: $dstful <br />";
            		$copied[$srcful] = $dstful;
            	}
				 
            } 
        } 
    } 
    closedir($dir); 
} 
function delete_deleted() { 
  	global $copied;
  	foreach($copied as $src => $dst){
  		if(!file_exists($src)){
  			//TODO: make sure we ignore files to be ignored
  			//1. User modified CSS files
  			//    if file starts with **wp-content/themes and ends with css or CSS, ignore
  			//2. Uploads
  			//    if file starts with **wp-content/uploads/
  			unlink($dst);
//  			echo "Delete: $dst <br />";
  			unset($copied[$src]);
  		}
  	}
}
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

//update the serialized masterfiles
//TODO: we need to save masterfiles only if modified to eliminate redundancy
file_put_contents(dirname(__FILE__) .'/masterfiles', serialize($copied));

