<?php

    if(!session_id()) {
        session_start();
    } else {

    }

    global $ROOT_DB_PATH;

    $ROOT_DB_PATH = "./_db/";

    include_once "{$ROOT_DB_PATH}header.php";
    include_once "{$ROOT_DB_PATH}security.php";
    include_once "{$ROOT_DB_PATH}objects.php";

    if(isset($_SESSION['user']))
        $currentUser = new users($_SESSION['user']);
    else
        $currentUser = false;

?>