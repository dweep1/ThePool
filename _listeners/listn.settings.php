<?php

include_once "./listn.header.php";

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

        if($result === -1)//wrong password
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Incorrect Password" : $_SESSION['result'];
        else if($result === -2)//changed password was bad
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

        if($result === -1)//wrong password
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

        if($rival->erase()){
            return -3;
        }

        return -4;

    }

    $identifier = $POST['username'];

    $user = new users;

    if(!$user->load($identifier, 'email')){
        if(!$user->load($identifier, 'username'))
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

        Core::sendEmail('The Pool - Email Address Change', $message, $user->email);
    }

    unset($POST['password']);
    unset($POST['email']);

    $response = ($user->updateObject($POST)) ? $user->update() : -3;

    if($response === true)
        $_SESSION['user'] = $user->toArray();

    return $response;


}

?>