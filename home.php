<?php

    include "./bootstrap.php";

    $user = users::returnCurrentUser();

    if($user === false || !$user->verifyAuth())
        header("Location: ./logout.php");

    $thisWeek = week::getCurrent();

?>
<!DOCTYPE html>
<html>
<head>

    <title>The Pool - Home</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>


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

        <div class="fluid-row aligncenter">
            <div class="ui-mini-screen" style="background: url('./images/mini-screen.png')">
                <h5 class="current-title">Overall Standings</h5>
                <h5 class="last-title">Last Week</h5>

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

        <div class="fluid-row slim aligncenter" >
            <h4>This Week's Games <button style="vertical-align: middle;" class="ui-button dark" type="button" data-link="./picks.php">Make Picks</button></h4>
            <br/>

            <?php if(season::getCurrent()->type !== "playoff"){ ?>
                <h5>Pool Size: ~$<?php echo week::getPoolAmount($thisWeek->id); ?></h5>
            <?php }else{ ?>
                <h5>Pool Size: ~$<?php echo week::getPoolAmount(false, true); ?></h5>
            <?php } ?>

            Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><b><a href="./settings.php" style="padding:10px;">Buy Credits</a></b>
        </div>

        <div class="fluid-row slim alignleft">

            <div class="fluid-row width-50 slim alignleft" style="vertical-align: middle;">
                <h5>Closed Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>
            </div>

            <div class="fluid-row width-50 slim alignright" style="vertical-align: middle;">
                <h6>Search:</h6> <input type="text" data-ng-model="search" />
            </div>

            <div class="clear-fix"></div>

            <div data-trans-id="current_picks" class="aligncenter">

                <ul class="ui-games-list">

                    <li class="full" data-ng-repeat="item in games | filter:search | orderBy:'date'" data-ng-init="item.status = 'closed'"

                        data-ng-if="item.gameLock != false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-team-id="{{ item.away_team.id }}" class="team alignleft"
                             data-ng-class="{'picked': item.pick.team_id == item.away_team.id, 'loss': item.away_score < item.home_score}"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>

                        <div data-ng-if="(item.away_score + item.home_score) > 0" class="middle">

                            <i data-ng-class="{true: 'loss', false: ''}[item.away_score < item.home_score]">
                                {{ item.away_score }}
                            </i>

                            <i>@</i>

                            <i data-ng-class="{true: 'loss', false: ''}[item.away_score > item.home_score]">
                                {{ item.home_score }}
                            </i>

                        </div>


                        <div data-ng-if="(item.away_score + item.home_score) <= 0" class="middle">

                            {{ item.display_date }}

                        </div>


                        <div data-team-id="{{ item.home_team.id }}" class="team alignright float-right"
                             data-ng-class="{'picked': item.pick.team_id == item.home_team.id, 'loss': item.away_score > item.home_score}"
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

            <div class="fluid-row width-50 slim alignleft">
                <h5>Open Picks <i data-trans-for="open_picks" class="fa fa-bars"></i></h5>
            </div>

            <div data-trans-id="open_picks" class="aligncenter">

                <ul class="ui-games-list">

                    <li class="full" data-ng-repeat="item in games | filter:search | orderBy:'date'" data-ng-init="item.status = 'open'"
                        data-ng-if="item.gameLock == false" data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-team-id="{{ item.away_team.id }}"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="middle">

                            {{ item.display_date }}

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
    </div>

    <div class="fluid-row width-50 float-right secondary">

        <div class="fluid-row aligncenter">

            <?php

            $currentWeek = week::getCurrent();

            if ($currentWeek !== false) {
              $previousWeek = $currentWeek->getPrevious();
            } else {
              $previousWeek = false;
            }

            if($previousWeek !== false ){
              $games = game::loadMultiple(["week_id" => $previousWeek->id, "season_id" => season::getCurrent()->id]);
            } else {
              $games = [];
            }

            if(count($games) > 0 || season::getCurrent()->type !== "playoff" ):

            ?>

            <h4>Last Week's Game Results <i data-trans-for="last_week" class="fa fa-bars"></i></h4>

            <div data-trans-id="last_week" class="alignleft">

                <ul class="ui-games-list">

                    <li data-ng-repeat="item in gamesOld | orderBy:'id'"
                        data-picked-id="{{ item.pick.team_id }}">

                        <div data-team-id="{{ item.away_team.id }}" style="background-image: url('{{ item.away_team.image_url }}')"
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

                        <div class="display-date">{{ item.display_date }}</div>

                        <div class="clear-fix"></div>

                    </li>
                </ul>

            </div>

            <?php

                else:

            ?>

            <h4>Last Season's Results <i data-trans-for="last_season" class="fa fa-bars"></i></h4>

            <div data-trans-id="last_season" class="alignleft" data-ng-controller="LastSeasonController">

                <div data-ng-repeat="item in oldResults | orderBy:'id'" class="team-small" style="background-image: url('{{ item.image_url }}')">

                    <div class="gradient-left" >
                        <div class="team alignleft">
                            <h5>{{ item.city }}</h5>
                            <h6>{{ item.team_name }}</h6>
                        </div>

                        <div class="team-stats alignright">
                            <h6>Wins: {{ item.wins }}</h6>
                            <h6>Losses: {{ item.games - item.wins }}</h6>
                        </div>
                    </div>

                </div>

            </div>

            <?php

                endif;

            ?>
        </div>


    </div>

    <div class="clear-fix"></div>

</div>

<script>

    week_id = <?php echo week::getCurrent()->id; ?>;
    season_id = <?php echo season::getCurrent()->id; ?>;

</script>

<script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>
<script src="./js/home.js?ver=<?php echo VERSION ?>"></script>
<script>
    $("#changeBox").velocity("fadeOut", { visibility: "hidden", duration: 0});
</script>

<script>

    function LastSeasonController($scope, $http) {

        getPreviousSeason($scope, $http);

    }

    function getPreviousSeason($scope, $http){

        $scope.season_id = season_id;

        return $http.post("./_listeners/listn.playoff.stats.php", { "season_id" : $scope.season_id}).
            success(function(data, status) {

                $scope.oldResults = data;

                console.log($scope.oldResults);

                return true;

            })
            .error(function(data, status) {

                return false;
            });

    }


</script>

<?php

    include "./_footer.php";

?>
