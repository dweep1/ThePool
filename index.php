<?php

    include_once "./_header.php";

    FormValidation::generate();

    $currentUser = users::returnCurrentUser();

    $keyUser = new users();

    if($currentUser !== false && $currentUser->verifyAuth())
        header("Location: ./home.php");

    if(isset($_GET['key']))
        $keyUser->load($_GET['key'], "security_key");

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
    <script src="./js/jquery.velocity.min.js"></script>
    <script src="./js/velocity.ui.min.js"></script>
    <script src="./js/modernizr.min.js"></script>
    <script src="./js/general.js"></script>
    <script src="./js/jquery.tinytimer.js"></script>
    <script src="./js/pickClock.js"></script>

</head>
<body class="height-100" data-ng-app>

    <?php if(isset($_SESSION['result'])): ?>

        <div class="ui-message-background hidden" data-background-id="1"></div>
        <div class="ui-message-box" data-type="result" data-message-id="1">
            <i class="fa fa-times-circle float-right ui-message-close" data-close-id="1"></i>
            <h5>Result</h5>
            <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
        </div>

    <?php endif; ?>

    <?php if($keyUser->id !== null): ?>

    <div class="ui-message-background hidden" data-background-id="2"></div>
    <div class="ui-message-box aligncenter" data-type="overlay" data-message-id="2">
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

    <nav id="main-nav">

        <div id="logo"><h3>TP</h3></div>
        <ul>
            <li id="expand-menu"><h2><i class="fa fa-bars"style="padding-left:1px;"></i></h2></li>
            <li><h2 data-icon="" data-id="1"><i class="fa fa-user" style="padding-left:2px;"></i> Login</h2></li>
            <li><h2><i class="fa fa-question-circle" style="padding-left:1px;"></i> About</h2></li>
        </ul>

        <div id="pick-clock" class="index">
            <div id="lockHold" title="N Days, Hours:Minuets:Seconds">
                <span id="day"></span> Picks Lock: <span id="lockClock"></span>
            </div>

            <div class="sep"></div>
        </div>

        <div id="login-area" class="aligncenter hidden" data-menu="" data-menu-id="1">
            <i class="fa fa-times-circle float-right ui-message-close" data-menu-close-id="1"></i>
            <h5>Login</h5>
            <form action="./_listeners/listn.login.php" method="post">
                <div class="faux-row"><input type="text" name="email" value="Email" /></div>
                <div class="faux-row"><input type="text" name="password" value="Password" data-password autocomplete="off" /></div>
                <div class="faux-row">
                    <input type="hidden" name="confirm" value="Confirm Password" data-password autocomplete="off" />
                    <?php if(isset($_SESSION['login_attempts']) && (int) $_SESSION['login_attempts'] >= 2): ?>
                        <div id="forgotPass" style="cursor:pointer">Forgot Password?</div>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="submitType" value="0" />
                <div class="faux-row">
                    <button type="button" class="ui-button float-left" data-button-type="register">Register</button>
                    <button class="ui-button float-right">Submit</button>
                    <div class="clear-fix"></div>
                </div>
            </form>

        </div>

    </nav>


    <div id="content-area" class="height-100" style="background-image: url('./images/bg1.jpg'); background-size: cover; background-position: left center;">

        <div class="width-50 height-100 fluid-row">

            <div class="intro-word-con">
                <div class="intro-word">
                    Plan &#62;
                </div> <br/>
                <div class="intro-word">
                    Pick &#62;
                </div> <br/>
                <div class="intro-word">
                    Play &#62;
                </div>
            </div>

        </div>

        <div class="fluid-row width-50 height-100 dark float-right secondary">

            <div class="fluid-row aligncenter"><img id="logo-banner" src="./images/poolbanner.png" /></div>

            <div class="fluid-row aligncenter">
                <div class="fluid-row width-90 alignleft"><i>The Pool is a weekly betting game where users pick and number NFL teams based on who they think they will win each week.
                        At the end of the week, the person who has scored the most points wins the betting pool for that week.</i></div>

                <div class="fluid-row width-90 alignleft"><p class="number">01</p><p class="desc">Plan your picks accordingly. Choose a strong team and try to predict upsets to gain the advantage.</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">02</p><p class="desc">Pick and rate teams which you think will have the upper hand going into their games.</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">03</p><p class="desc">After the week is over the person with the most points wins the weekly pool. Will you win big?</p></div>

                <div class="fluid-row width-90 alignleft"><p class="c-right">A fee of $10 is to be collected a per played week. This must be paid prior to submitting
                        your picks for that week. If notification of a pending payment has not been received you will not be able to enter your picks for that week.</p></div>
            </div>

        </div>

        <div class="clear-fix"></div>

    </div>

    <script>

        lockTimer();

        setTimeout(function(){
            var $mi = $("[data-menu-id='1']");

            if($mi.hasClass("hidden"))
                toggleMenuItemOverlay($mi);

        },3000);
    </script>

    <script>
        localStorage.clear();
    </script>

<?php

    include "./_footer.php";

?>