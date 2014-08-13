<div class="fluid-row">
    <h3>Picks Management</h3>

    <br/>

    <h6>Search</h6>
    <input type="text" data-ng-model="search" />

    <br/>

    <div class="fluid-row width-30 no-padding">

        <div data-ng-controller="RowController">

            <ul class="pages-list">

                <li class="alignleft">
                    <div class="width-10 aligncenter">ID</div>
                    <div class="width-30 alignleft">Username</div>
                    <div class="width-5"></div>
                    <div class="width-50 alignleft">Email</div>
                </li>

                <li class="alignleft" data-ng-repeat="item in users | filter:search | orderBy:'id'" data-object='users' data-user-id='{{ item.id }}' >
                    <div class="width-10 aligncenter">{{ item.id }}</div>
                    <div class="width-30 alignleft">{{ item.username }}</div>
                    <div class="width-5"></div>
                    <div class="width-50 alignleft">{{ item.email }}</div>
                </li>

            </ul>

        </div>

    </div>

    <div class="fluid-row width-60 no-padding">

        <div class="fluid-row width-50 slim">

            <div data-ng-controller="GameController">

                <ul class="pages-list">
                    <li data-ng-if="(item.home_team - 0) <= 0" class="aligncenter" data-ng-repeat="item in games | filter:search | orderBy:'id'" data-object='game' data-game-id='{{ item.id }}' >
                        <div class="width-70 aligncenter" ng-init="item.bye = 'bye week'">Bye Week</div>
                    </li>

                    <li data-ng-if="(item.home_team - 0) > 0" class="aligncenter" data-ng-repeat="item in games | filter:search | orderBy:'id'" data-object='game' data-game-id='{{ item.id }}' >
                        <div class="width-25 alignleft">{{ item.away.city }}</div>
                        <div class="width-5 aligncenter" data-ng-if="(item.away_score - 0) > 0">{{ item.away_score }}</div>
                        <div class="width-5 aligncenter">@</div>
                        <div class="width-5 aligncenter" data-ng-if="(item.home_score - 0) > 0">{{ item.home_score }}</div>
                        <div class="width-25 alignright">{{ item.home.city }}</div>
                        <div class="width-10 aligncenter">on</div>
                        <div class="width-20 alignleft">{{ item.date }}</div>
                    </li>

                </ul>

            </div>

        </div>

        <div class="fluid-row width-50 slim" id="data-list">

        </div>

    </div>
</div>

<script>

    function GameController($scope, $http) {

        $scope.url = "games.json.php";

        $http.post($scope.url, { "data" : "game"}).
            success(function(data, status) {

                $scope.status = status;
                $scope.games = data;

                $scope.games.forEach(function(entity){
                    entity.id = parseInt(entity.id);
                });

                $scope.keywords = "teams";

                getObjects($scope, $http);

            })
            .
            error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });

    }

    function formatData($scope){

        $scope.games.forEach(function(entity){

            $scope.data.forEach(function(team){
                if(team.id == parseInt(entity.home_team))
                    entity.home = team;
                else if(team.id == parseInt(entity.away_team))
                    entity.away = team;
            });

        });

    }

    $(document).on("mousedown", "[data-game-id]", function (e) {

        disabledEventPropagation(e);

        displayData($(this).attr('data-game-id'), "manage.game.data.php", $(this).attr('data-object'));

    });

    function RowController($scope, $http) {

        $scope.url = "admin.json.php";

        $http.post($scope.url, { "data" : "users"}).
            success(function(data, status) {

                $scope.status = status;
                $scope.users = data;

                $scope.users.forEach(function(entity){
                    entity.id = parseInt(entity.id);
                });
            })
            .
            error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });


    }

    $(document).on("mousedown", "[data-user-id]", function (e) {

        localStorage["selected_user"] = $(this).attr('data-user-id');

    });

</script>