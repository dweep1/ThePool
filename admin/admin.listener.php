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


    //new admin_pages
	if($submitType == 0){

        $newPage = new $objectType($_POST);

        if($newPage->save())
            $_SESSION['result'] = "Successfully added new $objectType";
        else
            $_SESSION['result'] = "Unable to add new $objectType";

        header("Location: ./index.php");

        exit;

    }else if($submitType == 1){

        $newPage = new $objectType($_POST);

        if($newPage->update())
            $_SESSION['result'] = "Successfully saved $objectType";
        else
            $_SESSION['result'] = "Unable to add save $objectType";

        header("Location: ./index.php");

        exit;

    }



}
