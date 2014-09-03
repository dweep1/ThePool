<?php

    global $time;
    global $mem;
    global $memTwo;

    define("VERSION", "12");

    $time = microtime(TRUE);
    $mem = memory_get_usage();

    if(!session_id())
        session_start();

    global $ROOT_DB_PATH;

    $ROOT_DB_PATH = "./_db/";

    include "{$ROOT_DB_PATH}header.php";
    include "{$ROOT_DB_PATH}security.php";
    include "{$ROOT_DB_PATH}objects.php";

    $memTwo = memory_get_usage();


?>