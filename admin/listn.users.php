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

    if(!isset($_POST['className'])){
        $_SESSION['result'] = 'Class Name Error';

        header("Location: ./index.php");

        exit;

    }

    $objectType = $_POST['className'];

    if(isset($_POST['password']) && strlen($_POST['password']) > 2){
        $password = new Password($_POST['password']);

        $_POST['password'] = $password->getKey();
        $_POST['salt'] = $password->getSalt();
    }else{

        unset($_POST['password']);

    }

    //new users
	if($submitType == 0){

        if(users::verifyRegInfo($_POST)){

            $_POST['username'] = explode("@", $_POST['email'])[0];
            $_POST['security_key'] = Cipher::getRandomKey(16);
            $_POST['auth_key'] = Cipher::getRandomKey(16);

            $newPage = new $objectType($_POST);

            if($newPage->save())
                $_SESSION['result'] = "Successfully added new $objectType";
            else
                $_SESSION['result'] = "Unable to add new $objectType";

        }else{

            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Email/Password was invalid" : $_SESSION['result'] ;

        }

        header("Location: ./index.php");

        exit;

    }else if($submitType == 1){

        $newPage = new $objectType($_POST);

        if($newPage->update())
            $_SESSION['result'] = "Successfully saved $objectType";
        else
            $_SESSION['result'] = "Unable to update $objectType";

        header("Location: ./index.php");

        exit;

    }else if($submitType == 2){

        $newPage = new $objectType($_POST);

        if($newPage->erase())
            $_SESSION['result'] = "Successfully deleted $objectType";
        else
            $_SESSION['result'] = "Unable to deleted $objectType";

        header("Location: ./index.php");

        exit;

    }



}
