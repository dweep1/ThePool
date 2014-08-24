<?php


class users extends DatabaseObject{

    public $username;
    public $email;
    public $paypal;
    public $password;
    public $salt;
    public $auth_key; //key that provides auth login
    public $first_name;
    public $last_name;
    public $last_ip;
    public $last_login_date;
    public $user_level = null; //if user is admin, user level = 0
    public $security_key; //key associated with lost passwords
    public $favorite_team_id;
    public $login_count;
    public $access_level; //if the user wants to be seen or not, -1 is invisible
    public $credits;
    public $disable_notes;
    public $pay_key;

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

    public static function verifyRegInfo($POST){

        $errors = 0;
        $password = $POST['password'];

        foreach($POST as $key => $value){

            if($key === "username"){
                if(filter_var($value, FILTER_SANITIZE_STRING) == false ||  strlen($value) < 3 ){
                    $errors++;
                    $_SESSION['result'] = 'Invalid Username. Usernames must be longer then 3 characters';
                }
            }

            if($key === "email"){
                if(filter_var($value, FILTER_VALIDATE_EMAIL) == false){
                    $errors++;
                    $_SESSION['result'] = 'Invalid Email Address';
                }
            }

            if($key === "password"){
                $password = $value;
                if(0 === preg_match("/.{6,}/", $value)){
                    $errors++;

                    $_SESSION['result'] = 'Invalid Password. Must contain at least 6 characters';
                }
            }

            if($key === "confirm"){
                if(0 !== strcmp($password, $value)){
                    $errors++;
                    $_SESSION['result'] = 'Passwords do not match';
                }
            }
        }

        return ($errors > 0) ? false : true;

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

        if($this->user_level === -1 && $level === 0)
            return false;

        $_SESSION['auth_key'] = $this->auth_key = Cipher::getRandomKey();

        if($level === 0)
            $_SESSION['admin_key'] = $this->auth_key;

        $this->last_login_date = Core::unixToMySQL("now");
        $this->last_ip = $_SERVER['REMOTE_ADDR'];
        $this->login_count++;

        if(strlen($this->pay_key) <= 1)
            $this->pay_key = Cipher::getRandomKey(16);

        return ($this->update() !== false) ? true : false;

    }

    public static function deAuth(){

        foreach($_SESSION as $key => $value){

            if($key !== "result" || $key !== "Result" || $key !== "RESULT")
                unset($_SESSION[$key]);

        }

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

    public static function getFilteredUserList(){

        $users = new users;

        $users = $users->getList();

        foreach($users as $key => $value){

            if((int) $value->access_level === -1 && (int) $value->id !== (int) self::returnCurrentUser()->id){
                unset($users[$key]);
            }else{
                $users[$key]->last_ip = $users[$key]->last_login_date = $users[$key]->user_level = $users[$key]->security_key = null;
                $users[$key]->password = $users[$key]->salt = $users[$key]->auth_key = $users[$key]->login_count = null;
                $users[$key]->access_level = $users[$key]->credits = $users[$key]->email = $users[$key]->paypal = null;
            }

        }

        return $users;

    }

    public function getRivals(){

        $rivals = new rivals();

        return $rivals->getList(null, array("user_id" => $this->id));

    }

}

/**
 * The Class basis of a dated object, with a start and end date.
 */
class event extends DatabaseObject{

    public $date_start;
    public $date_end;

    public static function getCurrent(){

        $className = get_called_class();

        $now = new DateTime("now", Core::getTimezone());

        if(isset($_SESSION['current_'.$className])){

            $object = new $className($_SESSION['current_'.$className]);

            $dateEnd = new DateTime($object->date_end, Core::getTimezone());

            if($className == "season")
                $dateEnd->add(new DateInterval("P14D"));

            if($dateEnd >= $now)
                return $object;

        }

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare("SELECT * FROM $className WHERE DATE(date_end) >= :today AND DATE(date_start) <= :today ORDER BY date_end LIMIT 1");

            $query->execute(array(":today" => $now->format("Y-m-d")));

            $object = $query->fetchObject($className);

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

    /*public static function getCurrent(){
        return new season(3);
    }*/

    public function createNew(){

        if($this->save() === false)
            return false;

        $date = new DateTime($this->date_start);

        for($i = 0; $i < $this->week_count; $i++){

            $start = $date->format('Y-m-d H:i:s');
            $date->modify('next monday');
            $end = $date->format('Y-m-d H:i:s');
            $number = $i + 1;

            $tempWeek = new week(array("date_start" => $start, "date_end" => $end, "week_number" => $number, "season_id" => $this->id));

            if($tempWeek->save() === false)
                return false;

            $gameDate = new DateTime($end);
            $gameDate->modify('-1 Day');
            $gameDate = $gameDate->format('Y-m-d H:i:s');

            for($k = 0; $k < ($this->game_count/$this->week_count); $k++){

                $tempGame = new game(array("week_id" => $tempWeek->id, "season_id" => $this->id, "date" => $gameDate));

                if($tempGame->save() === false)
                    return false;

            }

        }

        return true;

    }

    public function deleteSeason(){

        $weeks = new week();
        $weeks = $weeks->getList(null, array("season_id" => $this->id));

        if(is_bool($weeks))
            return false;

        foreach($weeks as $week){

            $games = new game();
            $games = $games->getList(null, array("week_id" => $week->id));

            if(is_bool($games))
                return false;

            foreach($games as $game){
                if($game->erase() === false)
                    return false;

            }

            if($week->erase() === false)
                return false;

        }

        return $this->erase();

    }

}

class week extends event{

    public $season_id;
    public $week_number;

    /*public static function getCurrent(){
        return new week(28);
    }*/

    public function getGameCount(){

        $games = $this->getGames(true);

        return count($games);

    }

    public static function getNextLock($offset){

        $game = game::nextGame();

        if($game === false)
            return false;

        while($game->isLocked() === true){

            if(strpos(Core::getDay($game->date),'Mon') !== false)
                break;


            $next = $game->getNext();

            if($next === false)
                return false;

            if((int) $next->week_id !== (int) $game->week_id)
                break;
            else
                $game = $next;

        }

        $gameLock = $game->getLockTime();

        return self::getTimezoneLockTime($gameLock, $offset);

    }

    public static function getTimezoneLockTime($gameDate, $offset){

        $newOffset = (240 - $offset)/60;

        $EST = Core::getTimezone();

        $game = new DateTime($gameDate, $EST);

        if($newOffset >= 0){
            $game->add(new DateInterval("PT{$newOffset}H"));
        }else{
            $newOffset = $newOffset*(-1);
            $game->sub(new DateInterval("PT{$newOffset}H"));

        }

        return $game->format('Y-m-d H:i:s');

    }

    public function getStructured($user_id = null, $noIndex = false){

        $this->games = $this->getGames($noIndex);

        $picks = $this->getPicks($user_id);
        $teams = teams::getTeamsList();

        $tempRanking = users::getPlayerRanking(null, $this->id);

        $this->week_score = stat_log::getUserStats(6, array('week_id' => $this->id))[0]['value'] ?: 0;
        $this->week_rank = Core::formatNumber((((int) $tempRanking > 0) ? $tempRanking : "N/A" ));

        $tempRanking = users::getPlayerRanking();

        $this->total_score = stat_log::getUserStats(6)[0]['value'] ?: 0;
        $this->total_rank = Core::formatNumber((((int) $tempRanking > 0) ? $tempRanking : "N/A" ));

        if(!is_bool($this->games)){
            foreach($this->games as $key => $val){

                $this->games[$key]->gameLock = $this->games[$key]->isLocked();

                $this->games[$key]->away_team = $teams[$this->games[$key]->away_team];
                $this->games[$key]->home_team = $teams[$this->games[$key]->home_team];

                $this->games[$key]->away_score = (int) $this->games[$key]->away_score;
                $this->games[$key]->home_score = (int) $this->games[$key]->home_score;

                $gameDate = new DateTime($this->games[$key]->date, Core::getTimezone());
                $gameDate->setTime(0,0,0);

                $this->games[$key]->date_name = $gameDate->format('l');
                $this->games[$key]->display_date = $gameDate->format('D, m/d');

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

        $games = $games->getList("id asc", array("week_id" => $this->id));

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
    public $week_id = -1;
    public $nid;
    public $amount;

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

        $useCredit = new options();
        $useCredit->load("use_credit", "name");

        if((int) $useCredit->value <= 0)
            return true;

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
    public $order_weight;

}

class bugs extends DatabaseObject{

    public $ip_address;
    public $browser;
    public $page_header;
    public $report;
    public $email;
    public $date;

}

class admin_log extends DatabaseObject{

    public $type = "error";
    public $subject;
    public $log_data;
    public $location;

    //admin_log::generateLog(array( "type" => "", "subject" => "", "log_data" => "", "location" => $_SERVER['REQUEST_URI']))

    public static function generateLog($data){

        $newInstance = new self($data);

        return $newInstance->save();

    }

}

class rivals extends DatabaseObject{

    public $user_id;
    public $rival_id;
    public $rival_name;
    public $rival_custom_name = "";

    public static function findRival($userID, $rivalsList){

        foreach($rivalsList as $value){
            if((int)$userID === (int)$value->rival_id)
                return $value;
        }

        return false;
    }

}

class game extends DatabaseObject{

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

                    $value->result = (int) $value->team_id === (int) $winner ? 1 : 0;

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

        $now = new DateTime("now", Core::getTimezone());
        $gameDate = new DateTime($this->date, Core::getTimezone());
        $gameDate->add(new DateInterval("P1D"));

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

        $now = new DateTime("now", Core::getTimezone());
        $gameLock = new DateTime($this->getLockTime(), Core::getTimezone());

        return ($now >= $gameLock) ? true : false;
    }

    public function getLockTime(){

        $tempDate = new DateTime($this->date, Core::getTimezone());
        $tempDate->setTime(0,0,0);
        $dayCheck =  $tempDate->format('D');

        if(strpos($dayCheck,'Thu') !== false){

            $tempDate->add(new DateInterval('PT18H')); //should be 6pm EST on that given game date

        }else if(strpos($dayCheck,'Fri') !== false){

            $tempDate->add(new DateInterval('PT18H')); //should be 6pm EST on that given game date

        }else if(strpos($dayCheck,'Sat') !== false){

            $tempDate->add(new DateInterval('PT13H')); //should be 1pm EST on that given game date

        }else if(strpos($dayCheck,'Sun') !== false){

            $tempDate->add(new DateInterval('PT13H')); //should be 1pm EST on that given game date

        }else if(strpos($dayCheck,'Mon') !== false){

            $tempDate->add(new DateInterval('PT18H')); //should be 6pm EST on that day //$tempDate->sub();

        }else{

            $tempDate->add(new DateInterval('PT12H')); //should be 1pm EST on that given game date

        }

        return $tempDate->format('Y-m-d H:i:s');

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

class stat_log extends DatabaseObject{

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
            FROM pick WHERE result = 1 AND season_id = :season_id GROUP BY user_id ORDER BY total DESC";
        }else{
            $prepare = "SELECT user_id as userID, SUM(value) as total,
            (COUNT(*) / (SELECT COUNT(*) FROM pick WHERE week_id = :week_id AND season_id = :season_id AND result <> -1 AND user_id = userID)) as percent
            FROM pick WHERE week_id = :week_id AND season_id = :season_id AND result = 1 GROUP BY user_id ORDER BY total DESC";
        }

        if((int) $week_id === -1)
            $execArray = array(':season_id' => season::getCurrent()->id);
        else
            $execArray = array(':week_id' => $week_id, ':season_id' => season::getCurrent()->id);

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

    public function getTeamStats(){

        $this->stats = true;

    }

    public static function getTeamsList(){

        $instance = new self();

        $teams = $instance->getList("team_name ASC");

        if(!is_bool($teams)){
            foreach($teams as $value){
                $value->image_url = "./_storage/teams/{$value->id}/logo_150.png";
                $value->image_url_full = "./_storage/teams/{$value->id}/logo.png";
                $tempStore[$value->id] = $value;
            }
        }

        return ((isset($tempStore)) ? $tempStore : false );

    }

}


?>