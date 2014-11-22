<?php

class teams extends Logos_MySQL_Object{

    public $team_name;
    public $city;
    public $abbr;
    public $conference;
    public $division;
    public $wins;
    public $losses;
    public $games;

    public function getRecentGames($limit = 6){

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare("SELECT * FROM game WHERE (home_team = :team_id OR away_team = :team_id) AND date < CURDATE() ORDER BY date DESC LIMIT :lim");

            $query->bindValue(':lim', (int) $limit, PDO::PARAM_INT);
            $query->bindValue(':team_id', (int) $this->id, PDO::PARAM_INT);

            $query->execute();

            $objects = $query->fetchAll(PDO::FETCH_CLASS, 'game');

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        $this->recentGames = $objects;

        return $objects;

    }

    public static function getTeamsList(){

        $teams = self::query(["orderBy" => "team_name ASC"])->getList();

        if(!is_bool($teams)){
            foreach($teams as $value){
                $value->image_url = "./_storage/teams/{$value->id}/logo_150.png";
                $value->image_url_full = "./_storage/teams/{$value->id}/logo.png";
                $tempStore[$value->id] = $value;
            }
        }

        return isset($tempStore) ? $tempStore : false;

    }

}