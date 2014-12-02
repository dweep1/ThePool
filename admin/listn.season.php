<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$submitType = intval($_POST['submitType']);

    if(!isset($_POST['className'])){
        $_SESSION['result'] = 'Class Name Error';

        header("Location: ./index.php");

        exit;

    }

    $objectType = $_POST['className'];

    //new users
	if($submitType == 0){

        $newSeason = new season($_POST);

        if($newSeason->createNewSeason())
            $_SESSION['result'] = "Successfully added new $objectType";
        else
            $_SESSION['result'] = "Unable to add new $objectType";

        header("Location: ./index.php");

        exit;

    }else if($submitType == 1){

        $newPage = new $objectType($_POST);

        if($newPage->save())
            $_SESSION['result'] = "Successfully saved $objectType";
        else
            $_SESSION['result'] = "Unable to update $objectType";

        header("Location: ./index.php");

        exit;

    }else if($submitType == 2){

        $newSeason = new season($_POST);

        if($newSeason->deleteSeason())
            $_SESSION['result'] = "Successfully deleted $objectType";
        else
            $_SESSION['result'] = "Unable to deleted $objectType";

        header("Location: ./index.php");

        exit;

    }



}
