<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

global $si;

if(isset($_GET['si']))
    $si = $_GET['si'];
else
    $si = 1;

header("Location: index.php");

?>