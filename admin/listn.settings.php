<?php


global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";


if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    $settings = new options();
    $settings = $settings->getList();

    foreach($settings as $key => $value){
        $settings[$value->name] = $value;
        unset($settings[$key]);
    }

    foreach($_POST as $key => $value){

        if(isset($settings[$key])){
            $settings[$key]->value = $value;

            if($settings[$key]->save() === false)
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Unable to update a key value" : $_SESSION['result'];
        }

    }

    if(isset($_POST["use_credit"]) === false){
        $settings["use_credit"]->value = 0;

        if($settings["use_credit"]->save() === false)
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Unable to update a key value" : $_SESSION['result'];
    }

    $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Successfully Updated Settings" : $_SESSION['result'];

    header("Location: ./index.php");

    exit;

}


?>