<?php

    global $user;
    global $ROOT_DB_PATH;

    include_once "{$ROOT_DB_PATH}header.php";
    include_once "{$ROOT_DB_PATH}security.php";
    include_once "{$ROOT_DB_PATH}objects.php";
    include_once "./admin.functions.php";

    define('IN_PHPBB', true);
    $phpbb_root_path = '../forum/';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($phpbb_root_path . 'common.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_user.'.$phpEx);

    if(isset($_GET['sid'])){
        $user->session_begin();

        if(!isset($_SESSION['userdata'])){
            $_SESSION['userdata'] = $user->data;
        }
    }else if(isset($_SESSION['userdata'])){
        $user->data = $_SESSION['userdata'];
    }else{
        exit;
    }

    if(!isset($_SESSION['sidekick']) || !is_array($_SESSION['sidekick'])){

        $_SESSION['sidekick'] = getSidekickUser($user->data['user_id']);

        if($_SESSION['sidekick'] === false)
            trigger_error("Sidekick Couldn't Load");


    }

    $auth->acl($user->data);
    $user->setup();

    if ($user->data['user_id'] == ANONYMOUS){
        exit;
    }else if(!$auth->acl_gets('m_')){
        exit;
    }

    FormValidation::generate();


?>