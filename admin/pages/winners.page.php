<?php

include_once "./admin.header.php";

$winners = [];
$prizeValues = [];

$currentWeek = week::getCurrent();
$userList = new users;
$userList = $userList->getList();

$tempList = [];
foreach($userList as $value){
    $tempList[$value->id] = $value;
}
$userList = $tempList;
unset($tempList);

$userList[0] = new users(["username" => "No Winner", "email" => "N/A", "paypal"=> "N/A"]);

$weeks = renderWeekData(week::query(["orderBy" => "week_number asc"])->getList(["season_id" => season::getCurrent()->id]), $currentWeek);

$tempList = [];

foreach($weeks as $value){
    $tempList[$value->id] = $value;
}

$weeks = $tempList;
unset($tempList);

foreach($weeks as $value){
    $rankData = stat_log::getGlobalRankData($value->id);

    if(isset($rankData[0]))
        $winners[$value->id] = $rankData[0];
    else
        $winners[$value->id] = array("userID" => "0", "total"=> "0", "percent"=> "0");

    $prizeValues[$value->id] = week::getPoolAmount($value->id);
}

function renderWeekData($weekDataArray, $currentWeek){

    $weekDataTemp = [];

    foreach($weekDataArray as $value){
        if($value->date_end <= $currentWeek->date_start)
            array_push($weekDataTemp, $value);
    }

    array_push($weekDataTemp, $currentWeek);

    return $weekDataTemp;

}

?>

<div class="fluid-row width-90 alignleft no-padding">

    <ul class="pages-list">

        <li class="aligncenter">
            <div class="width-10 aligncenter">Week Number</div>
            <div class="width-5"></div>
            <div class="width-10 aligncenter">Payout Amount</div>
            <div class="width-5"></div>
            <div class="width-10 aligncenter">Username</div>
            <div class="width-5"></div>
            <div class="width-20 aligncenter">User Email</div>
            <div class="width-5"></div>
            <div class="width-20 aligncenter">Paypal</div>
        </li>

        <?php

        foreach($winners as $key => $value){

            echo " <li class='aligncenter'>
                    <div class='width-10 aligncenter'>Week {$weeks[$key]->week_number}</div>
                    <div class='width-5'></div>
                    <div class='width-10 aligncenter'>&#36;{$prizeValues[$key]}</div>
                    <div class='width-5'></div>
                    <div class='width-10 aligncenter'>{$userList[$value["userID"]]->username}</div>
                    <div class='width-5'></div>
                    <div class='width-20 aligncenter'>{$userList[$value["userID"]]->email}</div>
                    <div class='width-5'></div>
                    <div class='width-20 aligncenter'>{$userList[$value["userID"]]->paypal}</div>
                </li>";
        }
        ?>



    </ul>



</div>

<script>

    $(document).ready(setTimeout(dateTimeFormat(),200));

    $(document).on("mousedown", "#submitButton", function (e) {

        $("#subForm").submit();

    });

    $(document).on("mousedown", "#markBye", function (e) {

        var $confirm = confirm("Are you sure you want to mark this game as a bye week?");

        if ($confirm == true){
           $("#submitType").val("2");

           $("#subForm").submit();
        }

    });
</script>