<?php

    global $time;
    global $mem;
    global $memTwo;

    define("VERSION", "7");

    $time = microtime(TRUE);
    $mem = memory_get_usage();

    if(!session_id())
        session_start();

    global $ROOT_DB_PATH;

    $ROOT_DB_PATH = "./_db/";

    include_once "{$ROOT_DB_PATH}header.php";
    include_once "{$ROOT_DB_PATH}security.php";
    include_once "{$ROOT_DB_PATH}objects.php";

    $memTwo = memory_get_usage();


?>