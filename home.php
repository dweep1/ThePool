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

        <div class="fluid-row slim alignleft"><h2 data-trans-for="current_picks">Current Picks</h2></div>
        <div data-trans-id="current_picks">
            <div class="fluid-row slim alignleft">
                <h6>Search Games:</h6>  <input type="text" data-ng-model="search" />
            </div>

            <div class="fluid-row slim aligncenter">

                <ul class="ui-games-list">

                    <li ng-view class="velocity-opposites-transition-slideUpBigIn" data-velocity-opts="{ stagger: 150, drag: true }" data-ng-repeat="item in (filtered = (games | filter:search | orderBy:'id'))"  >
                        <div class="team alignleft" style="background-image: url('{{ item.away_team.image_url }}')">
                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>
                        </div>
                        <div class="middle">@</div>
                        <div class="team alignright" style="background-image: url('{{ item.home_team.image_url }}')">
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
<script src="./js/velocity.ui.js"></script>
<script src="./js/angular-velocity.min.js"></script>
<script src="./js/modernizr.js"></script>
<script src="./js/general.js"></script>
<script src="./js/bug_report.js"></script>

<script>
    angular.module('myHome', ['angular-velocity']);
    //angular.module('myHome', ['ngAnimate']);

</script>

<script>
    function RowController($scope, $http) {

        //$scope.week_id = <?php echo week::getCurrent(); ?>;
        $scope.week_id = 29;

        $scope.url = "./_listeners/listn.picks.php?method=GET";

        // Create the http post request
        // the data holds the keywords
        // The request is a JSON request.

        $http.post($scope.url, { "week_id" : $scope.week_id}).
            success(function(data, status) {

                $scope.status = status;
                $scope.week = data;
                $scope.games = data.games;

            })
            .
            error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });

    }
</script>


<?php if(isset($_SESSION['result'])) unset($_SESSION['result']); ?>

</body>
</html>