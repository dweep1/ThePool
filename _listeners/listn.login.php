<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./listn.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ./index.php");

        exit;

    }

    if((int)$submitType === 0){

        $response = doLogin($_POST);

        if($response === true){//login works


        }else if($response === -1){//incorrect password


        }else if($response === false){//incorrect email or account doesn't exist


        }//

    }

}


function doLogin($POST){

    $user = new users();

    if(!$user->load($POST['email'], 'email'))
        return false;

    if(!$user->doAuth($POST['password']))
        return -1;

    return true;

}

function doRegister($POST){

    if($POST['password'] === $)

    $POST['username'] = explode("@", $POST['email'])[0];

    //$POST['username'] = $POST['username'][0];

    /**
     * $username;
     * $email;
     * $password;
     * $salt;
     * $auth_key;
     * $first_name;
     * $last_name;
     * $last_ip;
     * $last_login_date;
     * $user_level;
     * $security_key;
     * $favorite_team_id;
     * $login_count;
     * $access_level; //if the user has verified their email and is not banned
    */


    $POST['$salt']


}

function doForgotPass($POST){

}

?>