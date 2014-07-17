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
    public $user_level;
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

        if($this->user_level !== 0)
            trigger_error("You are not authorized to view this page");

        return true;

    }

    public function doAuth($password, $level = 0){

        if((int) $level === -1){
            if($this->verifyAdmin())
                return true;
        }else{
            if($this->verifyAuth())
                return true;
        }

        if($this->verifyLogin($password) === false)
            return false;

        $_SESSION['auth_key'] = $this->auth_key = Cipher::getRandomKey();

        if((int) $level === -1)
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


?>