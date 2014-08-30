<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";


if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$submitType = intval($_POST['submitType']);

    if(FormValidation::validate() === false){
        echo 'The Form Could not be validated.<br/>Please enable javascript/cookies';
        exit;

    }

    if(!isset($_POST['className'])){

        echo  'Class Name Error';

        exit;
    }

    $objectType = $_POST['className'];

   if($submitType == 2){

        $newPage = new $objectType($_POST);

        if($newPage->erase())
            $result = "Successfully deleted $objectType";
        else
            $result = "Unable to deleted $objectType";

       echo $result;

       exit;

   }



}
