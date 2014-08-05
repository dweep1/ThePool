<?php

global $time;
global $mem;

$time = microtime(TRUE);
$mem = memory_get_usage();

global $require_login; //if the login to this page is required;
global $require_admin;

include "../_objects/class.header.php";

@define('PREFIX', "ADMIN");

header::setup('../');

include ROOT_PATH.CLASS_PATH."/class.db.php";
$mysqlDB = new db();

global $pdo;

$pdo = $mysqlDB->newPDO();
unset($mysqlDB);

include ROOT_PATH.CLASS_PATH."/class.user.php";

if($require_login === true){
    header::require_login();
}

if($require_admin === true){
    header::require_admin("./index.php");
}



?>
