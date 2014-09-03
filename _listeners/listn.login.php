<?php

include "./listn.header.php";

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

        header("Location: ../login.php");

    }else if((int)$submitType === 1){//register

        $response = doRegister($_POST);

        if($response === -1){//User Information Issue
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Some information provided was invalid" : $_SESSION['result'] ;
        }else if($response === -2)
            $_SESSION['result'] = "ERROR: Username/Email already exists!";
        else if($response === false)//db error
            $_SESSION['result'] = "Database Error";
        else{

            $message = "You are now a member of a premiere betting league. Your account allows you to compete against others in a weekly betting pool.
            This is a chance for you to win big, through smart plays and persistence throughout the NFL season.
            <br/><br/><br/>
            You have registered for The Pool using the e-mail address: {$_POST['email']}
            <br/><br/>
            You may login by using either your email address listed above, or by using the following user name, along with the password you entered in when you signed up.
            <br/><br/>
            $username
            <br/><br/><br/>
            You may login and make your picks; Select a favorite team; Or change any personal information in the settings panel.
            <br/><br/>
            <a href='{$_SERVER['HTTP_HOST']}/rules.php'>Be sure to read over the rules before making your picks.</a>";

            $emailMessage = "
                <div class=\"email-template\" style='font-family: \"Open Sans\", sans-serif; background: #fff; width:auto; height:100%; padding:10px 20px; text-align: left;'>
                    <table cellpadding='0' cellspacing='0' style='width:800px; height:auto; margin:10px auto; border:1px solid #b9b9ba; background: #f1f1f2; padding:0px; border-radius:4px; border-bottom: 2px solid rgba(100,100,100,0.9);'>
                        <tr>
                            <td style='background: url('http://i.imgur.com/4S4yqzW.png'); border-radius:3px 3px 0px 0px; height:100px;'>
                                <img style='border-radius:3px 3px 0px 0px;' src='http://i.imgur.com/rMfcpC8.png'>
                            </td>
                        </tr>

                        <tr>
                           <td style='padding:0px 25px; padding-top:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>
                                <h2 style='border-bottom:1px solid rgba(120,120,255, 0.9); padding:3px 0px; margin-right:3px; font-weight:300;'>
                                    Welcome to The Pool!
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:0px 25px; padding-bottom:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>

                                $message

                            </td>
                        </tr>
                        <tr>
                            <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                                Copyright 'The Pool', Anthony Harris,  2014<br/>
                            </td>
                        </tr>
                    </table>
                </div>";

            @Core::sendEmail('Welcome to The Pool', $emailMessage, $_POST['email']);

            $response = doLogin($_POST);

            if($response === false || $response === -1 || $response === -2){
                $_SESSION['result'] = "We had a problem with the auto login. Your account has been created, you may now login.";
            }else{
                $_SESSION['login_attempts'] = 0;
                $_SESSION['user'] = $response->toArray();
            }

        }

        header("Location: ../register.php");


    }else if((int)$submitType === 2){//forgot password

        $response = doForgotPass($_POST);

        if($response === -1)//email didnt exist
            $_SESSION['result'] = "The Email Address doesn't exist in our database";
        else if($response === -2)//can't save user
            $_SESSION['result'] = "Database Error";
        else if($response === false)
            $_SESSION['result'] = "Unable to Mail Key";
        else
            $_SESSION['result'] = "Successfully Submitted Password Resend Request. Check your email for a link in a few minuets.";

        header("Location: ../login.php");


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

        header("Location: ../login.php");

    }


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

    if($user->load($POST['username'], 'username'))
        return -2;

    if(!users::verifyRegInfo($POST))
        return -1;

    $password = new Password($POST['password']);

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

    $user->security_key = substr(Cipher::getRandomKey(true), 0, -2);

    if(!$user->update())
        return -2;

    $keyFormat = "{$_SERVER['HTTP_HOST']}/index.php?key={$user->security_key}";

    $message = "You Recently tried to reset your account password. This is an email notifing you of the request.
        <br/>If this was not you, then you may ignore this email. Your information is safe with us.
        <br/>
        <a href='{$keyFormat}'>Click Here to update your password.</a><br/>
        <br/>
        <br/>
        <br/>
        <br/>
        If you cannot click on that link, copy and paste the following link into your web browser. (make sure you copy the entire link!)<br/>
        <br/>
        $keyFormat
        <br/>
        <br/>
        <br/>
        This is an automated response, please do not reply!";

    $emailMessage = "
        <style>
            @import url(http://fonts.googleapis.com/css?family=Open+Sans);

            .email-template h1, .email-template h2, .email-template h3, .email-template h4, .email-template h5{
                font-family: 'Open Sans', sans-serif;
                font-weight:300;
                color:rgb(80,80,80);
                padding:3px 0px;
                margin-right:3px;
                border-bottom:1px solid rgba(120,120,255, 0.9);
            }
        </style>

        <div class=\"email-template\" style='font-family: \"Open Sans\", sans-serif; background: #fff; width:auto; height:100%; padding:10px 20px; text-align: left;'>
            <table cellpadding='0' cellspacing='0' style='width:800px; height:auto; margin:10px auto; border:1px solid #b9b9ba; background: #f1f1f2; padding:0px; border-radius:4px; border-bottom: 2px solid rgba(100,100,100,0.9);'>
                <tr>
                    <td style='background: url('http://i.imgur.com/4S4yqzW.png'); border-radius:3px 3px 0px 0px; height:100px;'>
                        <img style='border-radius:3px 3px 0px 0px;' src='http://i.imgur.com/rMfcpC8.png'>
                    </td>
                </tr>

                <tr>
                   <td style='padding:0px 25px; padding-top:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>
                        <h2 style='border-bottom:1px solid rgba(120,120,255, 0.9); padding:3px 0px; margin-right:3px; font-weight:300;'>
                            Password Reset
                        </h2>
                    </td>
                </tr>
                <tr>
                    <td style='padding:0px 25px; padding-bottom:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>

                        $message

                    </td>
                </tr>
                <tr>
                    <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                        Copyright 'The Pool', Anthony Harris,  2014<br/>
                    </td>
                </tr>
            </table>
        </div>";

    return Core::sendEmail('The Pool - Forgotten Password', $emailMessage, $POST['email']);

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