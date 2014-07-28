<?php

    include_once "./header.php";

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

        <div id="login-area" class="aligncenter hidden" data-menu="" data-menu-id="1">
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
                <div class="fluid-row width-90 alignleft"><i>At vero eos et accusamus et iusto odio dignissimos ducimus
                        qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias
                        excepturi sint occaecati cupiditate non provident.</i></div>

                <div class="fluid-row width-90 alignleft"><p class="number">01</p><p class="desc">Quisque vel euismod risus. Cras eget dui vulputate</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">02</p><p class="desc">Sed et lacus nibh. Ut sed felis ut nulla tincidunt faucibus vel eu mauris</p></div>

                <div class="fluid-row width-90 alignleft"><p class="number">03</p><p class="desc">Vivamus aliquet tellus eros, id venenatis ante venenatis tempus.</p></div>

                <div class="fluid-row width-90 alignleft"><p class="c-right">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer cursus consequat nunc. Sed
                        convallis, ipsum et commodo scelerisque, nunc risus euismod orci, eu auctor mi sapien tincidunt lacus. Mauris in pulvinar risus.</p></div>
            </div>

        </div>

        <div class="clear-fix"></div>

    </div>

    <script src="./js/bug_report.js"></script>

    <script>
        setTimeout(function(){
            var $mi = $("[data-menu-id='1']");

            if($mi.hasClass("hidden"))
                toggleMenuItemOverlay($mi);

        },3000);
    </script>

    <script>
        localStorage.clear();
    </script>

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>