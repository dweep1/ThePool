<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "./";

include "{$ROOT_DB_PATH}header.php";
include "{$ROOT_DB_PATH}objects.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $dataArray = array('ip_address' => $_SERVER['REMOTE_ADDR'],
                        'browser' => $_SERVER['HTTP_USER_AGENT'],
                        'page_header' => "{$_POST['page_title']}",
                        'report' => $_POST['report'],
                        'email' => $_POST['email']);

    $bug_report = new bugs($dataArray);

    echo json_encode(array('success' => $bug_report->save()));

}


?>
