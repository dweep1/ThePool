var myApp = angular.module('myHome', []);

function endClock(){
    angular.element(document.getElementById('content-area')).scope().doRefresh();
}

function RowController($scope, $http) {

    $scope.force = true;

    getLiveData($scope, $http);
    getOldData($scope,$http);

    $scope.doRefresh = function() {
        $scope.force = true;

        localStorage.clear();

        getLiveData($scope, $http);

    };

}

function getLiveData($scope, $http, $callback){

    $scope.week_id = week_id;

    if(checkSet(localStorage["week_data"]) && checkSet(localStorage["game_data"]) && checkSet(localStorage["week_id"])){

        if(parseInt(localStorage["week_id"]) === parseInt($scope.week_id) && $scope.force === false){

            storeLocalGames($scope, null, $callback);

            if(objLength($scope.games) <= 0){

                $scope.force = true;

                getLiveData($scope, $http, $callback);

            }

            return true;
        }

    }

    $scope.force = false;


    return $http.post( "./_listeners/listn.picks.php?method=GET", { "week_id" : $scope.week_id}).
        success(function(data, status) {

            $scope.status = status;
            storeLocalGames($scope, data, $callback);

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

function storeLocalGames($scope, data, $callback){


    if(checkSet(localStorage["week_data"]) === false || $scope.force === true){

        $scope.week = data;
        $scope.games = data.games;

        refreshStoreLocal($scope);

    }else{

        $scope.week = JSON.parse(localStorage["week_data"]);
        $scope.games = JSON.parse(localStorage["game_data"]);

    }

    if(checkSet($callback))
        $callback();

}

