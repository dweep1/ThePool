<?php

$data = file_get_contents("php://input");
$objData = json_decode($data);

include "./listn.header.php";

if(!isset($_GET['method']))
    exit;

if($_GET['method'] === 'GET'){

    $obj = new week($objData->week_id);
    $currentWeek = week::getCurrent();

    $games = game::loadMultiple(["week_id" => $obj->id]);
    $results["picks"] = pick::loadMultiple(["season_id" => season::getCurrent()->id, "user_id" => users::returnCurrentUser()->id]);

    $teams = teams::getTeamsList();
    $temp = [];

    foreach($teams as $key => $value){
        $temp[$value->id] = $value;
        unset($teams[$key]);
    }

    $teams = $temp;
    unset($temp);

    $results["teams"] = [];

    foreach($games as $value){

        $nullify = false;

        if($obj->id == $currentWeek->id){
            foreach($results["picks"] as $pick){

                if($pick->week_id == $currentWeek->id){
                    $nullify = true;
                }

            }
        }

        if($value->home_team > 0 && $value->away_team > 0 && !$nullify){

            $home = $teams[$value->home_team];
            $away = $teams[$value->away_team];

            $home->vs = ["team_name" => $away->team_name, "id" => $away->id, "city" => $away->city];
            $away->vs = ["team_name" => $home->team_name, "id" => $home->id, "city" => $home->city];

            array_push($results["teams"], $home);
            array_push($results["teams"], $away);

        }

    }

    echo json_encode($results);

}else if($_GET['method'] === 'PUT'){

    $currentWeek = week::getCurrent();

    $gamesList = $currentWeek->getGames();

    $result = array("result" => "");
    $errors = 0;

}