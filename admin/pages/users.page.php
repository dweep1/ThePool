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

                <li data-link="../apply/index.php">Create New User<i class='fa right fa-desktop'></i></li>

                <hr />

                <li class="animate-left-left" data-ng-repeat="item in (filtered = (data | filter:search | orderBy:'-id')) | limitTo: 20" data-object='users' data-id='{{ item.id }}' >
                    {{ item.username }}  <i data-object='users' data-edit-id='{{ item.id }}' class='fa right fa-pencil-square-o'></i>
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
</script>