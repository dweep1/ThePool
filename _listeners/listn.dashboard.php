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

        $currentUser = users::returnCurrentUser();

        //returns a list of users that has sensitive information filtered out

        $currentWeek = week::getCurrent();

        $return['weeks'] = renderWeekData($currentWeek->getList("week_number asc", array("season_id" => season::getCurrent()->id)), $currentWeek);

        $return['users'] = renderUserData(users::getFilteredUserList());

        $return['myUserID'] = $currentUser->id;

        //returns a list of player ranks for global list
        $return['userRank'][0]['list'] = stat_log::getGlobalRankData(-1);
        $return['userRank'][0]['rankData'] = stat_log::getPlayerRank($return['userRank'][0]['list']);

        $rivals = $currentUser->getRivals();

        $count = 0;

        $return['rivals'] = [];

        if(!is_bool($rivals) && count($rivals) > 0){

            foreach($rivals as $rival){

                $return['rivals'][$count] = (int) $rival->rival_id;

                $return['userPicks'][$count]['data'] = renderPointData(stat_log::getPlayerPointData($rival->rival_id), $return['weeks']);
                $return['userPicks'][$count]['title'] = $rival->rival_custom_name;
                $return['userPicks'][$count]['strokeColor'] = "rgba(250,40,30,0.7)";
                $return['userPicks'][$count]['pointColor'] = "rgba(100,40,40,0.7)";
                $return['userPicks'][$count]['pointStrokeColor'] = "#ccc";

                $count++;
            }
        }

        $return['userPicks'][$count]['data'] = renderPointData(stat_log::getGlobalPointData(), $return['weeks']);
        $return['userPicks'][$count]['title'] = "Global Picks";
        $return['userPicks'][$count]['strokeColor'] = "#AAA";
        $return['userPicks'][$count]['pointColor'] = "#3366DD";
        $return['userPicks'][$count]['pointStrokeColor'] = "#AAA";
        $count++;

        //gets the users pick data in terms of points per each week
        $return['userPicks'][$count]['data'] =  renderPointData(stat_log::getPlayerPointData(), $return['weeks']);
        $return['userPicks'][$count]['title'] = "Your Picks";
        $return['userPicks'][$count]['strokeColor'] = "#333";
        $return['userPicks'][$count]['pointColor'] = "rgba(27,206,245, 1)";
        $return['userPicks'][$count]['pointStrokeColor'] = "#111";


        echo json_encode($return);

    }

    function renderWeekData($weekDataArray, $currentWeek){

        $weekDataTemp = [];

        foreach($weekDataArray as $value){
            if($value->date_end <= $currentWeek->date_start)
                array_push($weekDataTemp, $value);
        }

        array_push($weekDataTemp, $currentWeek);

        return $weekDataTemp;

    }

    function renderUserData($userDataArray){

        $userDataTemp = [];

        foreach($userDataArray as $tempUser){
            array_push($userDataTemp, $tempUser);
        }

        return $userDataTemp;

    }

    function renderPointData($pointDataArray, $weekData){

        $pointDataTemp = [];

        foreach($weekData as $week){

            $count = 0;

            foreach($pointDataArray as $pointDataObject){

                if((int)$pointDataObject['week_id'] == (int)$week->id){
                    array_push($pointDataTemp, $pointDataObject);
                    $count++;
                }

            }

            if($count <= 0){
                $pointDataObject = array("week_id" => (int)$week->id, "value" => 0);
                array_push($pointDataTemp, $pointDataObject);
            }

        }

        return $pointDataTemp;

    }


?>