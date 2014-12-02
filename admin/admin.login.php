<?php

include"./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

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

    return (!$user->doAuth($POST['password'], 0)) ? false : $user;

}

?>