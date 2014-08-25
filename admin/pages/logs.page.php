<div class="fluid-row">
    <h3>Logs Directory</h3>

    <br/>

    <h6>Search</h6>
    <input type="text" data-ng-model="search.title" />

    <br/>

    <div class="fluid-row width-40 no-padding">

        <div data-ng-controller="RowController">

            <br/>

            <div class="fluid-row width-90 no-padding">
                Number of RealResults: {{ filtered.length }}
            </div>

            <ul class="pages-list">

                <li ng-animate="'animate-left-left'" data-ng-repeat="item in (filtered = (data | filter:search | orderBy:'-id')) | limitTo: 20" data-object='admin_log' data-id='{{ item.id }}' >
                    {{ item.type }} - {{ item.subject }}
                </li>

            </ul>


        </div>

    </div>

    <div class="fluid-row width-60" id="data-list">

    </div>
</div>

<script>
    function RowController($scope, $http) {

        $scope.keywords = "admin_log";

        getObjects($scope, $http);

    }
</script>

