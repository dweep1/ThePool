<?php 

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(!isset($_POST['password']) || $_POST['password'] == "Password"){
        echo('please enter a password');
        exit;
    }

    $user = users::returnCurrentUser();

    if($user->doAuth($_POST['password'], 0) !== false){

        $_SESSION['user'] = $user->toArray();

        echo("Location: ./admin.php");
        exit;

    }else{

        echo('an error has occurred');
        exit;

    }

}

?>