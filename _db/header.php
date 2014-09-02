<?php

if(!class_exists('Core')){
    require "backend.php";
}


Config::write('db.host', 'localhost');
Config::write('db.base', 'thepool');//whatsyo1_thepool - thepool
Config::write('db.user', 'dimlitl_prax');//whatsyo1_thepool - dimlitl_prax
Config::write('db.password', 'Radegast123/*');//P*OuT51Nq_T3 - Radegast123/*
Config::write('salt', 'qSfUi0iwXretE3A0waLekjdINi6S97hNMfu/rPNQcis=');

global $core;

try {
	$core = Core::getInstance();
}catch(Exception $e){
	print_r("PDO Connection Exception Occurred");
}

?>