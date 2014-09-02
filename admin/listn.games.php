<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

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

    if($submitType == 1){

        $newPage = new game($_POST);

        if($newPage->updateGame())
            $_SESSION['result'] = "Successfully saved $objectType";
        else
            $_SESSION['result'] = "Unable to update $objectType";

        header("Location: ./index.php");

        exit;

    }else if($submitType == 2){

        $newPage = new game($_POST);
        $newPage->home_team = 0;
        $newPage->away_team = 0;
        $newPage->home_score = 0;
        $newPage->away_score = 0;

        if($newPage->update())
            $_SESSION['result'] = "Successfully marked $objectType as a bye game";
        else
            $_SESSION['result'] = "Unable to update $objectType";

        header("Location: ./index.php");

        exit;

    }



}
