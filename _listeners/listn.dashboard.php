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

        $return['users'] = [];

        foreach(users::getFilteredUserList() as $tempUser){
            array_push($return['users'], $tempUser);
        }

        $return['myUserID'] = $currentUser->id;

        //returns a list of player ranks for global list
        $return['userRank'][0]['list'] = stat_log::getGlobalRankData(-1);
        $return['userRank'][0]['rankData'] = stat_log::getPlayerRank($return['userRank'][0]['list']);


        $rivals = $currentUser->getRivals();

        $count = 0;

        if(!is_bool($rivals) && count($rivals) > 0){

            foreach($rivals as $rival){

                $return['rivals'][$count] = (int) $rival->rival_id;

                $return['userPicks'][$count]['data'] = stat_log::getPlayerPointData($rival->rival_id);
                $return['userPicks'][$count]['title'] = $rival->rival_custom_name;
                $return['userPicks'][$count]['strokeColor'] = "rgba(250,40,30,0.7)";
                $return['userPicks'][$count]['pointColor'] = "rgba(100,40,40,0.7)";
                $return['userPicks'][$count]['pointStrokeColor'] = "#ccc";

                $count++;
            }
        }

        $return['userPicks'][$count]['data'] = stat_log::getGlobalPointData();
        $return['userPicks'][$count]['title'] = "Global Picks";
        $return['userPicks'][$count]['strokeColor'] = "#AAA";
        $return['userPicks'][$count]['pointColor'] = "#3366DD";
        $return['userPicks'][$count]['pointStrokeColor'] = "#AAA";
        $count++;

        //gets the users pick data in terms of points per each week
        $return['userPicks'][$count]['data'] = stat_log::getPlayerPointData();
        $return['userPicks'][$count]['title'] = "Your Picks";
        $return['userPicks'][$count]['strokeColor'] = "#333";
        $return['userPicks'][$count]['pointColor'] = "rgba(27,206,245, 1)";
        $return['userPicks'][$count]['pointStrokeColor'] = "#111";

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