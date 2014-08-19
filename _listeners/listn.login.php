<?php

include_once "./listn.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(!isset($_SESSION['login_attempts']))
        $_SESSION['login_attempts'] = 0;

    if((int)$submitType === 0){

        $response = doLogin($_POST);

        if($response === -1){//incorrect password

            $_SESSION['result'] = "Incorrect Password";
            $_SESSION['login_attempts'] = $_SESSION['login_attempts'] + 1;

        }else if($response === false)//incorrect email or account doesn't exist
            $_SESSION['result'] = "That email/username isn't in our database";
        else{//login works
            $_SESSION['login_attempts'] = 0;
            $_SESSION['user'] = $response->toArray();
        }

    }else if((int)$submitType === 1){//register

        $response = doRegister($_POST);

        if($response === -1){//User Information Issue
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Some information provided was invalid" : $_SESSION['result'] ;
        }else if($response === -2)
            $_SESSION['result'] = "ERROR: Username/Email already exists!";
        else if($response === false)//db error
            $_SESSION['result'] = "Database Error";
        else //registration successful
            $_SESSION['result'] = "Registration Successful!";

    }else if((int)$submitType === 2){//forgot password

        $response = doForgotPass($_POST);

        if($response === -1)//email didnt exist
            $_SESSION['result'] = "The Email Address doesn't exist in our database";
        else if($response === -2)//can't save user
            $_SESSION['result'] = "Database Error";
        else if($response === false)
            $_SESSION['result'] = "Unable to Mail Key";
        else
            $_SESSION['result'] = "Successfully Submitted Password Resend Request";


    }else if((int)$submitType === 3){//user is resetting password

        $response = doPasswordReset($_POST);

        if($response === -1){//password didn't meet requirements
            if(!isset($_SESSION['result']))
                $_SESSION['result'] = "Password Error, please click the link in your email and try again.";
            else
                $_SESSION['result'] .= "<br/>Please click the link in your email and try again.";
        }else if($response === -2)//user doesn't exist
            $_SESSION['result'] = "Invalid Key, please click the link in your email and try again.";
        else if($response === true)
            $_SESSION['result'] = "You may now login with your new password.";
        else
            $_SESSION['result'] = "Database Error";

    }

    header("Location: ../index.php");

}

function doPasswordReset($POST){

    $keyUser = new users();

    if(!users::verifyRegInfo($POST))
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

    if($user->load(explode("@", $POST['email'])[0], 'username'))
        return -2;

    if(!users::verifyRegInfo($POST))
        return -1;

    $password = new Password($POST['password']);

    $POST['username'] = explode("@", $POST['email'])[0];
    $POST['password'] = $password->getKey();
    $POST['salt'] = $password->getSalt();
    $POST['security_key'] = Cipher::getRandomKey(16);
    $POST['auth_key'] = Cipher::getRandomKey(16);
    $POST['user_level'] = -1;

    $user = new users($POST);

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