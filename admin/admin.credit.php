<?php

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$submitType = intval($_POST['submitType']);

    if(!isset($_POST['className'])){

        echo  'Class Name Error';

        exit;
    }

    $objectType = $_POST['className'];

   if($submitType == 2){

        $newPage = new $objectType($_POST);

        if($newPage->remove())
            $result = "Successfully deleted $objectType";
        else
            $result = "Unable to deleted $objectType";

       echo $result;

       exit;

   }



}
