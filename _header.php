<?php

    if(!session_id())
        session_start();

    include "./vendor/autoload.php";

    Config::write('db.host', 'localhost');
    Config::write('db.base', 'thepool');//whatsyo1_thepool - thepool
    Config::write('db.user', 'dimlitl_prax');//whatsyo1_thepool - dimlitl_prax
    Config::write('db.password', 'Radegast123/*');//P*OuT51Nq_T3 - Radegast123/*

    define("VERSION", "18");

?>