<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include "./admin.header.php";

    if(!isset($_GET['method']))
        exit;

    if($_GET['method'] === 'GET'){

        $weeks = week::loadMultiple();

        foreach($weeks as $key => $value){
            $weeks[$value->id] = $value;
            unset($weeks[$key]);
        }

        $obj = new credit();

        if((int) $objData->user_id === -1){
            $obj = $obj->getList();
            $users = users::loadMultiple();

            foreach($users as $key => $value){
                $users[$value->id] = $value;
                unset($users[$key]);
            }

        }else{
            $user =  users::loadSingle(["id" => $objData->user_id]);
            $obj = $obj->getList(["user_id" => $user->id]);
        }

        if(is_array($obj)){
            foreach($obj as $key => $value){

                $value->date = displayDate($value->date);

                $value->week_number = ((int) $value->week_id > 0) ? $weeks[$value->week_id]->week_number : "N/A";

                $value->username = ((int) $objData->user_id === -1) ? $users[$value->user_id]->username : $user->username;

                $obj[$key] = $value;
            }
        }

        echo json_encode($obj);

    }

    function displayDate($dateTimeString){
        $tempDate = new DateTime($dateTimeString);
        return $tempDate->format('D, m/d @ h:i a');
    }



?>