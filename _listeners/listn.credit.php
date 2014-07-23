<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include_once "./listn.header.php";

    if(!isset($_GET['method']))
        exit;

    echo json_encode(array("results" => "true"));

    exit;

    /**

    if($_GET['method'] === 'USE'){

        echo json_encode(array("results" => credit::useCredit()));

    }else if($_GET['method'] === 'CHECK'){

        echo json_encode(array("results" => (credit::validCredit() === false) ? credit::validCredit(null, 0) : true));

    }
     *
     *
     * */


?>