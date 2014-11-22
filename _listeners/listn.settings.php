<?php

include "./listn.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(isset($_SESSION['result'])) unset($_SESSION['result']);

    if((int)$submitType === 0){

        $result = doSettingsChange($_POST);

        if($result === -1){

            if(strlen($POST['confirm_password']) <= 1)//no password
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "You must enter your password in the confirm password box to save changes to your account" : $_SESSION['result'];
            else
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Incorrect Password" : $_SESSION['result'];

            header("Location: ../settings.php?password=error");
            exit;

        }else if($result === -2)//changed password was bad
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Password was poorly formatted" : $_SESSION['result'];
        else if($result === -3)//error updating user object, most likely due to malformed array data
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Couldn't update user's array" : $_SESSION['result'];
        else if($result === false)//database error
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Database Error" : $_SESSION['result'];
        else{
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "User Successfully Updated" : $_SESSION['result'];

        } //success

    }elseif((int)$submitType === 1){

        $result = doRivalChange($_POST);

        if($result === -1)
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Couldn't find a user with given email/username" : $_SESSION['result'];
        else if($result === false)//database error
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Database Error" : $_SESSION['result'];
        else if($result === true)
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Rival Successfully Added" : $_SESSION['result'];
        else if($result === -2)//rival is user
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "You cannot add yourself as a rival" : $_SESSION['result'];
        else if($result === -3)//rival erased
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Rival Removed From List" : $_SESSION['result'];
        else if($result === -4)//couldn't erase rival
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Couldn't Remove Rival" : $_SESSION['result'];
        else//database error
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Magical Error" : $_SESSION['result'];


    } //success

    header("Location: ../settings.php");

}


function doRivalChange($POST){

    if((int)$POST['hiddenID'] !== -1){

        $rival = new rivals($POST['hiddenID']);

        if($rival->remove()){
            return -3;
        }

        return -4;

    }

    $identifier = $POST['username'];

    $user = new users;

    if(!$user->load(['username' => $identifier])){
        if(!$user->load(['email' => $identifier]))
            return -1;//no user found
    }

    $rival = new rivals();

    $rival->user_id = users::returnCurrentUser()->id;
    $rival->rival_id = $user->id;
    $rival->rival_name = $user->username;
    $rival->rival_custom_name = $POST['rival_custom_name'];

    if($rival->user_id == $rival->rival_id)
        return -2;

    if($rival->save())
        return true;

    return false;//db error

}

//@todo finish email error change

function doSettingsChange($POST){

    $user = users::returnCurrentUser();

    if($user->verifyLogin($POST['confirm_password']) === false)
        return -1;

    if(strlen($POST['password']) > 3){

        if(!users::verifyRegInfo($POST))
            return -2;

        $password = new Password($POST['password']);

        $user->password = $password->getKey();
        $user->salt = $password->getSalt();

    }

    if($user->email != $POST['email']){
        $keyFormat = "{$_SERVER['HTTP_HOST']}/email_change.php?key={$user->security_key}&email={$POST['email']}";

        $message = "<br/>You recently updated your account email. This is an email notifing you of the request.
        <br/>If This was you, then click the link below to change your email address.
        <br/>
        <br/>
        <a href='{$keyFormat}'>Click Here</a> change your email.<br/>
        <br/>
        <br/>
        OR Copypaste this into your web browser.
        <br/>
        $keyFormat
        <br/>
        <br/>
        <b>If this was <i>NOT</i> you</b>, then please reset your password, as your account may have been compromised.
        This is an automated response, please do not reply!<br/>";

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
                                Email Address Change
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

        if(Core::sendEmail('The Pool - Email Address Change', $emailMessage, $user->email))
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "In order to complete the change to your email, you must click the link we sent to your old email address." : $_SESSION['result'];
        else
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Unable to send verification email, mail server might be down. Contact an admin if this problem persists." : $_SESSION['result'];
    }

    unset($POST['password']);
    unset($POST['email']);

    $response = $user->save($POST) !== false ? true : -3;

    if($response === true)
        $_SESSION['user'] = $user->toArray();

    return $response;


}

?>