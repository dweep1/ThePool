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

<div id="content-area" style="background-size: cover; background-position: left center;" data-ng-controller="RowController">
    <div class="width-50 fluid-row">

        <div class="fluid-row slim alignleft">
            <h6>Search:</h6> <input type="text" data-ng-model="search" />
            <button class="ui-button dark float-right" ng-click="doSave()">Save Picks</button>
            <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
        </div>

        <div class="fluid-row slim alignleft">

            <h5>Open Picks <i data-trans-for="open_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="open_picks">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpIn"
                        data-ng-repeat="item in games | filter:search | orderBy:'id'"
                        data-ng-if="item.pick.team_id <= 0" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-ng-click="item.pick.team_id = item.away_team.id;"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.away_team.id }}"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>


                        <div class="middle">

                            <i class="fa fa-minus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) - 1;"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   data-ng-model="item.pick.value" data-game-id="{{ item.id }}" />

                            <i class="fa fa-plus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) + 1;"></i>

                        </div>


                        <div data-ng-click="item.pick.team_id = item.home_team.id"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.home_team.id }}"
                             data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                             style="background-image: url('{{ item.home_team.image_url }}')">

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

            <h5>Current Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="current_picks">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpIn"
                        data-ng-repeat="item in games | filter:search | orderBy:'id'"
                        data-ng-if="item.pick.team_id > 0" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-ng-click="item.pick.team_id = item.away_team.id;" data-team-id="{{ item.away_team.id }}"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>


                        <div class="middle">

                            <i class="fa fa-minus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) - 1;"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   data-ng-model="item.pick.value" data-game-id="{{ item.id }}" />

                            <i class="fa fa-plus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) + 1;"></i>

                        </div>


                        <div data-ng-click="item.pick.team_id = item.home_team.id" data-team-id="{{ item.home_team.id }}"
                             data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                             style="background-image: url('{{ item.home_team.image_url }}')">

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

    <div class="fluid-row width-50 float-right secondary">

        <div class="fluid-row aligncenter">
            <div class="ui-mini-screen" style="background: url('./images/mini-screen.png')">
                <h6 class="current-title">Current Standings</h6>
                <h6 class="last-title">Last Week</h6>

                <div class="current-data">
                    <h2>{{ weekOld.total_rank }}</h2>
                    <h6>{{ weekOld.total_score }} points</h6>
                </div>

                <div class="last-data">
                    <h2>{{ weekOld.week_rank }}</h2>
                    <h6>{{ weekOld.week_score }} points</h6>
                </div>
            </div>
        </div>

        <div class="fluid-row aligncenter">

            <h4>Last Week's Results <i data-trans-for="last_week" class="fa fa-bars"></i></h4>

            <div data-trans-id="last_week">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpIn"
                        data-ng-repeat="item in gamesOld | filter:search | orderBy:'id'"
                        data-picked-id="{{ item.pick.team_id }}">

                        <div data-team-id="{{ item.away_team.id }}"style="background-image: url('{{ item.away_team.image_url }}')"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]">

                            <div data-ng-class="{true: 'loss gradient-left', false: 'gradient-left'}[item.away_score < item.home_score]">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="middle">

                            <i data-ng-class="{true: 'loss', false: ''}[item.away_score < item.home_score]">
                                {{ item.away_score }}
                            </i>

                            <i>@</i>

                            <i data-ng-class="{true: 'loss', false: ''}[item.away_score > item.home_score]">
                                {{ item.home_score }}
                            </i>

                        </div>

                        <div data-team-id="{{ item.home_team.id }}" style="background-image: url('{{ item.home_team.image_url }}')"
                             data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]">

                            <div data-ng-class="{true: 'loss gradient-right', false: 'gradient-right'}[item.away_score > item.home_score]">
                                <h5>{{ item.home_team.city }}</h5>
                                <h6>{{ item.home_team.team_name }}</h6>
                            </div>
                        </div>

                    </li>
                </ul>

            </div>
        </div>

    </div>

    <div class="clear-fix"></div>

</div>

<script src="./js/bug_report.js"></script>

<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>