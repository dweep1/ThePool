<?php

class users extends Logos_MySQL_Object{

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

    //checks to see if a given password is legit
    public function verifyLogin($password){

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

        return $this->save() !== false ? true : false;

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

        $today = new Datetime("now");

        return $today >= $expireDate ? self::deAuth() : false;

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

        if($user_id === null)
            $user_id = users::returnCurrentUser()->id;

        $object = stat_log::getGlobalRankData($week_id);

        if($object !== false){

            $rankCount = 0;

            forEach($object as $value){
                $rankCount++;

                if($value['userID'] == $user_id){
                    return $rankCount;
                }

            }
        }

        return false;

    }

    public static function getFilteredUserList(){

        $users = users::newInstance()->getList();

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

        return rivals::loadMultiple(["user_id" => $this->id]);

    }

}