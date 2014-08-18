<div class="fluid-row">
    <div data-ng-controller="GameController">

        <h3>Picks Management</h3>

        <div class="fluid-row width-30">

            <div class="fluid-row alignleft">
                <h6>Search Users:</h6>
                <input type="text" data-ng-model="search" />
            </div>

            <ul class="pages-list">

                <li class="alignleft">
                    <div class="width-10 aligncenter">ID</div>
                    <div class="width-30 alignleft">Username</div>
                    <div class="width-5"></div>
                    <div class="width-50 alignleft">Email</div>
                </li>

                <li class="alignleft" data-ng-repeat="item in users | filter:search | orderBy:'id'" ng-click="selectUser();" data-object='users' data-user-id='{{ item.id }}' >
                    <div class="width-10 aligncenter">{{ item.id }}</div>
                    <div class="width-30 alignleft">{{ item.username }}</div>
                    <div class="width-5"></div>
                    <div class="width-50 alignleft">{{ item.email }}</div>
                </li>

            </ul>

        </div>

        <div class="fluid-row width-60">

            <div class="fluid-row alignleft">
                <h6>Search Games:</h6>
                <div class="clear-fix"></div>
                <div class="float-left">
                    <input type="text" data-ng-model="gameSearch" />
                </div>
                <div class="float-right">
                    <button class="ui-buttons dark" ng-click="doRefresh()">Discard Changes</button>
                    <button class="ui-buttons dark" ng-click="doSave()">Save Picks</button>
                </div>
                <div class="clear-fix"></div>
            </div>

            <div class="fluid-row no-padding alignleft">

                <ul class="ui-games-list">

                    <li data-ng-repeat="item in games | filter:gameSearch | orderBy:'id'"
                        data-picked-id="{{ item.pick.team_id }}" data-bad-value="{{ item.pick.bad }}" >

                        <div data-ng-click="item.pick.team_id = item.away_team.id;"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.away_team.id }}"
                             data-ng-class="{true: 'team alignleft picked', false: 'team alignleft'}[item.pick.team_id == item.away_team.id]"
                             style="background-image: url('{{ item.away_team.image_url }}')">

                            <div class="gradient-left">
                                <h5>{{ item.away_team.city }}</h5>
                                <h6>{{ item.away_team.team_name }}</h6>
                            </div>

                        </div>


                        <div class="middle">

                            <i class="fa fa-minus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) - 1;"></i>

                            <input type="text" class="small" data-bad-value="{{ item.pick.bad }}" value="{{ item.pick.value }}"
                                   data-ng-model="item.pick.value" data-game-id="{{ item.id }}" />

                            <i class="fa fa-plus-circle" data-game-id="{{ item.id }}"
                               data-ng-click="item.pick.value = (item.pick.value - 0) + 1;"></i>

                        </div>


                        <div data-ng-click="item.pick.team_id = item.home_team.id"
                             data-pick-id="{{ item.pick.id }}" data-team-id="{{ item.home_team.id }}"
                             data-ng-class="{true: 'team alignright float-right picked', false: 'team alignright float-right'}[item.pick.team_id == item.home_team.id]"
                             style="background-image: url('{{ item.home_team.image_url }}')">

                            <div class="gradient-right">
                                <h5>{{ item.home_team.city }}</h5>
                                <h6>{{ item.home_team.team_name }}</h6>
                            </div>

                        </div>

                        <div class="clear-fix"></div>

                    </li>
                </ul>

            </div>

        </div>

    </div>
</div>

<script>

    currentUser = <?php echo users::returnCurrentUser()->id; ?>;

    function GameController($scope, $http) {

        getLiveData($scope, $http);

        $http.post("admin.json.php", { "data" : "users"}).
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

        $scope.selectUser = function() {
            setTimeout(function(){
                getLiveData($scope, $http);
            },200);
        };

        $scope.doRefresh = function() {
            getLiveData($scope, $http);
        };

        $scope.doSave = function() {
            savePicks($scope, $http);
        };

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

    function buildPicks($scope, $callback){

        var $picks = [];

        $scope.games.forEach(function(entity){
            if(checkSet(entity.pick) !== false && parseInt(entity.pick.team_id) !== -1)
                $picks.push(entity.pick);
        });

        $scope.picks = JSON.parse(JSON.stringify($picks));

        $scope.picks.forEach(function(entity){
            entity.user_id = localStorage["selected_user"];
        });

        if(checkSet($callback))
            $callback();

    }

    function savePicks($scope, $http){

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

            return $http.post("./listn.picks.php?method=PUT", $scope.picks).
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

        if(checkSet(localStorage["selected_user"]))
            $scope.user_id = parseInt(localStorage["selected_user"]);
        else
            $scope.user_id = parseInt(currentUser);

        localStorage["selected_user"] = $scope.user_id;

        return $http.post("./listn.picks.php?method=GET", {"user_id" : $scope.user_id}).
            success(function(data, status) {

                $scope.status = status;
                $scope.week = data;
                $scope.games = data.games;

                return true;

            })
            .error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;

                return false;
            });
    }


    $(document).on("mousedown", "[data-user-id]", function (e) {

        localStorage["selected_user"] = $(this).attr('data-user-id');

    });

</script>