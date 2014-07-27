<?php


class users extends DatabaseObject{

    public $username;
    public $email;
    public $password;
    public $salt;
    public $auth_key; //key that provides auth login
    public $first_name;
    public $last_name;
    public $last_ip;
    public $last_login_date;
    public $user_level; //if user is admin, user level = -1
    public $security_key; //key associated with lost passwords
    public $favorite_team_id;
    public $login_count;
    public $access_level; //if the user has verified their email and is not banned

    protected function classDataSetup(){ }

    //checks to see if a given password is legit
    public function verifyLogin($password){

        if(!class_exists('Password')){
            global $ROOT_DB_PATH;
            @include_once "{$ROOT_DB_PATH}security.php";
        }

        $passwordCheck = new Password($this->password, array('salt' => $this->salt, 'hashed' => true));

        return $passwordCheck->checkPassword($password);

    }

    //@TODO permissions system syncing
    public function verifyAuth(){

        if(!isset($_SESSION['auth_key']))
            return false;

        if($this->auth_key !== $_SESSION['auth_key'])
            return false;

        if($this->expireAuth())
            return false;

        return true;

    }

    public function verifyAdmin(){

        if(!isset($_SESSION['admin_key']))
            return false;

        if($this->auth_key !== $_SESSION['admin_key'])
            return false;

        if($this->expireAuth())
            return false;

        if($this->user_level === -1)
            trigger_error("You are not authorized to view this page");

        return true;

    }

    public function doAuth($password, $level = -1){

        if((int) $level === 0){
            if($this->verifyAdmin())
                return true;
        }else{
            if($this->verifyAuth())
                return true;
        }

        if($this->verifyLogin($password) === false)
            return false;

        $_SESSION['auth_key'] = $this->auth_key = Cipher::getRandomKey();

        if((int) $level === 0)
            $_SESSION['admin_key'] = $this->auth_key;

        $this->last_login_date = strtotime("now");
        $this->last_ip = $_SERVER['REMOTE_ADDR'];
        $this->login_count++;

        return ($this->update() !== false) ? true : false;

    }

    public static function deAuth(){

        session_unset();

        return true;

    }

    public function expireAuth($days = 7){

        $expireDate = new DateTime($this->last_login_date, Core::getTimezone());
        $expireDate->add(new DateInterval("P{$days}D"));

        $today = new Datetime("now", Core::getTimezone());

        return ($today >= $expireDate) ? self::deAuth() : false;

    }

    public static function returnCurrentUser(){
        return isset($_SESSION['user']) ? new users($_SESSION['user']) : false;
    }

    /*
     * Returns the player rank for the indicated week. if Week is -1, then
     * returns the player rank data for the season.
     *
     * @todo rewrite this to use pick data instead, using possible GROUP BY user_id clause
     */
    public static function getPlayerRanking($user_id = null, $week_id = -1, $season_id = -1){

        $season_id = (!isset($season_id) || (int) $season_id == -1) ? season::getCurrent()->id : $season_id;
        $user_id = (!isset($user_id) || $user_id === null) ? users::returnCurrentUser()->id : $user_id;

        $prepare = "SELECT SUM(value) as value, user_id FROM pick WHERE season_id = :season_id";

        if($week_id !== -1){
            $prepare .= " AND week_id = :week_id";
            $execArray[":week_id"] = $week_id;
        }

        $prepare .= " AND result = 1 GROUP BY user_id ORDER BY value DESC";

        $execArray[":season_id"] = $season_id;

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare($prepare);

            $query->execute($execArray);

            $object = $query->fetchAll(PDO::FETCH_ASSOC);


            if($object !== false){

                $rankCount = 0;

                forEach($object as $value){
                    $rankCount++;

                    if($value['user_id'] == $user_id){
                          return $rankCount;
                    }

                }
            }

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;

    }
    /*
     * Returns the player's ranks in a list for a given season
     */
    public static function getPlayerRankData($user_id = -1, $season_id = -1){

        $user_id = ($user_id == -1) ? users::returnCurrentUser()->id : $user_id;
        $season_id = ($season_id == -1) ? season::getCurrent()->id : $season_id;

        $query = DB::sql("SELECT * FROM stat_log WHERE stat_id = 6 AND season_id = '$season_id' AND user_id = '$user_id' ORDER BY week_id ASC");

        if(DB::sql_row_count($query) > 0){

            $array = array();

            while($result = DB::sql_fetch($query)){

                $array[$result['week_id']] = $result;

            }

            return $array;
        }

        return false;

    }

}

/**
 * The Class basis of a dated object, with a start and end date.
 */
class event extends DatabaseObject{

    public $date_start;
    public $date_end;

    /**
     * Loads a selected week from the database
     * @param Int $id is the ID of the selected object which to load from a DB
     * @return Object of selected class name;
     * @throws PDO error if database is unreachable
     */
    public static function selected($id = null){

        $className = get_called_class();

        if($id === null){
            $selected = (isset($_SESSION['selected_'.$className])) ? new $className($_SESSION['selected_'.$className]) : false;
        }else{
            $selected = new $className($id);
            $_SESSION['selected_'.$className] = $selected->toArray();
        }

        return $selected;

    }

    public static function getCurrent(){

        $className = get_called_class();

        $now = new DateTime("now", Core::getTimezone());

        if(isset($_SESSION['current_'.$className])){

            $object = new $className($_SESSION['current_'.$className]);

            if($object->date_end >= $now)
                return $object;

        }

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare("SELECT * FROM $className WHERE DATE(date_end) >= :today AND DATE(date_start) <= :today ORDER BY date_end LIMIT 1");

            $query->execute(array(":today" => $now->format("Y-m-d")));

            $object = $query->fetchAll(PDO::FETCH_CLASS, $className);

        }catch(PDOException $pe) {

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        if(isset($object) && is_object($object)){
            $_SESSION['current_'.$className] = $object->toArray();
            return $object;
        }

        return false;

    }

}

class season extends event{

    public $year;
    public $game_count;
    public $text_id;
    public $week_count;

    public function createNew(){

    }

    public static function getCurrent(){
        $instance = new season(3);
        return $instance;
    }

}

class week extends event{

    public $season_id;
    public $week_number;

    public static function getCurrent(){
        $instance = new week(29);
        return $instance;
    }

    public function getStructured($user_id = null, $noIndex = false){

        $this->games = $this->getGames($noIndex);

        $picks = $this->getPicks($user_id);
        $teams = teams::getTeamsList();

        $this->week_score = stat_log::getUserStats(6, array('week_id' => $this->id));
        $this->week_rank = Core::formatNumber(users::getPlayerRanking(null, $this->id));

        $this->total_score = stat_log::getUserStats(6);
        $this->total_rank = Core::formatNumber(users::getPlayerRanking());

        if(!is_bool($this->games)){
            foreach($this->games as $key => $val){

                $this->games[$key]->away_team = $teams[$this->games[$key]->away_team];
                $this->games[$key]->home_team = $teams[$this->games[$key]->home_team];

                $gameDate = new DateTime($this->games[$key]->date, Core::getTimezone());
                $gameDate->setTime(0,0,0);
                $gameDate = $gameDate->format('l');

                $this->games[$key]->date_name = $gameDate;

                if(isset($picks[$val->id]))
                    $this->games[$key]->pick = $picks[$val->id];
                else{
                    $user = users::returnCurrentUser();

                    $dataArray = array("season_id" => $this->season_id, "week_id" => $this->id, "game_id" => $val->id
                                      ,"team_id" => -1, "user_id" => $user->id, "value" => 0, "result" => -1);

                    $newPick = new pick($dataArray);

                    $this->games[$key]->pick = $newPick;
                }

            }
        }
    }

    public function getGames($noIndex = false){

        $games = new game();
        $tempStore = [];

        $games = $games->getList("week_id asc", array("week_id" => $this->id));

        foreach($games as $value){

            if((int) $value->home_team !== 0)
                $tempStore[$value->id] = $value;

        }

        if($noIndex)
            $tempStore = array_values($tempStore);

        return (!is_bool($games)) ? ((isset($tempStore)) ? $tempStore : false ) : $games;

    }

    public function getPicks($user_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        $picks = new pick;
        $picks = $picks->getList("game_id asc", array("week_id" => $this->id, "user_id" => $user_id));

        if(!is_bool($picks)){
            foreach($picks as $value){
                $value->value = (int) $value->value;
                $tempStore[$value->game_id] = $value;
            }
        }

        return (!is_bool($picks)) ? ((isset($tempStore)) ? $tempStore : false ) : $picks;

    }

}

class credit extends DatabaseObject{

    public $date;
    public $user_id;
    public $nid;
    public $week_id = -1;

    public static function useCredit($user_id = null, $week_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        if($week_id === null)
            $week_id = week::getCurrent()->id;

        $credit = self::validCredit($user_id, $week_id);

        if($credit !== false){
            return true;
        }else{

            $credit = self::validCredit($user_id, -1);

            if($credit !== false){

                $elem = false;

                foreach($credit as $value){
                    $elem = $value;
                }

                $elem->week_id = $week_id;
                return $credit->update();
            }

        }

        return false;

    }

    public static function validCredit($user_id = null, $week_id = null){

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        if($week_id === null)
            $week_id = week::getCurrent()->id;

        return self::returnInstance([$user_id, $week_id], array("type" => ["user_id", "week_id"]));

    }

    public static function generateCredit($data){

        $newInstance = new self($data);

        return $newInstance->save();

    }


}

class admin_pages extends DatabaseObject{

    public $title;
    public $icon;
    public $template_file;
    public $permission_level;
    public $parent_id;

}

class bugs extends DatabaseObject{

    public $ip_address;
    public $browser;
    public $page_header;
    public $report;
    public $email;
    public $date;

}

class rivals extends DatabaseObject{

    public $user_id;
    public $rival_id;
    public $rival_name;
    public $rival_custom_name;

}

class game extends DatabaseObject{

    public $home_team;
    public $away_team;
    public $date;
    public $home_score;
    public $away_score;
    public $season_id;
    public $week_id;

    public function played($id = null){

        if($id !== null)
            $this->load($id);

        $now = new DateTime("now", Core::getTimezone());
        $now->add(new DateInterval("P1D"));

        return ($this->date <= $now) ? false : true;

    }

    public function getTeamData($id = null){

        if($id !== null)
            $this->load($id);

        $game['away'] = new teams($this->away_team);
        $game['home'] = new teams($this->home_team);

        return $game;

    }

}

class options extends DatabaseObject{

    public $name;
    public $value;

}

class pick extends DatabaseObject{

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
            $week_id = week::getCurrentWeek();

        if($user_id === null)
            $user_id = users::returnCurrentUser();

        $prepare = "SELECT COUNT(*) AS pick_count FROM pick WHERE week_id = :week_id AND user_id = :user_id";

        if($complete === true){
            $prepare .= " AND value > 0";
        }

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare($prepare);

            $query->execute(array(":week_id" => $week_id, ":user_id" => $user_id));

            $object = $query->fetch(PDO::FETCH_ASSOC);

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return (isset($object) && $object !== false) ? $object['pick_count'] : false;

    }


}

class stat_log extends DatabaseObject{

    public $season_id;
    public $week_id;
    public $team_id;
    public $user_id;
    public $stat_id;
    public $stat_var;
    public $note;

    //
    //
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

    public static function getUserStats($stat_id, $dataArray = array('user_id' => -1, 'week_id' => -1, 'team_id' => -1, 'season_id' => -1)){

        $dataArray['user_id'] = (!isset($dataArray['user_id']) || $dataArray['user_id'] == -1) ? @users::returnCurrentUser()->id : $dataArray['user_id'];
        $dataArray['season_id'] = (!isset($dataArray['season_id']) || $dataArray['season_id'] == -1) ? @season::getCurrent()->id : $dataArray['season_id'];

        $prepare = "";
        $execArray = [];

        if($stat_id == 6){//user point totals

            if(!isset($dataArray['week_id']) || $dataArray['week_id'] == -1){//global point total

                $prepare = "SELECT SUM(value) as value FROM pick WHERE user_id = :user_id AND season_id = :season_id AND result = 1";
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

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare($prepare);

            $query->execute($execArray);

            $object = $query->fetch(PDO::FETCH_ASSOC);

            return (isset($object['value'])) ? $object['value'] : false;

        }catch(PDOException $pe){

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        return false;

    }

}

class teams extends DatabaseObject{

    public $team_name;
    public $city;
    public $abbr;
    public $conference;
    public $division;
    public $wins;
    public $losses;
    public $games;

    public static function getTeamsList(){

        $instance = new self();

        $teams = $instance->getList();

        if(!is_bool($teams)){
            foreach($teams as $value){
                $value->image_url = "./_storage/teams/{$value->id}/logo_150.png";
                $tempStore[$value->id] = $value;
            }
        }

        return ((isset($tempStore)) ? $tempStore : false );

    }

}


?>