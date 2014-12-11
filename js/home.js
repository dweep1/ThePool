var myApp = angular.module('myHome', []);

function endClock(){
    angular.element(document.getElementById('content-area')).scope().doRefresh();
}

function RowController($scope, $http) {

    getLiveData($scope, $http);
    getOldData($scope,$http);

    $scope.doRefresh = function() {

        getLiveData($scope, $http);

    };

}

function getLiveData($scope, $http, $callback){

    $scope.week_id = week_id;

    return $http.post("./_listeners/listn.picks.php?method=GET", { "week_id" : $scope.week_id}).
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

function storeLocalGamesOld($scope, data){

    $scope.weekOld = data;
    $scope.gamesOld = data.games;

}

function storeLocalGames($scope, data, $callback){

    $scope.week = data;
    $scope.games = data.games;

    if(checkSet($callback))
        $callback();

}

