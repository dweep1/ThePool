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

<div id="content-area">
    <div class="width-90 fluid-row aligncenter">

        <div class="width-90 fluid-row-slim alignleft">

        </div>

    </div>

    <div class="clear-fix"></div>

</div>

<script src="./js/bug_report.js"></script>

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>