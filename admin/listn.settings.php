<?php


global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";


if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ./index.php");

        exit;

    }

    $settings = new options();
    $settings = $settings->getList();

    foreach($settings as $key => $value){
        $settings[$value->name] = $value;
        unset($settings[$key]);
    }

    foreach($_POST as $key => $value){

        if(isset($settings[$key])){
            $settings[$key]->value = $value;

            if($settings[$key]->update() === false)
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Unable to update a key value" : $_SESSION['result'];
        }

    }

    if(isset($_POST["use_credit"]) === false){
        $settings["use_credit"]->value = 0;

        if($settings["use_credit"]->update() === false)
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Unable to update a key value" : $_SESSION['result'];
    }

    $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Successfully Updated Picks" : $_SESSION['result'];

    header("Location: ./index.php");

    exit;

}


?>