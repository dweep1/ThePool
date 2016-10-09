<?php

    date_default_timezone_set('America/New_York');

    if(!session_id())
        session_start();

    include "./vendor/autoload.php";

    Config::write('db.host', 'localhost');
    Config::write('db.base', 'zadmin_thepool');//whatsyo1_thepool - thepool
    Config::write('db.name', 'zadmin_thepool');//whatsyo1_thepool - thepool
    Config::write('db.user', 'thepool');//whatsyo1_thepool - dimlitl_prax
    Config::write('db.password', '2agene5u5');//P*OuT51Nq_T3 - Radegast123/*

    define("VERSION", "18");

?>
