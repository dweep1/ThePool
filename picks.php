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
    <link href="./css/index.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script>

        week_id = <?php echo week::getCurrent()->id; ?>;

    </script>
    <script src="./js/jquery.velocity.min.js"></script>
    <script src="./js/velocity.ui.min.js"></script>
    <script src="./js/angular-velocity.min.js"></script>
    <script src="./js/modernizr.min.js"></script>
    <script src="./js/general.js"></script>
    <script src="./js/pick.js"></script>

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

<div id="content-area" data-ng-controller="RowController">
    <div class="width-50 fluid-row first">

        <div class="fluid-row slim alignleft">
            <h6>Search:</h6> <input type="text" data-ng-model="search" />
            <button class="ui-button dark float-right" ng-click="doSave()">Save Picks</button>
            <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
        </div>

        <div class="fluid-row slim float-right">
            <div id="changeBox">You have unsaved picks.</div>
        </div>

        <div class="fluid-row slim alignleft">

            <h5>Closed Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>

            <div class="clear-fix"></div>

            <div data-trans-id="current_picks">

                <ul class="ui-games-list">

                    <li data-ng-repeat="item in games | filter:search | orderBy:'id'"
                        data-ng-if="item.gameLock != false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-team-id="{{ item.away_team.id }}"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>


                        <div class="middle">

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   data-ng-model="item.pick.value" data-game-id="{{ item.id }}" disabled />

                        </div>


                        <div data-team-id="{{ item.home_team.id }}"
                             data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                             style="background-image: url('{{ item.home_team.image_url }}')">

                            <div class="gradient-right">
                                <h5>{{ item.home_team.city }}</h5>
                                <h6>{{ item.home_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="clear-fix"></div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="fluid-row slim alignleft">

            <h6 class="remaining">Remaining Numbers: <b data-ng-if="remaining.length <= 0">NONE</b> <b data-ng-repeat="item in remaining | orderBy:'value'">{{ item.value }},</b></h6>
            <h5>Open Picks <i data-trans-for="open_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="open_picks">

                <ul class="ui-games-list">

                    <li data-ng-repeat="item in games | filter:search | orderBy:'id'"
                        data-ng-if="item.gameLock == false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

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

                        <div class="clear-fix"></div>

                    </li>
                </ul>
            </div>

        </div>

    </div>

    <div id="team-info" class="fluid-row width-50 dark float-right secondary">

        <?php

            include "./tpl.picks.teaminfo.php";

        ?>

    </div>

    <div class="clear-fix"></div>

</div>

<script>
    $("#changeBox").velocity("fadeOut", { visibility: "hidden", duration: 0});
</script>

<?php

    include "./_footer.php";

?>