<?php

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$submitType = intval($_POST['submitType']);

    if(!isset($_POST['className'])){
        $_SESSION['result'] = 'Class Name Error';

        header("Location: ./index.php");

        exit;

    }

    $objectType = $_POST['className'];

    //new admin_pages
	if($submitType == 0){

        $newPage = new $objectType($_POST);

        if($newPage->createNew())
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

        $newPage = new $objectType($_POST);

        if($newPage->remove())
            $_SESSION['result'] = "Successfully deleted $objectType";
        else
            $_SESSION['result'] = "Unable to deleted $objectType";

        header("Location: ./index.php");

        exit;

    }



}
