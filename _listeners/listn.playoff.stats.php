<?php

$data = file_get_contents("php://input");
$objData = json_decode($data);

include "./listn.header.php";

$currSeason = season::getCurrent();

if ($currSeason !== false) {
  $prevSeason = $currSeason->getPrevious();
} else {
  $prevSeason = season::getPrevious();
}

$currentSeasonId = $objData->season_id;

if(!is_int($currentSeasonId)) {
  $currentSeasonId = $prevSeason->id;
}

$currentWeek = week::loadSingle(["season_id" => $currentSeasonId, "week_number" => 1]);
$games = game::loadMultiple(["season_id" => $prevSeason->id]);

$teamGames = game::loadMultiple(["week_id" => $currentWeek->id]);
$teams = teams::getTeamsList();
$temp = [];

foreach($teams as $key => $value){
    $temp[$value->id] = $value;
    unset($teams[$key]);
}

$teams = $temp;
unset($temp);

$playoffTeams = [];

foreach($teamGames as $value){

    $playoffTeams[$value->home_team] = $teams[$value->home_team];
    $playoffTeams[$value->away_team] = $teams[$value->away_team];

    $playoffTeams[$value->home_team]->wins = 0;
    $playoffTeams[$value->away_team]->wins = 0;

    $playoffTeams[$value->home_team]->games = 0;
    $playoffTeams[$value->away_team]->games = 0;

}

foreach($games as $value){

    $winner = $value->getWinner();

    if(isset($playoffTeams[$winner]))
        $playoffTeams[$winner]->wins += 1;

    if(isset($playoffTeams[$value->home_team]))
        $playoffTeams[$value->home_team]->games += 1;

    if(isset($playoffTeams[$value->away_team]))
        $playoffTeams[$value->away_team]->games += 1;


}

echo json_encode($playoffTeams);
