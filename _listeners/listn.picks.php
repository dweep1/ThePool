<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include_once "./listn.header.php";

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(isset($_GET['method']))
        $method = $_GET['method'];
    else
        exit;

    if($method === 'GET'){

        $data = file_get_contents("php://input");
        $objData = json_decode($data);

        $obj = new week($objData->week_id);

        $obj->getStructured(null, true);

        echo json_encode($obj);

    }

?>