<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    global $ROOT_DB_PATH;
    $ROOT_DB_PATH = "../_db/";

    include "./admin.header.php";

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(!isset($_GET['method']))
        exit;

    if($_GET['method'] === 'GET'){

        $obj = new credit();

        if((int) $objData->user_id === -1)
            $obj = $obj->getList();
        else
            $obj = $obj->getList(null, array("user_id" => $objData->user_id));

        if(is_array($obj)){
            foreach($obj as $key => $value){

                $value->date = displayDate($value->date);
                $obj[$key] = $value;
            }
        }

        echo json_encode($obj);

    }

    function displayDate($dateTimeString){
        $tempDate = new DateTime($dateTimeString);
        return $tempDate->format('D, m/d @ h:i a');
    }



?>