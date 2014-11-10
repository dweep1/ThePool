<?php

    include_once "./_header.php";

    $keyUser = new users();

    $response = false;

    if(isset($_GET['email'])){

        if(isset($_GET['key'])){

            if($keyUser->load($_GET['key'], "security_key")){

                unset($_GET['key']);

                if(users::verifyRegInfo($_GET)){

                    $keyUser->email = $_GET['email'];

                    $response = $keyUser->update();

                }else
                    $response = -1;

            }
        }
    }

    if($result === -1)//email malformed
        $_SESSION['result'] = (!isset($_SESSION['result'])) ? "The email address you submitted was malformed." : $_SESSION['result'];
    else if($result === false)//database error
        $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Database Error" : $_SESSION['result'];
    else if($result === true)//email changes successfully
        $_SESSION['result'] = (!isset($_SESSION['result'])) ? "Email's changed successfully! Please login again using the new email." : $_SESSION['result'];

    header("Location: ./logout.php");


?>