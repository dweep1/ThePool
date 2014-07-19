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

class game extends DatabaseObject{

    public $home_team;
    public $away_team;
    public $date;
    public $home_score;
    public $away_score;
    public $season_id;
    public $week_id;

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
    public $value;
    public $result;

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

class season extends DatabaseObject{

    public $year;
    public $game_count;
    public $text_id;
    public $week_count;
    public $date_start;
    public $date_end;

}

class stat_log extends DatabaseObject{

    public $season_id;
    public $week_id;
    public $team_id;
    public $user_id;
    public $stat_id;
    public $stat_var;
    public $note;
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
    public $total_c_points;

}

class week extends DatabaseObject{

    public $season_id;
    public $date_start;
    public $date_end;
    public $week_number;

    protected function classDataSetup(){ }

    public static function getCurrentWeek(){

        $now = new DateTime("now", Core::getTimezone());
        $now = $now->format("Y-m-d");

        if(isset($_SESSION['current_week'])){

            $object = new week($_SESSION['current_week']);

            if($object->date_end >= $now)
                return $object;

        }

        try {

            $pdo = Core::getInstance();
            $query = $pdo->dbh->prepare("SELECT * FROM week WHERE DATE(date_end) >= :today AND DATE(date_start) <= :today ORDER BY date_end LIMIT 1");

            $query->execute(array(":today" => $now));

            $object = $query->fetchAll(PDO::FETCH_CLASS, __CLASS__);

        }catch(PDOException $pe) {

            trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);

        }

        if(isset($object) && $object !== false){
            $_SESSION['current_week'] = $object;
            return $object;
        }

        return false;

    }

}

?>