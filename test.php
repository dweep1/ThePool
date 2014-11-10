<?php

    if(!session_id())
        session_start();

    include "./vendor/autoload.php";

    $instance = new admin_log();

    var_dump($instance);