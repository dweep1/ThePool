<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

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

        <div class=\"email-template\" style='font-family: \"Open Sans\", sans-serif; background: #fff; width:auto; height:100%; padding:10px 20px; text-align: left;'>
            <table cellpadding='0' cellspacing='0' style='width:800px; height:auto; margin:10px auto; border:1px solid #b9b9ba; background: #f1f1f2; padding:0px; border-radius:4px; border-bottom: 2px solid rgba(100,100,100,0.9);'>
                <tr>
                    <td style='background: url('http://i.imgur.com/4S4yqzW.png'); border-radius:3px 3px 0px 0px; height:100px;'>
                        <img style='border-radius:3px 3px 0px 0px;' src='http://i.imgur.com/4S4yqzW.png'>
                    </td>
                </tr>

                <tr>
                    <td style='padding:0px 25px; padding-top:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>
                        <h2 style='border-bottom:1px solid rgba(120,120,255, 0.9); padding:3px 0px; margin-right:3px; font-weight:300;'>
                            $subject
                        </h2>
                    </td>
                </tr>
                <tr>
                    <td style='padding:0px 25px; padding-bottom:15px; font-size: 12px; line-height:22px; color:rgb(80,80,80);'>

                        $message

                    </td>
                </tr>
                ";

    if($emailGroup === -1){

        $userList = ["matkle414@gmail.com", "antwood1971@gmail.com"];

        $emailMessage .= "<tr>
                    <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                        Copyright 'The Pool', Anthony Harris,  2014<br/>
                        <a href='http://www.whats-your-confidence.com/removeEmail.php?email={$mailTo}'>Remove Me From Mailing Group</a>
                    </td>
                </tr>
            </table>
        </div>";

        $errors = 0;
        foreach($userList as $email){
            if(@Core::sendEmail($subject, $emailMessage, $email))
                $_SESSION['result'] = "Email Sent";
            else
                $errors++;
        }

        if($errors > 0){
            $_SESSION['result'] = "There was a problem sending 1 or more emails";
        }

    }else if($emailGroup === 1){

        $userList = new users;
        $userList = $userList->getList("id ASC", array("disable_notes" => 0));

        $emailMessage .= "<tr>
                    <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                        Copyright 'The Pool', Anthony Harris,  2014<br/>
                        <a href='http://www.whats-your-confidence.com/removeEmail.php?email={$mailTo}'>Remove Me From Mailing Group</a>
                    </td>
                </tr>
            </table>
        </div>";

        $errors = 0;
        foreach($userList as $user){
            if(@Core::sendEmail($subject, $emailMessage, $user->email))
                $_SESSION['result'] = "Email Sent";
            else
                $errors++;
        }

        if($errors > 0){
            $_SESSION['result'] = "There was a problem sending 1 or more emails";
        }


    }else if($emailGroup === 2){


        $userList = ["alimac66@yahoo.com",
                    "tony.mountvernon@aol.com",
                    "pokerdreamz@gmail.com",
                    "bklynking@usa.net",
                    "Rthompson129r@aol.com",
                    "kenken9898@aol.com",
                    "getsitdone17@hotmail.com",
                    "alphanso74@gmail.com",
                    "Covb24@yahoo.com",
                    "rdink31@yahoo.com",
                    "sugargoody1@yahoo.com",
                    "Randy4173@comcast.net",
                    "linjuangunter@yahoo.com",
                    "sjburdick@aol.com",
                    "kirkeous@gmail.com",
                    "joe_woolfolk@yahoo.com",
                    "himila@bellsouth.net",
                    "judsonsimmons10@gmail.com",
                    "antwood1971@gmail.com",
                    "vnyce8@yahoo.com",
                    "hotwings212@yahoo.com",
                    "greg.kendrick51@gmail.com",
                    "jwest850@yahoo.com",
                    "metrovalet@ymail.com",
                    "Birdfurr@yahoo.com",
                    "willeasem@gmail.com",
                    "4crewesin@gmail.com",
                    "gwoods@teamsai.com"];

        $emailMessage .= "<tr>
                    <td style='padding:5px 25px; font-family: \"Open Sans\", sans-serif; font-size: 10px; line-height:18px; text-align:center; border-top:1px solid #b9b9ba; background: #eaeaeb;'>
                        Copyright 'The Pool', Anthony Harris,  2014<br/>
                        <a href='http://www.whats-your-confidence.com/removeEmail.php?email={$mailTo}'>Remove Me From Mailing Group</a>
                    </td>
                </tr>
            </table>
        </div>";

        $errors = 0;
        foreach($userList as $email){
            if(@Core::sendEmail($subject, $emailMessage, $email))
                $_SESSION['result'] = "Email Sent";
            else
                $errors++;
        }

        if($errors > 0){
            $_SESSION['result'] = "There was a problem sending 1 or more emails";
        }

    }

    header("Location: ./index.php");


}
