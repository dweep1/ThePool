<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    include "./admin.header.php";

    if(!isset($_GET['method']))
        exit;

    if($_GET['method'] === 'GET'){

        $obj = week::selected();

        $obj->getStructured($objData->user_id, true);

        echo json_encode($obj);

    }else if($_GET['method'] === 'PUT'){

        $currentWeek = week::selected();

        $gamesList = $currentWeek->getGames();

        $result = ["result" => ""];
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

            if(credit::useCredit($objData[0]->user_id, $currentWeek->id) !== true){
                $result["result"] .= "Unable to spend credit. There are no valid credits for this user. ";
                $errors++;
            }

            if($errors < 1){

                foreach($objData as $value){

                    $savePick = new pick($value);

                    $game = new game($savePick->game_id);

                    if($game->getWinner() !== false)
                        $savePick->result = ((int) $savePick->team_id === (int) $game->getWinner()) ? 1 : 0;

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

        $result["errors"] = $errors;

        if($errors === 0){
            $result["result"] .= "Successfully Update Picks!";
        }

        echo json_encode($result);

    }



?>