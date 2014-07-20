<?php

    include_once "./header.php";

    FormValidation::generate();

    $user = users::returnCurrentUser();

    if($user === false || !$user->verifyAuth())
        header("Location: ./logout.php");

    global $time;
    global $mem;
    global $memTwo;

    $time = microtime(TRUE);
    $mem = memory_get_usage();


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

</head>
<body class="height-100" data-ng-app="">

<?php if(isset($_SESSION['result'])): ?>

    <div class="ui-message-background hidden"></div>
    <div class="ui-message-box" data-type="result">
        <i class="fa fa-times-circle float-right ui-message-close"></i>
        <h5>Result</h5>
        <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
    </div>

<?php endif; ?>

<?php

    include "./menu.php";

?>

<div id="content-area" class="height-100" style="background-size: cover; background-position: left center;">
    <div class="width-50 height-100 fluid-row">

        <div class="fluid-row aligncenter"><h2>Testing</h2></div>
        <div class="fluid-row aligncenter">
            <?php

            $memTemp = number_format(((memory_get_usage() - $mem) / 1024), 2);
            $memTwoTemp = number_format(((memory_get_usage() - $memTwo) / 1024), 2);
            $timeTemp =  number_format((microtime(TRUE) - $time), 6);

            echo "Memory 1: $memTemp KB<br/>";
            echo "Memory 2: $memTwoTemp KB <br/>";
            echo "Time: $timeTemp sec";

            ?>
        </div>
        <div class="fluid-row aligncenter"><!--<?php

            $week = new week();
            $class = get_class($week);

            for($i = 0; $i < 10000; $i++){
                echo get_class($week);
            }

            ?>--></div>
        <div class="fluid-row aligncenter">
                <?php

                $memTemp = number_format(((memory_get_usage() - $mem) / 1024), 2);
                $memTwoTemp = number_format(((memory_get_usage() - $memTwo) / 1024), 2);
                $timeTemp =  number_format((microtime(TRUE) - $time), 6);

                echo "Memory 1: $memTemp KB<br/>";
                echo "Memory 2: $memTwoTemp KB <br/>";
                echo "Time: $timeTemp sec";

                ?>
         </div>

        <div class="fluid-row aligncenter"><!--<?php

            $week = new week();
            $class = get_class($week);

            for($i = 0; $i < 10000; $i++){
                echo $class;
            }

            ?>--></div>
        <div class="fluid-row aligncenter">
            <?php

            $memTemp = number_format(((memory_get_usage() - $mem) / 1024), 2);
            $memTwoTemp = number_format(((memory_get_usage() - $memTwo) / 1024), 2);
            $timeTemp =  number_format((microtime(TRUE) - $time), 6);

            echo "Memory 1: $memTemp KB<br/>";
            echo "Memory 2: $memTwoTemp KB <br/>";
            echo "Time: $timeTemp sec";

            ?>
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

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.19/angular.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
<script src="./js/modernizr.js"></script>
<script src="./js/general.js"></script>
<script src="./js/bug_report.js"></script>

<script>
    setTimeout(function(){
        var $mi = $("[data-menu-id='1']");

        if($mi.hasClass("hidden"))
            toggleMenuItemOverlay($mi);

    },3000);
</script>

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>