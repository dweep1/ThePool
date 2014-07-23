<?php

    include_once "./header.php";

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
    <link href="./css/index.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script src="./js/jquery.velocity.min.js"></script>
    <script src="./js/velocity.ui.min.js"></script>
    <script src="./js/angular-velocity.min.js"></script>
    <script src="./js/modernizr.min.js"></script>
    <script src="./js/general.js"></script>
    <script src="./js/home.js"></script>
    <script>

        var week_id = <?php echo week::getCurrent()->id; ?>;

    </script>

</head>
<body class="height-100" data-ng-app="myHome">

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
    <div class="width-50 fluid-row" data-ng-controller="RowController">

        <div class="fluid-row slim alignleft">
            <h6>Search:</h6> <input type="text" data-ng-model="search" />
            <button class="ui-button dark float-right" ng-click="doSave()">Save Picks</button>
            <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
        </div>

        <div class="fluid-row slim alignleft">

            <h5>Current Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="current_picks">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpBigIn"
                        data-ng-repeat="item in (filtered = (gamesPicked | filter:search | orderBy:'id'))" data-picked-id="{{ item.pick.team_id }}"
                        on-finish-render="ngRepeatFinished" data-bad-value="{{ item.pick.bad }}" >

                        <div mm-pick ng-click="item.pick.team_id = item.away_team.id; changePick();"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.away_team.id }}"
                             class="team alignleft" style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="middle">

                            <i class="fa fa-minus-circle" game-id="{{ item.id }}" mm-minus
                               ng-click="item.pick.value = (item.pick.value - 0) - 1; subtractPoints();"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   ng-model="item.pick.value" game-id="{{ item.id }}" mm-value ng-change="changeValue()" />

                            <i class="fa fa-plus-circle" game-id="{{ item.id }}" mm-plus
                               ng-click="item.pick.value = (item.pick.value - 0) + 1; addPoints();"></i>

                        </div>

                        <div mm-pick ng-click="item.pick.team_id = item.home_team.id; changePick();"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.home_team.id }}"
                             class="team alignright"  style="background-image: url('{{ item.home_team.image_url }}')">

                            <div class="gradient-right">
                                <h5>{{ item.home_team.city }}</h5>
                                <h6>{{ item.home_team.team_name }}</h6>
                            </div>

                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="fluid-row slim alignleft">

            <h5>Open Picks <i data-trans-for="open_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="open_picks">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpBigIn"
                        data-ng-repeat="item in (filtered = (gamesNotPicked | filter:search | orderBy:'id'))" data-picked-id="{{ item.pick.team_id }}" >

                        <div mm-pick ng-click="item.pick.team_id = item.away_team.id; changePick();" data-team-id="{{ item.away_team.id }}"
                            class="team alignleft" style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="middle">

                            <i class="fa fa-minus-circle" game-id="{{ item.id }}" mm-minus
                               ng-click="item.pick.value = item.pick.value - 1; subtractPoints();"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   ng-model="item.pick.value" game-id="{{ item.id }}" mm-value ng-change="changeValue()" />

                            <i class="fa fa-plus-circle" game-id="{{ item.id }}" mm-plus
                               ng-click="item.pick.value = item.pick.value + 1; addPoints();"></i>

                        </div>

                        <div mm-pick ng-click="item.pick.team_id = item.home_team.id; changePick();" data-team-id="{{ item.home_team.id }}"
                            class="team alignright" style="background-image: url('{{ item.home_team.image_url }}')">
                            <div class="gradient-right">
                                <h5>{{ item.home_team.city }}</h5>
                                <h6>{{ item.home_team.team_name }}</h6>
                            </div>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="fluid-row width-50 dark float-right secondary">

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

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>