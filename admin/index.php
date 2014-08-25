<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

FormValidation::generate();
$user = users::returnCurrentUser();

if(!is_object($user))
    header("Location: ../logout.php");

global $si;

    if(isset($_GET['si']))
        $_SESSION['si'] = $_GET['si'];
    else if(!isset($_SESSION['si']))
        $_SESSION['si'] = 1;

    $si = $_SESSION['si'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>The Pool Admin</title>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/admin.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
	<link href="./css/redactor.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script src="../js/jquery.velocity.min.js"></script>
    <script src="../js/velocity.ui.min.js"></script>
    <script src="../js/angular-velocity.min.js"></script>
    <script src="../js/modernizr.min.js"></script>
    <script src="./js/redactor.min.js"></script>
    <script src="../js/bug_report.js"></script>
    <script src="../js/general.js?ver=<?php echo VERSION ?>"></script>
    <script src="./js/admin.js?ver=<?php echo VERSION ?>"></script>
    <script>

        <?php

            if(isset($_SESSION['result'])){
                echo "setTimeout(function(){displayFieldMessage(\"{$_SESSION['result']}\", 10000);},400);";

                unset($_SESSION['result']);
            }

        ?>
    </script>

</head>
<body data-ng-app="myAdmin">

    <div id="fieldbody"></div>

    <div id="loadedContent"></div>

    <?php if($user->verifyAdmin() === false): ?>

        <div class="ui-message-background hidden" data-background-id="1"></div>
        <div class="ui-message-box aligncenter" data-type="nopass" data-message-id="1">
            <i class="fa fa-times-circle float-right ui-message-close" style="display:none" data-close-id="1"></i>
            <h6>Admin Login</h6>
            <form action="./admin.login.php" method="post">
                <input type="hidden" name="submitType" value="0" />
                <div class="faux-row"><input type="password" no-default autocomplete="off" name="password" /></div>
                <div class="faux-row"><input type="submit" class="ui-button float-right" value="Submit"></div>
            </form>
        </div>

        <script>

            angular.module('myAdmin', ['ngAnimate']);

        </script>

    </body>
    </html>

    <?php exit; endif; ?>


    <header id="header">

        <a href="../index.php"><h3>Sidekick Admin</h3></a>
        <div class="right con">

            <div style="display: inline-block; padding: 0px 20px;" data-ng-controller="TopController">

                <form action="listn.timeSelect.php" id="timeSelect" method="post">

                    <div style="display: inline-block; padding: 0px 5px;">
                        Season:
                        <select class="top" ng-model="search.season_id" ng-change="options.selected_season = search.season_id" name="selected_season">

                            <option data-ng-repeat="item in season | orderBy:'-id'" value="{{ item.id }}"
                                    data-ng-selected="item.id == options.selected_season">{{ item.text_id }}</option>

                        </select>
                    </div>

                    <div style="display: inline-block; padding: 0px 5px;">
                        Week:
                        <select class="top" ng-model="options.selected_week" name="selected_week" >

                            <option data-ng-repeat="item in week | orderBy:'id'" value="{{ item.id }}" data-ng-if="item.season_id == options.selected_season"
                                    data-ng-selected="item.id == options.selected_week">Week {{ item.week_number }}</option>

                        </select>
                    </div>

                    <button class="ui-buttons">Go</button>

                </form>

            </div>

            <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
        </div>

    </header>

	<?php
		include "menu.php";
	?>

	<section id="content-area">

        <?php

            $page = getAdminContent($si);

            if($page !== false)
                include "{$page}";

        ?>

	</section>

    <script>

        angular.module('myAdmin', ['angular-velocity']);

        function TopController($scope, $http) {

            $scope.url = "admin.json.php";

            $http.post($scope.url, { "data" : "week"}).
                success(function(data, status) {

                    $scope.status = status;
                    $scope.week = data;

                    $scope.week.forEach(function(entity){
                        entity.id = parseInt(entity.id);
                    });

                })
                .
                error(function(data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });

            $http.post($scope.url, { "data" : "options"}).
                success(function(data, status) {

                    $scope.status = status;

                    $scope.options = {};

                    data.forEach(function(entity){

                        pushToAry(entity.name, entity.value);

                    });

                    function pushToAry(name, val) {
                        $scope.options[name] = val;
                    }

                    if(checkSet(localStorage['selected_season']) !== false){
                        $scope.options.selected_season = parseInt(localStorage['selected_season']);
                    }else{
                        $scope.options.selected_season =  parseInt($scope.options.current_season);
                        localStorage['selected_season'] = $scope.options.selected_season;
                    }

                    if(checkSet(localStorage['selected_week']) !== false){
                        $scope.options.selected_week = parseInt(localStorage['selected_week']);
                    }else{
                        $scope.options.selected_week = parseInt("0");
                        localStorage['selected_week'] =  $scope.options.selected_week;
                    }

                })
                .
                error(function(data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });

            $http.post($scope.url, { "data" : "season"}).
                success(function(data, status) {

                    $scope.status = status;
                    $scope.season = data;

                    $scope.season.forEach(function(entity){
                        entity.id = parseInt(entity.id);
                    });

                })
                .
                error(function(data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });

            setTimeout(function(){

                $scope.$watch('options.selected_season', function() {

                    if(parseInt(localStorage["selected_season"]) != parseInt($scope.options.selected_season))
                        localStorage["selected_season"] = $scope.options.selected_season;

                });

                $scope.$watch('options.selected_week', function() {

                    if(parseInt(localStorage["selected_week"]) != parseInt($scope.options.selected_week))
                        localStorage["selected_week"] = $scope.options.selected_week;

                });

            },1000);

        }

    </script>

</body>
</html>