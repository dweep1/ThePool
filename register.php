<?php

    include "./bootstrap.php";

    $currentUser = users::returnCurrentUser();

    $keyUser = new users();

    if($currentUser !== false && $currentUser->verifyAuth())
        header("Location: ./home.php");

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
        <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>

    </head>
<body class="height-100" data-ng-app>

<?php if(isset($_SESSION['result'])): ?>

    <div class="ui-message-background hidden" data-background-id="1"></div>
    <div class="ui-message-box" data-type="error" data-message-id="1">
        <i class="fa fa-times-circle float-right ui-message-close" data-close-id="1"></i>
        <h5>Result</h5>
        <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
    </div>

<?php endif; ?>

<?php if($keyUser->id !== null): ?>

    <div class="ui-message-background hidden instant" data-background-id="2"></div>
    <div class="ui-message-box aligncenter" data-type="nopass" data-message-id="2">
        <i class="fa fa-times-circle float-right ui-message-close" data-close-id="2"></i>
        <h6>Forgot Password</h6>
        <form action="./_listeners/listn.login.php" method="post">
            <input type="hidden" name="submitType" value="3" />
            <input type="hidden" name="security_key" value="<?php echo $keyUser->security_key; ?>" />
            <div class="faux-row"><input type="text" name="password" value="Password" data-password autocomplete="off"/></div>
            <div class="faux-row"><input type="text" name="confirm" value="Confirm Password" data-password autocomplete="off"/></div>
            <div class="faux-row"><button class="ui-button float-right">Submit</button></div>
        </form>
    </div>

<?php endif; ?>

    <div id="content-area" class="height-100" style="background-image: url('./images/bg1.jpg'); background-size: cover; background-position: left center; margin-left:0px;">

        <div id="pick-clock" class="index">
            <div id="lockHold" title="N Days, Hours:Minuets:Seconds">
                <span id="day"></span> Picks Lock: <span id="lockClock"></span>
            </div>

            <div class="sep"></div>
        </div>

        <div class="width-50 height-100 fluid-row">

            <div class="fluid-row aligncenter">

                <div class="fluid-row aligncenter">
                    <img id="logo-banner" src="./images/poolbanner.png" />
                </div>

                <div class="fluid-row width-90 aligncenter">

                    <div id="login-area">
                        <h5 id="text_id">Register</h5>
                        <form action="./_listeners/listn.login.php" method="post">
                            <input type="hidden" name="submitType" value="1" />
                            <div class="faux-row">
                                <input type="text" name="email" value="Email" />
                                <input type="text" name="username" value="Username" autocomplete="off" />
                            </div>
                            <div class="faux-row">
                                <input type="text" name="password" value="Password" data-password autocomplete="off" />

                            </div>
                            <div class="faux-row">
                                <input type="text" name="confirm" value="Confirm Password" data-password autocomplete="off" />
                            </div>
                            <div class="faux-row">
                                Passwords are case sensitive
                            </div>
                            <div class="faux-row">
                                <input type="text" name="paypal" value="Paypal Email" />
                            </div>
                            <div class="faux-row">
                                <select id="favorite_team_id" name="favorite_team_id">
                                    <option value="0" >Favorite Team</option>
                                    <?php
                                    foreach($teams as $value){

                                        echo "<option value='{$value->id}'>{$value->city} {$value->team_name}</option>";

                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="faux-row aligncenter">
                                <button class="ui-button large">Submit</button>
                            </div>
                        </form>
                    </div>
                    <br/>

                    <div class="intro-word-small" style="max-width:30%; margin-top:5%; margin-bottom:0;">
                        <a href="./login.php">Already have an account?</a>
                    </div>

                </div>

            </div>

        </div>

        <div class="fluid-row width-50 height-100 dark float-right secondary">

            <div class="fluid-row aligncenter">

                <div class="fluid-row width-90 alignleft"><p class="desc" style="font-size:1.1em; line-height: 1.35em;">The Pool is a weekly game where users pick and number NFL teams based on who they think they will win each week.
                        At the end of the week, the person who has scored the most points wins.</p></div>

                <div class="fluid-row width-90 aligncenter"><h3>The Pool General Rules</h3></div>

                <div class="fluid-row width-90 alignleft"><p class="number">PLAN</p><p class="desc">Plan your picks accordingly. Choose a strong team and try to predict upsets to gain the advantage.</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">PICK</p><p class="desc">Pick and rate teams which you think will have the upper hand going into their games.</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">PLAY</p><p class="desc">After the week is over the person with the most points wins the weekly pool. Will you win big?</p></div>

                <div class="fluid-row width-90 alignleft"><p class="desc" style="font-size:1.0em; text-transform: uppercase; line-height: 1.35em;">A fee of $10 is to be collected per played week. This must be paid prior to submitting your picks for that week.
                        If notification of pending payment has not been received you wil not be able to enter your picks for that week.</p></div>

                <div class="fluid-row width-90 alignleft"><a href="./rules.php">If you want to know more about The Pool click here.</a></div>

            </div>

        </div>

        <div class="clear-fix"></div>

    </div>

    <script>

        $(document).ready(function(){
            lockTimer();
            localStorage.clear();
        });


    </script>


<?php

include "./_footer.php";

?>