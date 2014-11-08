<?php

class pick extends Logos_MySQL_Object{

    public $season_id;
    public $week_id;
    public $game_id;
    public $team_id;
    public $user_id;
    public $date;
    public $value = 0;
    public $result = -1;

    public static function getPickCount($week_id = null, $user_id = null, $complete = false){

        if($week_id === null)
            $week_id = week::getCurrent()->id;

        if($user_id === null)
            $user_id = (users::returnCurrentUser()) ? users::returnCurrentUser()->id : false;

        $prepare = "SELECT COUNT(*) AS pick_count FROM pick WHERE week_id = :week_id AND user_id = :user_id";

        if($complete === true){
            $prepare .= " AND value > 0";
        }

        if($user_id === false)
            return 0;

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare($prepare);

            $query->execute(array(":week_id" => $week_id, ":user_id" => $user_id));

            $object = $query->fetch(PDO::FETCH_ASSOC);

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return (isset($object['pick_count']) && $object !== false) ? $object['pick_count'] : false;

    }


}
