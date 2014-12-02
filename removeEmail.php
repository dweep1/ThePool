<?php

    include "./bootstrap.php";

    if(isset($_GET['email'])){

        $user = new users;

        if($user->load($_GET['email'], "email")){

            $user->disable_notes = 1;

            if($user->update())
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "You have been removed from the mailing list" : $_SESSION['result'];
            else
                $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Error updating user. Please report this to the admin" : $_SESSION['result'];


        }else{
            $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Invalid Email/User doesn't exist" : $_SESSION['result'];
        }

    }else
        $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Email not set" : $_SESSION['result'];

    header("Location: ./index.php");

?>