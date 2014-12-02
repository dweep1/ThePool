<?php

class stat_log extends Logos_MySQL_Object{

    public $season_id;
    public $week_id;
    public $team_id;
    public $user_id;
    public $stat_id;
    public $stat_var;
    public $note;

    /* statID's
     * 6 = point totals
        * w/ Week_id get that weeks stats
        * w/o week_id get the season stats
     * 15 = team point totals, earned points with a given team over a season
     * 16 = possible points with a given team over a season
     * 1 = The percentage that a user gets their picks rights
        * w/ Week_id get that weeks stats
        * w/o week_id get the season stats
     * 14 = the percentage of points a user gains per N time. total points/possible points
        * w/ Week_id get that weeks stats
        * w/o week_id get the season stats
     */

    public static function getGlobalRankData($week_id = null){

        $week_id = ($week_id === null) ? week::getCurrent()->id : $week_id;

        if((int) $week_id === -1){
            $prepare = "SELECT user_id as userID, SUM(value) as total,
            (COUNT(*) / (SELECT COUNT(*) FROM pick WHERE result <> -1 AND season_id = :season_id AND user_id = userID)) as percent
            FROM pick WHERE result = 1 AND season_id = :season_id GROUP BY user_id ORDER BY total DESC, percent DESC, user_id DESC";
        }else{
            $prepare = "SELECT user_id as userID, SUM(value) as total,
            (COUNT(*) / (SELECT COUNT(*) FROM pick WHERE week_id = :week_id AND result <> -1 AND user_id = userID)) as percent
            FROM pick WHERE week_id = :week_id AND result = 1 GROUP BY user_id ORDER BY total DESC, percent DESC, user_id DESC";
        }

        if((int) $week_id === -1)
            $execArray = array(':season_id' => season::getCurrent()->id);
        else
            $execArray = array(':week_id' => $week_id);

        $query = MySQL_Core::fetchQuery($prepare, $execArray);

        try {

            return $query->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;
    }

    public static function getPlayerRank($sort = null, $week_id = null, $user_id = null){

        $user_id = ($user_id === null) ? @users::returnCurrentUser()->id : $user_id;
        $week_id = ($week_id === null) ? @week::getCurrent()->id : $week_id;

        if($sort === null)
            $sort = self::getGlobalRankData($week_id);

        if(is_bool($sort))
            return false;

        $count = 1;

        $return = false;

        foreach($sort as $value){

            $value['rank'] = $count;

            if($value['userID'] == $user_id)
                $return = $value;

            $count++;

        }

        return $return;

    }

    public static function getPlayerPointData($user_id = -1){

        $dataArray = array("user_id" => $user_id);

        return self::getUserStats(5, $dataArray);

    }

    public static function getGlobalPointData(){

        $prepare = "SELECT week_id, AVG(result) as value FROM
        (SELECT week_id, SUM(value) as result FROM pick WHERE season_id = :season_id AND result = 1 GROUP BY user_id, week_id ORDER BY week_id ASC)
        AS t1 GROUP BY t1.week_id";

        $execArray = array(':season_id' => @season::getCurrent()->id);

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare($prepare);

            $query->execute($execArray);

            $object = $query->fetchAll(PDO::FETCH_ASSOC);

            return $object;

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;

    }

    public static function getUserStats($stat_id, $dataArray = array('user_id' => -1, 'week_id' => -1, 'team_id' => -1, 'season_id' => -1)){

        $dataArray['user_id'] = (!isset($dataArray['user_id']) || $dataArray['user_id'] == -1) ? @users::returnCurrentUser()->id : $dataArray['user_id'];
        $dataArray['season_id'] = (!isset($dataArray['season_id']) || $dataArray['season_id'] == -1) ? @season::getCurrent()->id : $dataArray['season_id'];

        $prepare = "";
        $execArray = [];

        if($stat_id == 5){//user point totals

            $prepare = "SELECT week_id, SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result = 1 GROUP BY week_id";
            $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id']);


        }else if($stat_id == 6){//user point totals

            if(!isset($dataArray['week_id']) || $dataArray['week_id'] == -1){//global point total

                $prepare = "SELECT week_id, SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result = 1";
                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id']);

            }else{//weekly point total

                $prepare = "SELECT SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND week_id = :week_id AND result = 1";
                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id'], ':week_id' => $dataArray['week_id']);

            }


        }else if($stat_id == 15){//team point total

            $prepare = "SELECT SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND team_id = :team_id AND result = 1";
            $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id'], ':team_id' => $dataArray['team_id']);

        }else if($stat_id == 16){//team possible points

            $prepare = "SELECT SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND team_id = :team_id";
            $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id'], ':team_id' => $dataArray['team_id']);

        }else if($stat_id == 1){//pick percentage

            if(!isset($dataArray['week_id']) || $dataArray['week_id'] == -1){

                $prepare = "SELECT ((SELECT COUNT(*) FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result = 1)/ COUNT(*)) AS value
				FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result <> -1";

                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id']);

            }else{

                $prepare = "SELECT ((SELECT COUNT(*) FROM pick WHERE user_id = :user_id AND season_id = :season_id AND week_id = :week_id AND result = 1)/ COUNT(*)) AS value
				FROM pick WHERE user_id = :user_id AND season_id = :season_id AND week_id = :week_id AND result <> -1";

                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id'], ':week_id' => $dataArray['week_id']);

            }

        }else if($stat_id == 14){//point percentage

            if(!isset($dataArray['week_id']) || $dataArray['week_id'] == -1){

                $prepare = "SELECT ((SELECT SUM(value) FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result = 1)/ SUM(value)) AS value
				FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result <> -1";

                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id']);

            }else{

                $prepare = "SELECT ((SELECT SUM(value) FROM pick WHERE user_id = :user_id AND season_id = :season_id AND week_id = :week_id AND result = 1)/ SUM(value)) AS value
				FROM pick WHERE user_id = :user_id AND season_id = :season_id AND week_id = :week_id AND result <> -1";

                $execArray = array(':user_id' => $dataArray['user_id'], ':season_id' => $dataArray['season_id'], ':week_id' => $dataArray['week_id']);

            }

        }

        $query = MySQL_Core::fetchQuery($prepare, $execArray);

        try {

            return $query->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;

    }

}