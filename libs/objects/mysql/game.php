<?php


class game extends Logos_MySQL_Object{

    public $home_team;
    public $away_team;
    public $date;
    public $home_score;
    public $away_score;
    public $season_id;
    public $week_id;

    public function updateGame(){

        $winner = $this->getWinner();

        if($winner !== null){
            $picks = new pick;
            $picks = $picks->getList(null, array("game_id" => $this->id));

            if(!is_bool($picks)){
                foreach($picks as $value){

                    $value->result = ($value->team_id == $winner) ? 1 : 0;

                    if($value->update() === false)
                        return false;
                }
            }
        }

        return $this->update();

    }

    public function played($id = null){

        if($id !== null)
            $this->load($id);

        $now = new DateTime("now");
        $gameDate = new DateTime($this->date);
        $gameDate->setTime(0,0,0);

        return ($now >= $gameDate) ? true : false;

    }

    public function getTeamData($id = null){

        if($id !== null)
            $this->load($id);

        $game['away'] = new teams($this->away_team);
        $game['home'] = new teams($this->home_team);

        return $game;

    }

    public function getWinner(){

        if($this->played())
            return ($this->home_score > $this->away_score) ? $this->home_team : (($this->home_score < $this->away_score) ? $this->away_team : null);
        else
            return false;

    }

    public function isLocked($id = null){

        if($id !== null)
            $this->load($id);

        $now = new DateTime("now");
        $gameLock = new DateTime($this->getLockTime());

        return ($now >= $gameLock) ? true : false;
    }

    public function getLockTime(){

        $tempDate = new DateTime($this->date);
        $tempDate->setTime(0,0,0);
        $dayCheck =  $tempDate->format('D');

        if(strpos($dayCheck,'Thu') !== false){

            $tempDate->add(new DateInterval('PT20H')); //should be 6pm EST on that given game date

        }else if(strpos($dayCheck,'Fri') !== false){

            $tempDate->add(new DateInterval('PT20H')); //should be 6pm EST on that given game date

        }else if(strpos($dayCheck,'Sat') !== false){

            $tempDate->add(new DateInterval('PT13H')); //should be 1pm EST on that given game date

        }else if(strpos($dayCheck,'Sun') !== false){

            $tempDate->add(new DateInterval('PT13H')); //should be 1pm EST on that given game date

        }else if(strpos($dayCheck,'Mon') !== false){

            $tempDate->sub(new DateInterval('PT11H')); //should be 1pm EST on the sunday before //$tempDate->sub();

        }else{

            $tempDate->add(new DateInterval('PT13H')); //should be 1pm EST on that given game date

        }

        $tempDate->sub(new DateInterval(self::getDSTOffset()));

        return $tempDate->format('Y-m-d H:i:s');

    }

    public static function getDSTOffset(){

        return date('I', time()) ? "PT0H" : "PT1H";

    }

    public static function nextGame(){

        $className = get_called_class();

        $now = new DateTime("now", Core::getTimezone());

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare("SELECT * FROM $className WHERE date >= :today AND season_id = :season ORDER BY date ASC LIMIT 1");

            $query->execute(array(":today" => $now->format("Y-m-d"), ":season" => season::getCurrent()->id));

            $object = $query->fetchObject($className);

            return $object;

        }catch(PDOException $pe) {

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;
    }

}
