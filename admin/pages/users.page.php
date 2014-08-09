<div class="fluid-row">
    <h3>Users Directory</h3>

    <br/>

    <h6>Search</h6>
    <input type="text" data-ng-model="search" />

    <br/>

    <div class="fluid-row width-40 no-padding">

        <div data-ng-controller="RowController">

            <br/>

            <div class="fluid-row width-90 no-padding">
                Number of RealResults: {{ filtered.length }}
            </div>

            <ul class="pages-list">

                <li data-object='users' data-user-id='-1'>New User <i class="float-right fa fa-user"></i></li>

                <hr />

                <li class="animate-left-left" data-ng-repeat="item in (filtered = (data | filter:search | orderBy:'-id')) | limitTo: 20" data-object='users' data-user-id='{{ item.id }}' >
                    {{ item.username }}
                </li>

            </ul>

        </div>

    </div>

    <div class="fluid-row width-60" id="data-list">

    </div>
</div>

<script>
    function RowController($scope, $http) {

        $scope.keywords = "users";

        getObjects($scope, $http);

    }

    $(document).on("mousedown", "[data-user-id]", function (e) {

        disabledEventPropagation(e);

        displayData($(this).attr('data-user-id'), "manage.users.data.php", $(this).attr('data-object'));

    });

</script>