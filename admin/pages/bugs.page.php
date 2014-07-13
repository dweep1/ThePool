<div class="fluid-row">
    <h3>Bugs Directory</h3>

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

                <li class="animate-left-left" data-ng-repeat="item in (filtered = (data | filter:search | orderBy:'-id')) | limitTo: 20" data-object='bugs' data-id='{{ item.id }}' >
                    <b>{{ item.date | date:'MM-dd @ HH:mm'}}</b> - {{ item.ip_address }} <i data-object='bugs' data-edit-id='{{ item.id }}' class='fa right fa-pencil-square-o'></i>
                </li>

            </ul>


        </div>

    </div>

    <div class="fluid-row width-60" id="data-list">

    </div>
</div>

<script>
    function RowController($scope, $http) {

        $scope.keywords = "bugs";

        getObjects($scope, $http);

    }

    function formatData($scope){

        $.each($scope.data, function(index, element) {

            var t = $scope.data[index].date.split(/[- :]/);
            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

            $scope.data[index].date = Date.parse(d);
        });

    }

</script>