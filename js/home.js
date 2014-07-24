
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

myApp.directive("mmValue", function() {
    return {
        link: function(scope, elem, attrs) {
            scope.doChangeValue = function() {

                refreshGames(scope);

                refreshStoreLocal(scope);

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
    getOldData($scope,$http);

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

    $scope.changeValue = function() {
        this.doChangeValue();
    };

    $scope.doRefresh = function() {
        $scope.force = true;

        getLiveData($scope, $http);
    };

    $scope.doSave = function() {
        savePicks($scope, $http);
    };

}

function savePicks($scope, $http){

    $scope.url = "./_listeners/listn.picks.php?method=PUT";

    buildPicks($scope, function(){

        var $dupes = getGamesPicked($scope);

        if($dupes.length > 0){
            createMessageBox(
                {title: "error", message: "You have duplicate pick values. Please check your picks!"},
                function($messageID){toggleDisplayMessageBox($messageID);}
            );

            return false;
        }

        var $pickBoundary = false;

        $scope.picks.forEach(function(entity){
            if(parseInt(entity.value) > $scope.games.length || parseInt(entity.value) < 1){
                $pickBoundary = true;
            }
        });

        if($pickBoundary){
            createMessageBox(
                {type: "error", title: "error", message: "One of your pick's values is too high or too low. Please check your picks!"},
                function($messageID){toggleDisplayMessageBox($messageID);}
            );

            return false;
        }

        return $http.post($scope.url, $scope.picks).
            success(function(data, status) {

                $scope.status = status;

                createMessageBox(
                    {type: "result", title: "result", message: data.result},
                    function($messageID){toggleDisplayMessageBox($messageID);}
                );

                if(parseInt(data.errors) <= 0){
                    $scope.force = true;

                    getLiveData($scope, $http);
                }

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

    });

}

function getLiveData($scope, $http){

    $scope.week_id = week_id;

    $scope.url = "./_listeners/listn.picks.php?method=GET";

    // Create the http post request
    // the data holds the keywords
    // The request is a JSON request.

    if(parseInt(localStorage["week_id"]) === parseInt($scope.week_id) && $scope.force === false){

        storeLocalGames($scope, null);

        if(objLength($scope.games) <= 0){

            $scope.force = true;

            getLiveData($scope, $http);

        }else{

            getGamesPicked($scope);

            return true;
        }

        return false;

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

function getOldData($scope, $http){

    $scope.last_week_id = parseInt(week_id)-1;

    $scope.url = "./_listeners/listn.picks.php?method=GET";

    // Create the http post request
    // the data holds the keywords
    // The request is a JSON request.

    return $http.post($scope.url, { "week_id" : $scope.last_week_id}).
        success(function(data, status) {

            $scope.status = status;
            storeLocalGamesOld($scope, data);

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

function buildPicks($scope, $callback){

    var $picks = [];

    refreshGames($scope, function(){

        $scope.games.forEach(function(entity){
            if(checkSet(entity.pick) !== false && parseInt(entity.pick.team_id) !== -1)
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

function refreshStoreLocalOld($scope){

    localStorage["week_data_old"] = JSON.stringify($scope.weekOld);
    localStorage["game_data_old"] = JSON.stringify($scope.gamesOld);

}


function storeLocalGamesOld($scope, data){

    if(data === null){

        $scope.weekOld = data;
        $scope.gamesOld = data.games;

        refreshStoreLocalOld($scope);

    }else{

        $scope.weekOld = JSON.parse(localStorage["week_data_old"]);
        $scope.gamesOld = JSON.parse(localStorage["game_data_old"]);

    }

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

    return $dupes;

}

function changePickUI(elem){

    var $parentElem = $(elem).parent("li");

    var $children = $parentElem.children("[data-team-id]");
    var $pick_id_attr = $parentElem.attr("data-picked-id");

    $.each($children, function(){
        if(parseInt($(this).attr("data-team-id")) === parseInt($pick_id_attr)){
            $(this).addClass("picked");
        }else{
            $(this).removeClass("picked");
        }
    });

}

function refreshPicks(){

    $.each($("[data-team-id]"), function(){

        changePickUI(this);

    });

}
