<?php

include_once "./_header.php";

FormValidation::generate();

$user = users::returnCurrentUser();

if($user === false || !$user->verifyAuth())
    header("Location: ./logout.php");

$teams = teams::getTeamsList();

?>
<!DOCTYPE html>
<html>
<head>

    <title>The Pool</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script src="./js/jquery.velocity.min.js"></script>
    <script src="./js/velocity.ui.min.js"></script>
    <script src="./js/angular-velocity.min.js"></script>
    <script src="./js/modernizr.min.js"></script>
    <script src="./js/general.js"></script>

</head>
<body class="height-100">

<?php if(isset($_SESSION['result'])): ?>

    <div class="ui-message-background hidden" data-background-id="1"></div>
    <div class="ui-message-box" data-type="result" data-message-id="1">
        <i class="fa fa-times-circle float-right ui-message-close" data-close-id="1"></i>
        <h5>Result</h5>
        <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
    </div>

<?php endif; ?>

<?php

include "./menu.php";

?>

<div id="content-area">
    <div class="width-50 fluid-row aligncenter settings">

        <form action="./_listeners/listn.settings.php" method="post">
            <input type="hidden" name="submitType" value="0" />

            <div class="fluid-row width-90 alignleft">
                <h6>Confirm Password</h6>  <input type="password" name="confirm_password" autocomplete="off" />
                <button class="ui-button dark float-right">Save Account Changes</button>
            </div>

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row"><h4>Change Login Info</h4></div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="email">Email: </label> <input type="text" class="float-right" no-default id="email" name="email" value="<?php echo $user->email; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim"></div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="password">New Password: </label> <input class="float-right" type="password" id="password" name="password" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="confirm">Confirm Password: </label> <input class="float-right" type="password" id="confirm" name="confirm" />
                    <div class="clear-fix"></div>
                </div>

            </div>

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row"><h4>Add/Edit Account Info</h4></div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="username">Username: </label> <input class="float-right" type="text" no-default id="username" name="username" value="<?php echo $user->username; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="first_name">First Name: </label> <input class="float-right" type="text" no-default id="first_name" name="first_name" value="<?php echo $user->first_name; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="last_name">Last Name: </label> <input class="float-right" type="text" no-default id="last_name" name="last_name" value="<?php echo $user->last_name; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row width-60 slim over-90 <?php echo (strlen($user->paypal) > 3) ? "" : "error"; ?>">
                    <label for="username">Paypal Email: </label> <input class="float-right" type="text" no-default id="paypal" name="paypal" value="<?php echo $user->paypal; ?>" />
                    <div class="clear-fix"></div>
                </div>

                <div class="fluid-row slim"></div>

                <div class="fluid-row width-60 slim over-90">
                    <label for="favorite_team_id">Favorite Team: </label>
                    <select id="favorite_team_id" name="favorite_team_id" class="float-right">
                        <option value="0" >Favorite Team</option>
                        <?php
                            foreach($teams as $value){

                                if($user->favorite_team_id == $value->id)
                                    echo "<option value='{$value->id}' selected>{$value->city} {$value->team_name}</option>";
                                else
                                    echo "<option value='{$value->id}'>{$value->city} {$value->team_name}</option>";
                            }
                        ?>
                    </select>
                    <div class="clear-fix"></div>
                </div>

            </div>

        </form>


    </div>

    <div class="fluid-row width-50 dark height-100 float-right secondary aligncenter">
        <form action="./_listeners/listn.settings.php" method="post">

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row width-60 slim">
                    Paypal Email: <?php echo (strlen($user->paypal) > 3) ? $user->paypal : "NONE"; ?>
                </div>

                <div class="fluid-row">
                    <h4>Transactions</h4>
                </div>

                <ul class="ui-rank-list">
                    <li class="title">
                        <div class="width-25">Date</div>
                        <div class="width-25">Used</div>
                        <div class="width-40">Transaction ID</div>
                    </li>

                    <?php

                        $credits = new credit;
                        $credits = $credits->getList("id desc", array("user_id" => $user->id));

                        foreach($credits as $value){

                            $used = ((int) $value->week_id <= -1) ? "No" : "Yes";
                            $date = new DateTime($value->date);
                            $result = $date->format('D M jS, Y');

                            echo   "<li>
                                        <div class='width-25'>{$result}</div>
                                        <div class='width-25'>{$used}</div>
                                        <div class='width-40'>{$value->nid}</div>
                                    </li>";


                        }

                    ?>

                </ul>

            </div>

    </div>

    <div class="clear-fix"></div>

</div>

<?php

    include "./_footer.php";

?>