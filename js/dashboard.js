
var $chartData = {};

$(window).resize(function () {
    resizeCharts();
});

var myApp = angular.module('myDash', ['angular-velocity']);

function RowController($scope, $http) {

    $scope.force = false;

    getLiveStats($scope, $http);

    //$scope.$watch('games', function() {}, true);

}

function getLiveStats($scope, $http){

    $scope.week_id = week_id;

    return $http.post( "./_listeners/listn.dashboard.php?method=GET", { "week_id" : $scope.week_id}).
        success(function(data, status) {

            $scope.status = status;

            //storeLocalStats($scope, data);

            var $formatedPicks = [];
            var $formatedLabels = [];
            var $legend = $("#performanceLegend");

            data.userPicks.forEach(function(entity){

                $tempArray = {};
                $tempArray.title = entity.title;
                $tempArray.data = [];
                $tempArray.strokeColor = entity.strokeColor;
                $tempArray.pointColor = entity.pointColor;
                $tempArray.pointStrokeColor = entity.pointStrokeColor;

                entity.data.forEach(function(tempEnt){

                    $tempArray.data.push(tempEnt.value);

                });

                $formatedPicks.push($tempArray);

            });

            console.log($formatedPicks);

            data.weeks.forEach(function(entity){

                $formatedLabels.push("Week "+entity.week_number);

            });

            var options_two = {
                scaleShowLabels : false,
                scaleOverlay : false,
                scaleShowGridLines : false,
                animation : true,
                scaleFontColor : "#333",
                scaleLineColor : "rgba(255,255,255,0.0)",
                scaleGridLineWidth : 1,
                pointDotRadius : 5,
                pointDotStrokeWidth : 4,
                datasetStrokeWidth : 5
            }

            var chartData = {
                labels : $formatedLabels,
                datasets: []
            };

            $formatedPicks.forEach(function(entity){

                var $temp = {
                    label: entity.title,
                    fillColor : "rgba(220,220,220,0.0)",
                    strokeColor : entity.strokeColor,
                    pointColor : entity.pointColor,
                    pointStrokeColor : entity.pointStrokeColor,
                    data : entity.data
                };

                chartData.datasets.push($temp);

                var tempLegend = "<div class='ui-legend-item'>"+entity.title+" <i class='fa fa-user' style='color: "+entity.strokeColor+"'></i></div>";

                $legend.append(tempLegend)

            });



            var $charts = resizeCharts({ctxPicks: {picksData: chartData, optionData: options_two}});

            return true;

        })
        .error(function(data, status) {
            $scope.data = data || "Request failed";
            $scope.status = status;

            return false;
        });



}

function resizeCharts($chartData){

    if(checkSet($chartData.ctxPicks))
        $chartData['ctxPicks'] = $chartData.ctxPicks;

    var ctxPicksChart = document.getElementById("performanceChart").getContext("2d");

    var container =  $("#perChart");

    ctxPicksChart.canvas.width  = container.innerWidth()*0.9;
    ctxPicksChart.canvas.height = container.innerHeight()*0.9;

    var performanceChart = new Chart(ctxPicksChart).Line($chartData.ctxPicks.picksData, $chartData.ctxPicks.optionData);

    return {ctxPicks: ctxPicksChart};

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

function refreshStoreLocal($scope){

    localStorage["week_id"] = $scope.week_id;
    localStorage["week_data"] = JSON.stringify($scope.week);
    localStorage["game_data"] = JSON.stringify($scope.games);

}

function storeLocalStats($scope, data){

    if(checkSet(localStorage["week_data"]) === false || $scope.force === true){

        $scope.week = data;
        $scope.games = data.games;

        refreshStoreLocal($scope);

    }else{

        $scope.week = JSON.parse(localStorage["week_data"]);
        $scope.games = JSON.parse(localStorage["game_data"]);

    }

}
