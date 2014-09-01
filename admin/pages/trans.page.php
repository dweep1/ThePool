<div class="fluid-row">
    <div id="angularDiv" data-ng-controller="UserController">

        <h3>Transaction Management</h3>

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

            <ul class="pages-list">

                <li data-object='credit' data-credit-id="-1">Add New Credit<i class='fa right fa-desktop'></i></li>

                <hr />

                <li class="alignleft">
                    <div class="width-10 aligncenter">Week ID</div>
                    <div class="width-30 alignleft">Date</div>
                    <div class="width-5"></div>
                    <div class="width-30 alignleft">Transaction ID</div>
                    <div class="width-10"></div>
                    <div class="width-10 alignright">Delete?</div>
                </li>

                <li class="alignleft" data-ng-repeat="item in credits | orderBy:'-id'" >
                    <div class="width-10 aligncenter">{{ item.week_id }}</div>
                    <div class="width-30 alignleft">{{ item.date }}</div>
                    <div class="width-5"></div>
                    <div class="width-30 alignleft">{{ item.nid }}</div>
                    <div class="width-10"></div>
                    <div class="width-10 alignright" data-delete-id="{{ item.id }}"><i class='fa fa-times'></i></div>
                </li>

            </ul>

        </div>

    </div>
</div>

<script>

    currentUser = <?php echo users::returnCurrentUser()->id; ?>;

    function UserController($scope, $http) {

        $http.post("admin.json.php", { "data" : "users"}).
            success(function(data, status) {

                $scope.status = status;
                $scope.users = data;

                $scope.users.forEach(function(entity){
                    entity.id = parseInt(entity.id);
                });

                getLiveData($scope, $http);
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

    }

    function getLiveData($scope, $http){

        if(checkSet(localStorage["selected_user"]))
            $scope.user_id = parseInt(localStorage["selected_user"]);
        else
            $scope.user_id = parseInt(currentUser);

        return $http.post("./listn.credits.php?method=GET", {"user_id" : $scope.user_id}).
            success(function(data, status) {

                $scope.status = status;
                $scope.credits = data;

                $scope.credits.forEach(function(entity){
                    entity.id = parseInt(entity.id);
                });

                return true;

            })
            .error(function(data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;

                return false;
            });
    }



    $(document).on("mousedown", "[data-delete-id]", function (e) {

        var $confirm = confirm("Are you sure you want to delete this credit?");

        if ($confirm == true){

            $.ajax({
                url: './admin.credit.php',
                type: 'post',
                data: {submitType: 2, className: "credit", id: parseInt($(this).attr('data-delete-id'))},
                success: function(data) {

                    angular.element(document.getElementById('angularDiv')).scope().selectUser();

                    displayFieldMessage(data);

                }
            });

        }

    });

    $(document).on("mousedown", "[data-user-id]", function (e) {

        localStorage["selected_user"] = $(this).attr('data-user-id');

    });

    $(document).on("mousedown", "[data-credit-id]", function (e) {

        disabledEventPropagation(e);

        displayPopup(localStorage["selected_user"], "manage.credit.php", $(this).attr('data-object'));

    });

</script>