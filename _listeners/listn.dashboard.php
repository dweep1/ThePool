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

        $currentUser = users::returnCurrentUser();

        //returns a list of users that has sensitive information filtered out
        $return['users'] = users::getFilteredUserList();
        $return['myUserID'] = $currentUser->id;

        //returns a list of player ranks for global list
        $return['userRank'][0] = stat_log::getGlobalRankData(-1);

        //gets the global pick data in terms of points per each week
        $return['userPicks'][0]['data'] = stat_log::getGlobalPointData();
        $return['userPicks'][0]['title'] = "Global Picks";
        $return['userPicks'][0]['strokeColor'] = "#AAA";
        $return['userPicks'][0]['pointColor'] = "#3366DD";
        $return['userPicks'][0]['pointStrokeColor'] = "#AAA";

        //gets the users pick data in terms of points per each week
        $return['userPicks'][1]['data'] = stat_log::getPlayerPointData();
        $return['userPicks'][1]['title'] = "User Picks";
        $return['userPicks'][1]['strokeColor'] = "#666";
        $return['userPicks'][1]['pointColor'] = "rgba(27,206,245, 1)";
        $return['userPicks'][1]['pointStrokeColor'] = "#222";

        $currentWeek = week::getCurrent();

        $weekData = $currentWeek->getList("week_number asc", array("season_id" => season::getCurrent()->id));

        $return['weeks'] = [];

        foreach($weekData as $value){
            if($value->date_end <= $currentWeek->date_start)
                array_push($return['weeks'], $value);
        }

        echo json_encode($return);

    }
        /*
    $current_week = pool::getCurrentWeek();
    $previous_week = pool::getPreviousWeek();
    $user_id = user::current_user_id();

    if($previous_week !== false){
        $prev_games = getGamesByWeek($previous_week['id']);
    }else{
        $prev_games = false;
        $previous_week['id'] = $current_week;
    }

    $teams = pool::getBigTeamData();
    unset($teams[0]);

    $players = userstat::getRankingList($previous_week['id']);
    $percentages = userstat::getPercentageList($previous_week['id']);

    $weeks = pool::getWeeksTo($previous_week);

    $ranking = userstat::getPlayerRanking($user_id, $previous_week['id']);

    $userPerformance = userstat::getPlayerRankData();
    $globalPerformance = userstat::getGlobalRankData();

    $userStats = userstat::getAllPlayerStats($user_id);
    $userStats['average'] = userstat::getWeeklyPointAverage();
    $userStats['rank'] = userstat::getPlayerRanking($user_id);

    global $userNameList;

    $userNameList = pool::getUsernameList($type = 'hide');

    echo json_encode(teams::getTeamsList());
*/

?>