<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include "./listn.header.php";

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

        foreach($objData as $key => $value){

            if((int) $value->value > count($gamesList) || $value->value < 0){
                $result["result"] .= "The value of a pick was either too high or too low. ";
                $errors++;
            }

            if($value->team_id <= 0){
               unset($objData[$key]);
            }

        }

        if(count($objData) > 0){
            if(!credit::useCredit(null,$currentWeek->id)){

                $result["result"] .= "You don't have a valid credit to use currently. ";
                $errors++;

            }

            if($errors === 0){

                foreach($objData as $value){

                    $savePick = new pick($value);

                    $game = new game($savePick->game_id);

                    if($game->isLocked()){

                        $result["result"] .= "Current Game Is Locked";
                        $errors++;

                    }else{

                        $checkPick = pick::loadSingle(["game_id" => $savePick->game_id, "user_id" => $savePick->user_id]);

                        if((int) $checkPick->id > 0){

                            $savePick->id = $checkPick->id;

                            if($savePick->save() === false){
                                $result["result"] .= "Unable to update old pick. ";
                                $errors++;
                            }

                        }else{

                            if($savePick->createNew() === false){
                                $result["result"] .= "Unable to save new pick. ";
                                $errors++;
                            }

                        }
                    }
                }
            }
        }

        $result["errors"] = $errors;

        $pickCount = pick::getPickCount();
        $gameCount = $currentWeek->getGameCount();

        $pickLeft = $gameCount - $pickCount;

        if($errors === 0){
            $result["result"] .= "Successfully Update Picks! <br/>You have $pickLeft picks remaining.";
        }

        echo json_encode($result);

    }



?>