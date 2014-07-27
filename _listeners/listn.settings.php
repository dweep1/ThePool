<?php

include_once "./listn.header.php";

//@TODO settings page.

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if((int)$submitType === 0){

        $user = users::returnCurrentUser();

        //if(Password::)

    }

    header("Location: ../index.php");

}

function doPasswordReset($POST){

    $keyUser = new users();

    if(!verifyRegInfo($POST))
        return -1;

    if($keyUser->load($POST['security_key'], "security_key")){
        $password = new Password($POST['password']);

        $keyUser->password = $password->getKey();
        $keyUser->salt = $password->getSalt();

        if(!$keyUser->update())
            return false;

    }else
        return -2;

    return true;
}

function doLogin($POST){

    $user = new users();

    if(!$user->load($POST['email'], 'email')){
        if(!$user->load($POST['email'], 'username'))
            return false;
    }

    if(!$user->doAuth($POST['password']))
        return -1;

    return $user;

}

function doRegister($POST){

    $user = new users();

    if($user->load($POST['email'], 'email'))
        return -2;

    $password = new Password($POST['password']);

    $POST['username'] = explode("@", $POST['email'])[0];
    $POST['password'] = $password->getKey();
    $POST['salt'] = $password->getSalt();
    $POST['security_key'] = Cipher::getRandomKey(16);
    $POST['auth_key'] = Cipher::getRandomKey(16);
    $POST['user_level'] = -1;

    $user = new users($POST);

    if(verifyRegInfo($POST) !== false)
        return -1;

    return ($user->save(true)) ? true : false;

}

function doForgotPass($POST){

    $user = new users();

    if(!$user->load($POST['email'], 'email'))
        return -1;

    $user->security_key = Cipher::getRandomKey(true);

    if(!$user->update())
        return -2;

    $keyFormat = "{$_SERVER['HTTP_HOST']}/index.php?key={$user->security_key}";

    $message = "<a href='{$keyFormat}'>Click Here</a> to update your password.<br/>
        <br/>You Recently tried to reset your account password. This is an email notifing you of the request.
        <br/>If this was not you, then you may ignore this email. Your information is safe with us.
        <br/>
        <br/>
        If the link above does not work, then try and copy the following address into your web browser.<br/>
        <br/>
        {$keyFormat}<br/>
        <br/>
        <br/>
        This is an automated response, please do not reply!<br/>";

    return Core::sendEmail('The Pool - Forgotten Password', $message, $POST['email']);

}

function verifyRegInfo($POST){

    $errors = 0;
    $password = $POST['password'];

    foreach($POST as $key => $value){

        if($key === "username"){
            if(filter_var($value, FILTER_SANITIZE_STRING) == false ||  strlen($value) < 3 ){
                $errors++;
                $_SESSION['result'] = 'Invalid Username. Usernames must be longer then 3 characters';
            }
        }

        if($key === "email"){
            if(filter_var($value, FILTER_VALIDATE_EMAIL) == false){
                $errors++;
                $_SESSION['result'] = 'Invalid Email Address';
            }
        }

        if($key === "password"){
            $password = $value;
            if(0 === preg_match("/.{6,}/", $value)){
                $errors++;

                $_SESSION['result'] = 'Invalid Password. Must contain at least 6 characters';
            }
        }

        if($key === "confirm"){
            if(0 !== strcmp($password, $value)){
                $errors++;
                $_SESSION['result'] = 'Passwords do not match';
            }
        }
    }

    return ($errors > 0) ? false : true;

}

?>