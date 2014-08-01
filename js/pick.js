$(document).on("mousedown", "[data-team-id]", function (e) {

    var $for = $(this).attr('data-team-id');

   $("#team-info").load("./tpl.picks.teaminfo.php?team_id="+$for);

});

var myApp = angular.module('myHome', ['angular-velocity']);

function RowController($scope, $http) {

    $scope.force = false;

    getLiveData($scope, $http);
    getOldData($scope,$http);

    $scope.doRefresh = function() {
        $scope.force = true;

        localStorage.clear();

        getLiveData($scope, $http);
    };

    $scope.doSave = function() {
        savePicks($scope, $http);
    };

    $scope.$watch('games', function() {
        refreshStoreLocal($scope);
        getRemaining($scope);
    }, true);

    getRemaining($scope);

}

function getRemaining($scope){

    var $remaining = [];
    $scope.remaining = [];

    var $noPick = [];

    $scope.games.forEach(function(entity){
        if(checkSet(entity.pick) !== false)
            $noPick.push(entity.pick.value);
    });

    for(var $i = 1; $i < ($scope.games.length+1); $i++){
        if(indexOf.call($noPick, $i) > -1){

        }else{
            $remaining.push($i);
        }
    }

    $remaining.forEach(function(entity){
        if(checkSet(entity) !== false)
            $scope.remaining.push({"id": entity, "value": entity});
    });

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
            if(parseInt(entity.value) > $scope.games.length || parseInt(entity.value) < 0){
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

    if(checkSet(localStorage["week_data"]) && checkSet(localStorage["game_data"]) && checkSet(localStorage["week_id"]) && parseInt(localStorage["week_id"]) === parseInt($scope.week_id) && $scope.force === false){

        storeLocalGames($scope, null);

        if(objLength($scope.games) <= 0){

            $scope.force = true;

            getLiveData($scope, $http);

        }

        return true;

    }

    $scope.force = false;

    return $http.post( "./_listeners/listn.picks.php?method=GET", { "week_id" : $scope.week_id}).
        success(function(data, status) {

            $scope.status = status;
            storeLocalGames($scope, data);
            getGamesPicked($scope);

            return true;

        })
        .error(function(data, status) {
            $scope.data = data || "Request failed";
            $scope.status = status;

            return false;
        });



}

function getOldData($scope, $http){

    $scope.last_week_id = parseInt(week_id)-1;

    if(checkSet(localStorage["week_data_old"]) && checkSet(localStorage["game_data_old"]) && parseInt(localStorage["week_id"]) === parseInt($scope.week_id) && $scope.force === false){

        storeLocalGamesOld($scope, null);

        if(objLength($scope.gamesOld) <= 0){

            $scope.force = true;

            getOldData($scope, $http);

        }

        return true;

    }

    // Create the http post request
    // the data holds the keywords
    // The request is a JSON request.

    return $http.post("./_listeners/listn.picks.php?method=GET", { "week_id" : $scope.last_week_id}).
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

    $scope.games.forEach(function(entity){
        if(checkSet(entity.pick) !== false && parseInt(entity.pick.team_id) !== -1)
            $picks.push(entity.pick);
    });

    $scope.picks = JSON.parse(JSON.stringify($picks));

    if(checkSet($callback))
        $callback();

}

function refreshStoreLocalOld($scope){

    localStorage["week_data_old"] = JSON.stringify($scope.weekOld);
    localStorage["game_data_old"] = JSON.stringify($scope.gamesOld);

}


function storeLocalGamesOld($scope, data){

    if(data !== null || checkSet(localStorage["week_data"]) === false || $scope.force === true){

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

    if(checkSet(localStorage["week_data"]) === false || $scope.force === true){

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

    var $values = [];
    var $dupes = [];

    $scope.games.forEach(function(entity){

        if(parseInt(entity.pick.team_id) !== -1){
            if(indexOf.call($values, parseInt(entity.pick.value)) == -1)
                $values.push(parseInt(entity.pick.value));
            else
                $dupes.push(parseInt(entity.pick.value));
        }

    });

    $scope.games.forEach(function(entity){

        if(parseInt(entity.pick.team_id) !== -1){

            if(indexOf.call($dupes, parseInt(entity.pick.value)) > -1)
                entity.pick.bad = "true";
            else
                entity.pick.bad = "false";

        }

    });

    return $dupes;

}
