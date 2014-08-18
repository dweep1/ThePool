<?php

    $data = file_get_contents("php://input");
    $objData = json_decode($data);

    global $ROOT_DB_PATH;
    $ROOT_DB_PATH = "../_db/";

    include_once "./admin.header.php";

    if(FormValidation::validate() === false){

        $_SESSION['result'] = 'The Form Could not be validated.<br/>Please enable javascript/cookies';

        header("Location: ../index.php");

        exit;

    }

    if(!isset($_GET['method']))
        exit;

    if($_GET['method'] === 'GET'){

        $obj = week::selected();

        $obj->getStructured($objData->user_id, true);

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

        if($errors === 0){

            foreach($objData as $value){

                $savePick = new pick($value);

                $game = new game($savePick->game_id);

                if($game->getWinner() !== false)
                    $savePick->result = ((int) $savePick->team_id === (int) $game->getWinner()) ? 1 : 0;

                //var_dump($game->getWinner());

                $checkPick = new pick();
                $checkPick->load(array($savePick->game_id, $savePick->user_id), array("type" => array("game_id", "user_id")));

                if((int) $checkPick->id > 0){

                    $savePick->id = $checkPick->id;

                    if($savePick->update() === false){
                        $result["result"] .= "Unable to update old pick. ";
                        $errors++;
                    }

                }else{

                    if($savePick->save() === false){
                        $result["result"] .= "Unable to save new pick. ";
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