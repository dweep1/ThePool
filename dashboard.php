<?php

include_once "./_header.php";

FormValidation::generate();

$user = users::returnCurrentUser();

if($user === false || !$user->verifyAuth())
    header("Location: ./logout.php");


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
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>


</head>
<body class="height-100" data-ng-app="myDash">

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

<div id="content-area" data-ng-controller="RowController">
    <div class="width-50 fluid-row first">

        <div class="fluid-row slim alignleft">

            <h4>Pick Performance <i data-trans-for="pick_performance" class="fa fa-bars"></i></h4>

            <div data-trans-id="pick_performance">

                <div id="perChart" class="fluid-row slim aligncenter">

                    <div class="fluid-row slim alignleft" id="performanceLegend"></div>

                    <canvas id="performanceChart"></canvas>

                </div>

            </div>

        </div>

        <div class="fluid-row slim alignleft">

            <h4>User Rank <i data-trans-for="rank" class="fa fa-bars"></i></h4>

            <div data-trans-id="rank" class="aligncenter">

                <div class="fluid-row slim"></div>

                <div id="rankInfo" class="fluid-row slim aligncenter">

                </div>

            </div>


        </div>

    </div>

    <div class="fluid-row width-50 dark height-100 float-right secondary">

        <div class="fluid-row width-50 alignleft">
            <h5>Rank by Points</h5>
            <div class="fluid-row slim"></div>
            <ul class="ui-rank-list">
                <li class="title alignleft">
                    <div class="width-40">Name</div>
                    <div class="width-25">Rank</div>
                    <div class="width-25">Points</div>
                </li>
                <li data-ng-repeat="item in rankList | orderBy:'-total'"
                    data-ng-class="{'highlight': item.userID == myUserID, 'rival': rivals.indexOf(item.userID) != -1}">
                    <div class="width-40">{{ item.username }}</div>
                    <div class="width-25">{{ $index + 1 }}</div>
                    <div class="width-25">{{ item.total }}</div>

                </li>

            </ul>

        </div>

        <div class="fluid-row width-50 alignleft">
            <h5>Rank by Percentage</h5>
            <div class="fluid-row slim"></div>
            <ul class="ui-rank-list">
                <li class="title">
                    <div class="width-40">Name</div>
                    <div class="width-25">Rank</div>
                    <div class="width-25">Percent</div>
                </li>
                <li data-ng-repeat="item in rankList | orderBy:'-percent'"
                    data-ng-class="{'highlight': item.userID == myUserID, 'rival': rivals.indexOf(item.userID) != -1}">
                    <div class="width-40">{{ item.username }}</div>
                    <div class="width-25">{{ $index + 1 }}</div>
                    <div class="width-25">{{ item.percent }}%</div>
                </li>
            </ul>

        </div>

    </div>

    <div class="clear-fix"></div>

</div>

<script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>
<script src="./js/Chart.js"></script>

<script>

    week_id = <?php echo week::getCurrent()->id; ?>;

</script>

<script src="./js/dashboard.js?ver=<?php echo VERSION ?>"></script>

<?php

include "./_footer.php";


?>
