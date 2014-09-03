<?php

    global $ROOT_DB_PATH;

    if(!defined("VERSION"))
        define("VERSION", "5");

    include_once "{$ROOT_DB_PATH}header.php";
    include_once "{$ROOT_DB_PATH}security.php";
    include_once "{$ROOT_DB_PATH}objects.php";
    include_once "./admin.functions.php";

?>