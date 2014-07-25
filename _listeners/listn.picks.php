<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include_once "./listn.header.php";

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(!isset($_GET['method']))
        exit;

    if($_GET['method'] === 'GET'){

        $obj = new week($objData->week_id);

        $obj->getStructured(null, true);

        echo json_encode($obj);

    }else if($_GET['method'] === 'PUT'){

        $currentWeek = week::getCurrent();

        $gamesList = $currentWeek->getGames();

        $result = array("result" => "");
        $errors = 0;

        if(!credit::useCredit(null,$currentWeek->id)){

            //$result["result"] .= "You don't have a valid credit to use currently. ";
            //$errors++;

        }

        foreach($objData as $key => $value){

            if((int) $value->value > count($gamesList) || $value->value < 0){
                $result["result"] .= "The value of a pick was either too high or too low. ";
                $errors++;
            }

            if($value->team_id <= 0){
               unset($objData[$key]);
            }

        }

        if($errors === 0){

            foreach($objData as $value){

                $savePick = new pick($value);

                if($savePick->id === null || $savePick->id < 0){
                    if($savePick->save() === false){
                        $result["result"] .= "Unable to save new pick. ";
                        $errors++;
                    }
                }else{

                    if($savePick->update() === false){
                        $result["result"] .= "Unable to update old pick. ";
                        $errors++;
                    }
                }
            }
        }

        $result["errors"] = $errors;

        if($errors === 0){
            $result["result"] .= "Successfully Update Picks!";
        }

        echo json_encode($result);

    }



?>