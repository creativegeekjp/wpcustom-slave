<?php

if(file_exists(dirname(__FILE__) .'/wp-load.php')){
	echo 'Already installed';exit;
}
define("MASTER_INSTALL",true);
include('init.php');
include('filecheck.php');
echo 'DONE';
