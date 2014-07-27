<?php

include_once "./header.php";

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

<div id="content-area" style="background-size: cover; background-position: left center;">
    <div class="width-50 fluid-row aligncenter">

        <form action="./_listeners/listn.settings.php" method="post">
            <input type="hidden" name="submitType" value="0" />

            <div class="fluid-row width-90 alignleft">
                <h6>Confirm Password</h6>  <input type="password" name="confirm_password" autocomplete="off" />
                <button class="ui-button dark float-right">Save Account Changes</button>
            </div>

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row"><h4>Change Login Info</h4></div>

                <div class="fluid-row width-60 slim">
                    <label for="email">Email: </label> <input type="text" class="float-right" id="email" name="email" value="<?php echo $user->email; ?>" />
                </div>
                <div class="fluid-row slim"></div>
                <div class="fluid-row width-60 slim">
                    <label for="password">New Password: </label> <input class="float-right" type="password" id="password" name="password" />
                </div>
                <div class="fluid-row width-60 slim">
                    <label for="confirm">Confirm Password: </label> <input class="float-right" type="password" id="confirm" name="confirm" />
                </div>

            </div>

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row"><h4>Add/Edit Account Info</h4></div>

                <div class="fluid-row width-60 slim">
                    <label for="first_name">First Name: </label> <input class="float-right" type="text" id="first_name" name="first_name" value="<?php echo $user->first_name; ?>" />
                </div>

                <div class="fluid-row width-60 slim">
                    <label for="last_name">Last Name: </label> <input class="float-right" type="text" id="last_name" name="last_name" value="<?php echo $user->last_name; ?>" />
                </div>

                <div class="fluid-row slim"></div>

                <div class="fluid-row width-60 slim">
                    <label for="favorite_team">Favorite Team: </label>
                    <select id="favorite_team" class="float-right">
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
                </div>

            </div>

        </form>


    </div>

    <div class="fluid-row width-50 dark height-100 float-right secondary aligncenter">
        <form action="./_listeners/listn.settings.php" method="post">

            <div class="fluid-row width-90 alignleft">

                <div class="fluid-row"><h4>Rivals</h4></div>

            </div>

    </div>

    <div class="clear-fix"></div>

</div>

<script src="./js/bug_report.js"></script>

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>