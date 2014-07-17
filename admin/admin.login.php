<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if((int)$submitType === 0){
        $response = doLogin($_POST);

        if($response === false)//incorrect password
            $_SESSION['result'] = "Incorrect Password";
        else{//login works
            $_SESSION['result'] = "Login Success!";
            $_SESSION['user'] = $response->toArray();
        }

    }



    header("Location: ./index.php");

}

function doLogin($POST){

    $user = users::returnCurrentUser();

    return (!$user->doAuth($POST['password'], -1)) ? false : $user;

}

?>