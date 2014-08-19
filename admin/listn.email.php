<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";


if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $emailGroup = (int) $_POST['email-group'];

    $emailMessage = "
        <style>
            @import url(http://fonts.googleapis.com/css?family=Open+Sans);

            .email-template h1, .email-template h2, .email-template h3, .email-template h4, .email-template h5{
                font-family: 'Open Sans', sans-serif;
                font-weight:300;
                color:rgb(80,80,80);
                padding:3px 0px;
                margin-right:3px;
                border-bottom:1px solid rgba(120,120,255, 0.9);
            }
        </style>

        <div style='font-family: \"Open Sans\", sans-serif; background: #fff; width:auto; height:100%; padding:10px 20px; text-align: left;'>
            <table cellpadding='0' cellspacing='0' style='width:800px; height:auto; margin:10px auto; border:1px solid #b9b9ba; background: #f1f1f2; padding:0px; border-radius:4px; border-bottom: 2px solid rgba(100,100,100,0.9);'>
                <tr>
                    <td style='background: url('http://i.imgur.com/4S4yqzW.png'); border-radius:3px 3px 0px 0px; height:100px;'>
                        <img style='border-radius:3px 3px 0px 0px;' src='http://i.imgur.com/4S4yqzW.png'>
                    </td>
                </tr>

                <tr>
                    <td style='padding:0px 25px; padding-top:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>
                        <h3>$subject</h3>
                    </td>
                </tr>
                <tr>
                    <td style='padding:0px 25px; padding-bottom:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>

                        $message

                    </td>
                </tr>
                ";

    if($emailGroup === -1){

        $mailTo = "matkle414@gmail.com";

        $emailMessage .= "<tr>
                    <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                        Copyright 'The Pool', Anthony Harris,  2014<br/>
                        <a href='http://www.whats-your-confidence.com/removeEmail.php?email={$mailTo}'>Remove Me From Mailing Group</a>
                    </td>
                </tr>
            </table>
        </div>";

        if(@Core::sendEmail($subject, $emailMessage, $mailTo))
            $_SESSION['result'] = "Email Sent";
        else
            $_SESSION['result'] = "Couldn't Connect to Email Server";



    }else if($emailGroup === 1){

        $userList = new users;
        $userList = $userList->getList("id ASC", array("disable_notes" => 0));

        $_SESSION['result'] = "Email List Generated";

    }

    header("Location: ./index.php");


}
