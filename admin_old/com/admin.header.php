<?php 

include "../../_objects/class.header.php";

header::setup('../../');

include ROOT_PATH.CLASS_PATH."/class.db.php";
$mysqlDB = new db();

global $pdo;

$pdo = $mysqlDB->newPDO();
unset($mysqlDB);

include ROOT_PATH.CLASS_PATH."/class.user.php";

?>