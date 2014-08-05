<?php 

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(!isset($_POST['submitType'])){
        fail('Submit Type Not Set');
    }
    
    $submitType = intval($_POST['submitType']);
    
    if($submitType == 0){
    
        if(!isset($_POST['username']) || $_POST['username'] == "Username"){
            fail('please enter your email');
        }
        if(!isset($_POST['pass']) || $_POST['pass'] == "Password"){
            fail('please enter a password');
        }
    
        $auth = user::check_auth($_POST['username'], $_POST['pass']);
    
        if($auth !== false){
    
            if(user::log_in($auth)){
    
                if(isset($_SESSION['trycount'])){
                    unset($_SESSION['trycount']);
                }
    
                if(isset($_SESSION['date'])){
                    unset($_SESSION['date']);
                }
    
                if(isset($_SESSION['redirect_to'])){
                    $location = $_SESSION['redirect_to'];
                    unset($_SESSION['redirect_to']);
                    fail('Location: ' . $location);
                }else{
                    fail("Location: ./admin.php");
                }
    
            }else{
                fail("Unable to Login");
            }
    
        }else{
    
            if(!isset($_SESSION['trycount'])){
                $_SESSION['trycount'] = 0;
            }
    
            $_SESSION['trycount'] += 1;
            error('Invalid Login, '.(6-$_SESSION['trycount']).' tries left');
    
        }
    }

}

function getWait($date, $minuetsAdded){

    $timeRemaining = strtotime("+".$minuetsAdded." minutes", $date) - time();

    if($timeRemaining < 0){
        return false;
    }else{
        return intval(round($timeRemaining/60));
    }
}

function infoError($user){

    if(filter_var($user['username'], FILTER_SANITIZE_STRING) == false ||  strlen($user['username']) < 5 ){
        fail('Invalid Username');
    }

    if(filter_var($user['semail'], FILTER_VALIDATE_EMAIL) == false){
        fail('Invalid Email');
    }

    // Check password is valid
    if(0 === preg_match("/.{6,}/", $user['spass'])){
        fail('Invalid Password');
    }

    // Check password confirmation_matches
    if(0 !== strcmp($user['spass'], $user['cpass'])){
        fail('Passwords do not match');
    }

}

/**
 * Converts a timestamp into a readable time
 * @param unknown_type $timestamp
 * @return Ambigous <string, number>
 */
function convertTime($timestamp){

    $timeElapsed = strtotime($timestamp);

    if(time()-$timeElapsed < 166400){
        $timeElapsed = humanTiming($timeElapsed)." ago";
    }else{
        $timeElapsed = date("M j",$timeElapsed);
    }

    return $timeElapsed;
}

/**
 * Converts a timestamp into a readable time
 * @param unknown_type $timestamp
 * @return Ambigous <string, number>
 */
function humanTiming($time){

    $time = time() - $time; // to get the time since that moment

    $tokens = array (
            31536000 => 'y',
            2592000 => 'm',
            604800 => 'w',
            86400 => 'd',
            3600 => 'hr',
            60 => 'min',
            1 => 'sec'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}

?>