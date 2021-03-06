<?php

$data = file_get_contents("php://input");
$objData = json_decode($data);

include "./admin.header.php";

if(!isset($_GET['method']))
    exit;

if($_GET['method'] === 'GET'){

    $obj = week::selected();

    $games = game::loadMultiple(["week_id" => $obj->id]);
    $results["picks"] = pick::loadMultiple(["season_id" => season::selected()->id, "user_id" => $objData->user_id]);

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
      if($value->home_team > 0 && $value->away_team > 0){
        $home = $teams[$value->home_team];
        $away = $teams[$value->away_team];

        $home->game_id = $value->id;
        $away->game_id = $value->id;

        $home->locked = $value->isLocked();
        $away->locked = $value->isLocked();

        $home->vs = ["team_name" => $away->team_name, "id" => $away->id, "city" => $away->city];
        $away->vs = ["team_name" => $home->team_name, "id" => $home->id, "city" => $home->city];

        array_push($results["teams"], $home);
        array_push($results["teams"], $away);
      }
    }

    echo json_encode($results);

}else if($_GET['method'] === 'PUT'){

    $currentWeek   = week::selected();
    $currentSeason = season::selected();

    $currentUserId = $objData[0]->user_id;

    $gamesList     = $currentWeek->getGames();

    foreach($objData as $value){

        $value->season_id = $currentSeason->id;

        $savePick = new pick($value);

        $game = new game($savePick->game_id);

        $checkPick = pick::loadSingle([
            "team_id" => $savePick->team_id,
            "game_id" => $savePick->game_id,
            "user_id" => $savePick->user_id,
            "week_id" => $currentWeek->id
        ]);

        if(is_object($checkPick) && (int) $checkPick->id > 0){

            $savePick->id = $checkPick->id;

            if($savePick->save() === false){
                $result["result"] .= "Unable to update old pick. ";
                $result["errors"]++;
            }

        }else{

            if($savePick->createNew() === false){
                $result["result"] .= "Unable to save new pick. ";
                $result["errors"]++;
            }

        }
    }

    if($result["errors"] === 0){
        $result["result"] .= "Successfully Update Picks!";
    }

    echo json_encode($result);

}
