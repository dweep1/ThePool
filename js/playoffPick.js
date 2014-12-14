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

    $scope.picks = [{"points" : 1, "team" : null},{"points" : 2, "team" : null}];

    $scope.teams = [{"name" : "Green Bay", "drag": true}, {"name": "Sanfran", "drag": true}];

}
