"use strict";

$(document).on("mousedown", "[data-team-id]", function (e) {

    var $for = $(this).attr('data-team-id');

   $("#team-info").load("./tpl.picks.teaminfo.php?team_id="+$for);

});

function endClock(){
    angular.element(document.getElementById('content-area')).scope().doRefresh();
}

var myApp = angular.module('myHome', ['ngDragDrop']);

myApp.directive('ngDelay', ['$timeout', function ($timeout) {
    return {
        restrict: 'A',
        scope:true,
        compile: function (element, attributes) {
            var expression = attributes['ngChange'];
            if (!expression)
                return;

            attributes['ngChange'] = '$$delay.execute()';
            return {
                pre: function (scope, element, attributes) {
                    scope.$$delay = {
                        expression: expression,
                        delay: scope.$eval(attributes['ngDelay']),
                        execute: function () {
                            var state = scope.$$delay;
                            state.then = Date.now();
                            $timeout(function () {
                                if (Date.now() - state.then >= state.delay)
                                    scope.$eval(expression);
                            }, state.delay);
                        }
                    };
                }
            }
        }
    };
}]);

function RowController($scope, $http) {

    $scope.force = false;

    getLiveData($scope, $http);

    $scope.doRefresh = function() {
        $scope.force = true;

        localStorage.clear();

        getLiveData($scope, $http);
    };

    $scope.doSave = function() {
        savePicks($scope, $http);
    };

}

function savePicks($scope, $http){

    $scope.savedPicks = [];
    var teamCount = 0;

    $scope.points.forEach(function(point){

        if(checkSet(point.team)){
            $scope.savedPicks.push({"value": point.value, "team": parseInt(point.team.id)});
            teamCount++;
        }

    });

    if(teamCount < $scope.teams.length){

        teamCount = $scope.teams.length - teamCount;
        createMessageBox({
                type: "result",
                title: "result",
                message: "Picks NOT submitted, missing "+teamCount+" more teams. You need to assign points to EVERY team."
            });

        return false;

    }

    return $http.post("./_listeners/listn.playoff.picks.php?method=PUT", $scope.savedPicks).
        success(function(data, status) {

            $scope.status = status;

            createMessageBox({type: "result", title: "result", message: data.result});

            $scope.teams = [];

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

function savePicksALT($scope, $http){
    return $http.post("./_listeners/listn.picks.php?method=PUT", $scope.points).
        success(function(data, status) {

            $scope.status = status;

            createMessageBox(
                {type: "result", title: "result", message: data.result},
                function($messageID){toggleDisplayMessageBox($messageID);}
            );

            $scope.force = true;

            getLiveData($scope, $http);

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

function getLiveData($scope, $http){

    $scope.week_id = week_id;

    if(checkSet(localStorage["pick_data"]) &&
        checkSet(localStorage["team_data"]) &&
        checkSet(localStorage["week_id"]) &&
        parseInt(localStorage["week_id"]) === parseInt($scope.week_id) &&
        $scope.force === false){

        storeLocalGames($scope, null);

        if(objLength($scope.teams) <= 0){

            $scope.force = true;

            getLiveData($scope, $http);

        }else{
            getGamesPicked($scope);
        }

        return true;

    }

    $scope.force = false;

    //$scope.teams = [{"name" : "Green Bay", "drag": true}, {"name": "Sanfran", "drag": true}];
    //$scope.picks = [{"points" : 1, "team" : null},{"points" : 2, "team" : null}];

    return $http.post( "./_listeners/listn.playoff.picks.php?method=GET", { "week_id" : $scope.week_id}).
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


function refreshStoreLocal($scope){

    localStorage["week_id"] = $scope.week_id;
    localStorage["pick_data"] = JSON.stringify($scope.picks);
    localStorage["team_data"] = JSON.stringify($scope.teams);

}

function storeLocalGames($scope, data){

    if(checkSet(localStorage["pick_data"]) === false || $scope.force === true){

        $scope.picks = data.picks;
        $scope.teams = data.teams;

        refreshStoreLocal($scope);

    }else{

        $scope.picks = JSON.parse(localStorage["pick_data"]);
        $scope.teams = JSON.parse(localStorage["team_data"]);

    }

}



function getGamesPicked($scope){

    $scope.points = [];

    for(var $i = 1; $i <= 12; $i++){

        $scope.points.push({"value": $i, "drag": true, "team": false, "pick": false});

    }

    $scope.picks.forEach(function(pick){

        findLikePick($scope, pick);

    });

}

function findLikePick($scope, $pick){

    $scope.points.forEach(function(entity, index, theArray){

        if(entity.value === parseInt($pick.value)){

            if(entity.pick === false){

                theArray[index] = {
                    "value": parseInt($pick.value),
                    "drag": parseInt($pick.result) !== 0,
                    "team": false,
                    "pick": $pick
                };

                if(parseInt($pick.week_id) === week_id)
                    theArray[index].team = findTeamByID($scope, $pick.team_id);

            }else{

                if(entity.pick.week_id !== week_id){

                    theArray[index] = {
                        "value": parseInt($pick.value),
                        "drag": parseInt($pick.result) !== 0,
                        "team": false,
                        "pick": $pick
                    };
                }
            }

            return true;

        }
    });

}

function findTeamByID($scope, $team_id){

    var $return = false;

    $scope.teams.forEach(function(entity, index, theArray){

        if(entity.id == $team_id){

            $return = entity;

            delete theArray[index];

            return $return;

        }

    });

    return $return;

}
