<?php

if(!session_id()) {
    session_start();
} else {

}

define('SITEKEY', "ce6b20ee7f7797e102f68d15099e7d5b0e8d4c50f98a7865ea168717539ec3aa");


class Cipher {
    private $secureKey;
    private $iv;

    function __construct($textKey, $iv = null) {
        $this->secureKey = hash('sha256',$textKey,TRUE);

        if($iv == null)
            $this->iv = mcrypt_create_iv(32);
        else
            $this->iv = base64_decode($iv);

    }

    function encrypt($input) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->secureKey, $input, MCRYPT_MODE_ECB, $this->iv));
    }

    function decrypt($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->secureKey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }

    function getIV(){
        return base64_encode($this->iv);
    }

}

class FormValidation{

    public function __construct(){

        if(!isset($_SESSION['form_key']))
            self::generate();

    }

    public static function generate(){

        $form = new Password($_SERVER['REMOTE_ADDR'].SITEKEY, array('cost' => 7));

        $_SESSION['form_key'] = $form->getKey();
        $_SESSION['form_salt'] = $form->getSalt();

    }

    public static function validate(){

        if(isset($_SESSION['form_key'])){

            if(isset($_SESSION['form_salt'])){

                $form = new Password($_SESSION['form_key'],  array('cost' => 7, 'salt' => $_SESSION['form_salt'], 'hashed' => true));

                if($form->checkPassword($_SERVER['REMOTE_ADDR'].SITEKEY))
                    return true;

            }

        }

        return false;

    }


}


class Password{

    private $key;
    private $salt;
    private $options;

    public function __construct($key, $options = array('salt' => null, 'cost' => 11, 'hashed' => false)) {

        if(!isset($options['salt']))
            $options['salt'] = null;
        if(!isset($options['cost']))
            $options['cost'] = 11;
        if(!isset($options['hashed']))
            $options['hashed'] = false;

        if($options['salt'] == null)
           $this->generateSalt();
        else
           $this->salt = $options['salt'];

        $this->options = array('cost' => $options['cost'], 'salt' => $this->salt);

        if($options['hashed'] === false)
            $this->generatePassword($key);
        else
            $this->key = $key;

    }

    private function generateSalt(){
        $this->salt = substr(str_replace('+', '.', base64_encode(openssl_random_pseudo_bytes (128))), 0, 22);
    }

    private function generatePassword($password){
        $this->key = password_hash($password, PASSWORD_BCRYPT, $this->options);
    }

    public function checkPassword($password){
        if(password_verify($password, $this->key))
            return true;

        return false;
    }

    public function getKey(){
        return $this->key;
    }

    public function getSalt(){
        return $this->salt;
    }

}

?>