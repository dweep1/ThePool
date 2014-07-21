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


</head>
<body class="height-100" data-ng-app="myHome">

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
    <div class="width-50 height-100 fluid-row" data-ng-controller="RowController">

        <div class="fluid-row slim alignleft">
            <h6>Search Games:</h6>  <input type="text" data-ng-model="search" />
            <button class="ui-button dark float-right">Save Picks</button>
            <button class="ui-button dark float-right" ng-click="doRefresh()">Discard Changes</button>
        </div>

        <div class="fluid-row slim alignleft">

            <h5>Current Picks <i data-trans-for="current_picks" class="fa fa-bars"></i></h5>

            <div data-trans-id="current_picks">

                <ul class="ui-games-list">

                    <li class="velocity-opposites-transition-slideUpBigIn" data-velocity-opts="{ stagger: 100, drag: true }"
                        data-ng-repeat="item in (filtered = (gamesPicked | filter:search | orderBy:'id'))" data-picked-id="{{ item.pick.team_id }}"
                        on-finish-render="ngRepeatFinished" >

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
                               ng-click="item.pick.value = item.pick.value - 1; subtractPoints();"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}" />

                            <i class="fa fa-plus-circle" game-id="{{ item.id }}" mm-plus
                               ng-click="item.pick.value = item.pick.value + 1; addPoints();"></i>

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

                    <li class="velocity-opposites-transition-slideUpBigIn" data-velocity-opts="{ stagger: 100, drag: true }"
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

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}" />

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
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
<script src="./js/jquery.velocity.min.js"></script>
<script src="./js/velocity.ui.min.js"></script>
<script src="./js/angular-velocity.min.js"></script>
<script src="./js/modernizr.min.js"></script>
<script src="./js/general.js"></script>
<script src="./js/bug_report.js"></script>

<script>

    var myApp = angular.module('myHome', ['angular-velocity'])
        .directive('onFinishRender', function ($timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attr) {
                    if (scope.$last === true) {
                        $timeout(function () {
                            scope.$emit(attr.onFinishRender);
                        });
                    }
                }
            }
        });

    myApp.directive("mmPick", function() {
        return {
            link: function(scope, elem, attrs) {
                scope.doChangePick = function() {

                    //each game needs a pick object when its given.
                    //if there is no team_id, then pick isn't valid.

                    refreshGames(scope);

                    refreshStoreLocal(scope);

                    setTimeout(function(){
                        changePickUI(elem);
                    }, 200);
                }
            }
        }
    });

    myApp.directive("mmMinus", function() {
        return {
            link: function(scope, elem, attrs) {
                scope.doPointsSubtract = function() {

                    refreshGames(scope);

                    refreshStoreLocal(scope);

                }
            }
        }
    });

    myApp.directive("mmPlus", function() {
        return {
            link: function(scope, elem, attrs) {
                scope.doPointsAdd = function() {

                    refreshGames(scope);

                    refreshStoreLocal(scope);

                }
            }
        }
    });

    function RowController($scope, $http) {

        $scope.force = false;

        getLiveData($scope, $http);

        $scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
            setTimeout(refreshPicks, 50);
        });

        $scope.subtractPoints = function() {
            this.doPointsSubtract();
        };

        $scope.addPoints = function() {
            this.doPointsAdd();
        };

        $scope.changePick = function() {
            this.doChangePick();
        };

        $scope.doRefresh = function() {
            $scope.force = true;

            getLiveData($scope, $http);
        };

    }

    function buildPicks($scope, $callback){

        var $picks = [];

        refreshGames($scope, function(){

            $scope.games.forEach(function(entity){
                if(checkSet(entity.pick) !== false)
                    $picks.push(entity.pick);
            });

            $scope.picks = JSON.parse(JSON.stringify($picks));

            if(checkSet($callback))
                $callback();

        });

    }

    function refreshGames($scope, $callback){

        var $games = [];

        $scope.gamesPicked.forEach(function(entity){
            $games.push(entity);
        });

        $scope.gamesNotPicked.forEach(function(entity){
            $games.push(entity);
        });

        $scope.games = JSON.parse(JSON.stringify($games));

        if(checkSet($callback))
            $callback();

    }

    function changePickUI(elem){

        var $parentElem = $(elem).parent("li");

        var $children = $parentElem.children("[data-team-id]");
        var $pick_id_attr = $parentElem.attr("data-picked-id");

        $.each($children, function(){

            if(parseInt($(this).attr("data-team-id")) === parseInt($pick_id_attr)){
                $(this).addClass("picked");
                console.log("pik");
            }else{
                $(this).removeClass("picked");
            }
        });

    }

    function refreshPicks(){

        var $content = $(".picked");

        console.log("refreshing Picks");

        $.each($content, function(){

            $(this).removeClass("picked");

        });

        var $content = $("[data-picked-id]");

        $.each($content, function(){

            var $pick_id_attr = $(this).attr("data-picked-id");

            $('[data-team-id="'+$pick_id_attr+'"]').addClass("picked");

        });

    }

    function refreshStoreLocal($scope){

        localStorage["week_id"] = $scope.week_id;
        localStorage["week_data"] = JSON.stringify($scope.week);
        localStorage["game_data"] = JSON.stringify($scope.games);

    }

    function storeLocalGames($scope, data){

        if(parseInt(localStorage["week_id"]) !== parseInt($scope.week_id) || $scope.force === true){

            $scope.week = data;
            $scope.games = data.games;

            refreshStoreLocal($scope);

        }else{

            $scope.week = JSON.parse(localStorage["week_data"]);
            $scope.games = JSON.parse(localStorage["game_data"]);

        }

    }

    function getLiveData($scope, $http){

        $scope.week_id = <?php echo week::getCurrent()->id; ?>;

        $scope.url = "./_listeners/listn.picks.php?method=GET";

        // Create the http post request
        // the data holds the keywords
        // The request is a JSON request.

        if(parseInt(localStorage["week_id"]) === parseInt($scope.week_id) && $scope.force === false){

            storeLocalGames($scope, null);
            getGamesPicked($scope);

            return true;

        }else{

            return $http.post($scope.url, { "week_id" : $scope.week_id}).
                success(function(data, status) {

                    $scope.status = status;
                    storeLocalGames($scope, data);
                    getGamesPicked($scope);

                    if($scope.force === true)
                        $scope.force = false;

                    return true;

                })
                .
                error(function(data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;

                    if($scope.force === true)
                        $scope.force = false;

                    return false;
                });

        }

    }

    function getGamesPicked($scope){

        var $games = $scope.games;

        var $picked = [];
        var $notPicked = [];

        var $values = [];
        var $dupes = [];

        var $i = 0;
        var $k = 0;

        $games.forEach(function(entity){

            if(parseInt(entity.pick.team_id) !== -1){
                if(indexOf.call($values, parseInt(entity.pick.value)) == -1)
                    $values.push(parseInt(entity.pick.value));
                else
                    $dupes.push(parseInt(entity.pick.value));
            }

        });

        $games.forEach(function(entity){

           if(parseInt(entity.pick.team_id) !== -1){

               if(indexOf.call($dupes, parseInt(entity.pick.value)) > -1)
                   entity.pick.bad = "true";
               else
                   entity.pick.bad = "false";

               $picked.push(entity);

           }else{
               $notPicked.push(entity);
           }

        });

        $scope.gamesPicked = JSON.parse(JSON.stringify($picked));
        $scope.gamesNotPicked = JSON.parse(JSON.stringify($notPicked));

    }
</script>


<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>