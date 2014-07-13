<?php

require_once "backend.php";

Config::write('db.host', 'localhost');
Config::write('db.base', 'dimlitl_sidekick');
Config::write('db.user', 'dimlitl_prax');
Config::write('db.password', 'Radegast123/*');
Config::write('salt', 'qSfUi0iwXretE3A0waLekjdINi6S97hNMfu/rPNQcis=');

global $core;

try {
	$core = Core::getInstance();
}catch(Exception $e){
	print_r("PDO Connection Exception Occurred");
}

?>