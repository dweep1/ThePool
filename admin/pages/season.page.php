<div class="fluid-row">
    <h3>Season Directory</h3>

    <br/>

    <div class="fluid-row width-40">

        <div data-ng-controller="RowController">

            <ul class="pages-list">

                <li data-object='season' data-season-id='-1'>New Season <i class="float-right fa fa-calendar"></i></li>

                <hr />

                <li class="animate-left-left" data-ng-repeat="item in data | orderBy:'-id'" data-object='season' data-season-id='{{ item.id }}' >
                    {{ item.text_id }}
                </li>

            </ul>

        </div>

    </div>

    <div class="fluid-row width-60" id="data-list">

    </div>
</div>

<script>
    function RowController($scope, $http) {

        $scope.keywords = "season";

        getObjects($scope, $http);

    }

    function formatData($scope){

        $scope.data.forEach(function(entity){
            entity.id = parseInt(entity.id);
        });

    }

    $(document).on("mousedown", "[data-season-id]", function (e) {

        disabledEventPropagation(e);

        displayData($(this).attr('data-season-id'), "manage.season.data.php", $(this).attr('data-object'));

    });

</script>