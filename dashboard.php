<?php

include_once "./_header.php";

FormValidation::generate();

$user = users::returnCurrentUser();

if($user === false || !$user->verifyAuth())
    header("Location: ./logout.php");


?>

<!DOCTYPE html>
<html>
<head>

    <title>The Pool</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.18/angular-animate.min.js"></script>
    <script src="./js/jquery.velocity.min.js"></script>
    <script src="./js/velocity.ui.min.js"></script>
    <script src="./js/angular-velocity.min.js"></script>
    <script src="./js/modernizr.min.js"></script>
    <script src="./js/general.js"></script>
    <script src="./js/Chart.js"></script>

    <script>

        week_id = <?php echo week::getCurrent()->id; ?>;

    </script>

    <script src="./js/dashboard.js"></script>

</head>
<body class="height-100" data-ng-app="myDash">

<?php if(isset($_SESSION['result'])): ?>

    <div class="ui-message-background hidden" data-background-id="1"></div>
    <div class="ui-message-box" data-type="result" data-message-id="1">
        <i class="fa fa-times-circle float-right ui-message-close" data-close-id="1"></i>
        <h5>Result</h5>
        <div class="faux-row"><?php echo $_SESSION['result']; ?></div>
    </div>

<?php endif; ?>

<?php

    include "./menu.php";

?>

<div id="content-area" data-ng-controller="RowController">
    <div class="width-50 fluid-row first">

        <div class="fluid-row slim alignleft">

            <h4>Pick Performance <i data-trans-for="pick_performance" class="fa fa-bars"></i></h4>

            <div data-trans-id="pick_performance">

                <div id="perChart" class="fluid-row slim aligncenter">

                    <div class="fluid-row slim alignleft" id="performanceLegend"></div>

                    <canvas id="performanceChart"></canvas>

                </div>

            </div>

        </div>

        <div class="fluid-row slim alignleft">

            <h4>User Rank <i data-trans-for="rank" class="fa fa-bars"></i></h4>

            <div data-trans-id="rank" class="aligncenter">

                <div class="fluid-row slim"></div>

                <div id="rankInfo" class="fluid-row slim aligncenter">

                </div>

            </div>


        </div>

    </div>

    <div class="fluid-row width-50 dark height-100 float-right secondary">

        <div class="fluid-row width-50 alignleft">
            <h5>Rank by Points</h5>
            <div class="fluid-row slim"></div>
            <ul class="ui-rank-list">
                <li class="title alignleft">
                    <div class="width-40">Name</div>
                    <div class="width-25">Rank</div>
                    <div class="width-25">Points</div>
                </li>
                <li data-ng-repeat="item in rankList | orderBy:'-total'"
                    data-ng-class="{'highlight': item.userID == myUserID, 'rival': rivals.indexOf(item.userID) != -1}">
                    <div class="width-40">{{ item.username }}</div>
                    <div class="width-25">{{ $index + 1 }}</div>
                    <div class="width-25">{{ item.total }}</div>

                </li>

            </ul>

        </div>

        <div class="fluid-row width-50 alignleft">
            <h5>Rank by Percentage</h5>
            <div class="fluid-row slim"></div>
            <ul class="ui-rank-list">
                <li class="title">
                    <div class="width-40">Name</div>
                    <div class="width-25">Rank</div>
                    <div class="width-25">Percent</div>
                </li>
                <li data-ng-repeat="item in rankList | orderBy:'-percent'"
                    data-ng-class="{'highlight': item.userID == myUserID, 'rival': rivals.indexOf(item.userID) != -1}">
                    <div class="width-40">{{ item.username }}</div>
                    <div class="width-25">{{ $index + 1 }}</div>
                    <div class="width-25">{{ item.percent }}%</div>
                </li>
            </ul>

        </div>

    </div>

    <div class="clear-fix"></div>

</div>

<?php

include "./_footer.php";

exit;

?>

<div class="onecol" style="vertical-align: top;">
    <?php

    include "tpl.dashboard.menu.php";

    ?>
    <div id="standings" class="box white-solid" style="height:190px; width: 300px; padding:10px;">
        <div class="scrollme" style="height:190px">
            <table>

                <tr>
                    <th style="width:80px;">Name</th>
                    <th>Rank</th>
                    <th>Points</th>
                </tr>

                <?php

                $count = 1;

                foreach(userstat::getRankingList() as $value){

                    $username = getUserName($value['user_id']);
                    $points = $value['stat_var'];
                    $rank = $count;

                    if($username !== false){

                        if(intval($value['user_id']) === intval(user::current_user_id())){
                            echo "<tr selected>
											<td>$username</td>
											<td>$rank</td>
											<td>$points</td>
										  </tr>";
                        }else{

                            echo "<tr>
											<td>$username</td>
											<td>$rank</td>
											<td>$points</td>
										  </tr>";

                        }

                        $count++;

                    }

                }

                ?>
            </table>
        </div>
    </div>

    <div id="standings" class="box white-solid" style="height:190px; width: 300px; padding:10px;">
        <div class="scrollme" style="height:190px">
            <table>

                <tr>
                    <th class="name" style="width:80px;">Name</th>
                    <th>Rank</th>
                    <th>Percentage</th>
                </tr>

                <?php

                $count = 1;

                foreach(userstat::getPercentageList() as $value){

                    $username = getUserName($value['user_id']);
                    $points = $value['stat_var'];
                    $rank = $count;

                    $points = number_format(($points*100), 2);

                    if($username !== false){

                        if(intval($value['user_id']) === intval(user::current_user_id())){
                            echo "<tr selected>
										<td>$username</td>
										<td>$rank</td>
										<td>{$points}%</td>
									  </tr>";
                        }else{

                            echo "<tr>
										<td>$username</td>
										<td>$rank</td>
										<td>{$points}%</td>
									  </tr>";

                        }

                        $count++;

                    }



                }

                ?>
            </table>
        </div>
    </div>

</div>
<div class="twocol" style="vertical-align: top;">
<div id="user-content" class="box">
<div class="inner blue-solid">
    <h3>Pick Performance</h3>
    <h6>By Points, Personal vs <i>Global Average</i></h6>
    <br/>
    <canvas id="canvas" height="150" width="800" style="margin-left:-10px;"></canvas>
    <div style="text-align: center;">
        <div class="last-numbers">

            <?php

            $stats = array();
            $stats['rank'] = $userStats['rank']['rank'];
            $stats['total'] = $userStats['6']['stat_var'];
            $stats['average'] = number_format(($userStats['average']['6']['value']), 2);
            $stats['pick'] = number_format(($userStats['13']['stat_var']*100), 2);;

            echo "
									<h4>{$stats['rank']}<sup>rank</sup></h4>
									<h4>+{$stats['total']}<sup>total pts</sup></h4>
									<h4>+{$stats['average']}<sup>avg weekly</sup></h4>
									<h4>{$stats['pick']}%<sup>pick</sup></h4>";

            ?>
            <h6><i><?php echo user::getUserName_ByID($user_id); ?></i>'s Personal Performance</h6>
        </div>
    </div>
</div>
<div id="highlight-content" class="inner white-solid">
<h3>Leader Highlights</h3>
<h6>Weekly Stats, <i>Global Leaders</i> vs You</h6>
<br/>
<div class="table-block">
    <table>

        <tr>
            <th style="width:80px;">Name</th>
            <th>Rank</th>
            <th>Points</th>
        </tr>

        <?php

        $count = 1;

        foreach($players as $value){

            $username = getUserName($value['user_id']);
            $points = $value['stat_var'];
            $rank = $count;

            if($username !== false){

                if(intval($value['user_id']) === intval($user_id)){
                    echo "<tr selected>
										<td>$username</td>
										<td>$rank</td>
										<td>$points</td>
									  </tr>";
                }else{

                    echo "<tr>
										<td>$username</td>
										<td>$rank</td>
										<td>$points</td>
									  </tr>";

                }

                $count++;

            }

            if($count > 5){
                break;
            }

        }

        ?>
    </table>
    <br/><br/>
    <table>

        <tr>
            <th class="name" style="width:80px;">Name</th>
            <th>Rank</th>
            <th>Percent</th>
        </tr>

        <?php

        $count = 1;

        foreach($percentages as $value){

            $username = getUserName($value['user_id']);
            $points = $value['stat_var'];
            $rank = $count;

            $points = number_format(($points*100), 2);

            if($username !== false){

                if(intval($value['user_id']) === intval($user_id)){
                    echo "<tr selected>
										<td>$username</td>
										<td>$rank</td>
										<td>{$points}%</td>
									  </tr>";
                }else{

                    echo "<tr>
										<td>$username</td>
										<td>$rank</td>
										<td>{$points}%</td>
									  </tr>";

                }

                $count++;

            }

            if($count > 5){
                break;
            }

        }

        ?>
    </table>
</div>

<div class="breakdown-block">
    <div class="figures aligncenter">

        <script>

            var pieData = [

                <?php

                $limit = 3;

            $collective = array();
            $collective['week_count'] = 0;
            $collective['points'] = 0;
            $collective['points_avg'] = 0;
            $collective['percent'] = 0;
            $collective['percent_avg'] = 0;
            $collective['rank'] = 0;
            $collective['rank_avg'] = 0;
            $collective['rank_move'] = 0;

            $colors = array();

            $colors[0] = "#006086";
            $colors[1] = "#b5c526";
            $colors[2] = "#ea1c28";
            $colors[3] = "#ea1c28";
            $colors[4] = "#F38630";

            $descString = "";

            for($i = 0; $i < $limit; $i++){

                $id = $previous_week['id']-$i;

                $week_number = pool::getWeekNumber($id);

                if($week_number != 0){

                    $percentage = userstat::getPercentageData($id);
                    $ranking = userstat::getPlayerRanking($user_id, $id);
                    $percent = number_format(($percentage['stat_var']*100), 0);
                    $rank = $ranking['rank'].ordinal_suffix($ranking['rank']);

                    if($ranking['value'] > 0){

                        $collective['week_count']++;
                        $collective['points'] += $ranking['value'];
                        $collective['points_avg'] = $collective['points']/$collective['week_count'];
                        $collective['rank'] += $ranking['rank'];
                        $collective['rank_avg'] = $collective['rank']/$collective['week_count'];
                        $collective['percent'] += $percentage['stat_var'];
                        $collective['percent_avg'] = $collective['percent']/$collective['week_count'];

                        $color = ($i < 3) ? $colors[$i] : $colors[2];
                        $points = $ranking['value'];
                        $label = " Week ".$week_number." Rank - ".$rank;

                        if($i > 0){
                            echo ",";
                        }

                        $descString .= "
                                <div class=\"descString\" style=\"color:$color;\">
                                <h6>Week {$week_number}</h6>
                                <h2>+{$points} pts </h2>
                                <h6>{$percent}% Rank $rank</h6></div>";

                        echo "
                            {
                                value : $points,
                                color : \"$color\",
                                label : '$label',
                                labelColor : 'white',
                                labelFontSize : '14',
                                labelAlign : 'center'
                            }";

                    ?>



                <?php


                }else{
                    $limit++;
                }
            }

        }

        ?>


            ];

        </script>

        <canvas id="canvas-five" height="300" width="300"></canvas>

        <div id="centerChart">
            <h3><?php echo $collective['points']; ?></h3>
            <h6>Points</h6>
        </div>

        <div id="desc">
            <?php echo $descString; ?>
        </div>

    </div>



    <?php
    $collective['rank_avg'] = number_format(($collective['rank_avg']), 0);
    $collective['percent_avg'] = number_format(($collective['percent_avg']*100), 2);
    $collective['points_avg'] = number_format(($collective['points_avg']), 2);

    echo "<div class=\"last_numbers\"><h3>Last 3 Week Averages</h3>
							<h4>{$collective['rank_avg']}<sup>Rank</sup></h4>
							<h4>+{$collective['points']} pts (+{$collective['points_avg']}<sup>Avg</sup>)</h4>
							<h4>{$collective['percent_avg']}%</h4></div>";

    ?>


</div>

<script>

    var lineChartData = {
        labels : [

            <?php
                $weekString = "";

                foreach($weeks as $value){

                    $weekString .= "'Week {$value['week_number']}',";

                }

                $weekString = substr($weekString, 0, -1);

                echo $weekString;

            ?>

        ],datasets : [
            {
                fillColor : "rgba(220,220,220,0.0)",
                strokeColor : "rgba(60,60,60, 0.2)",
                pointColor : "rgba(27,206,245, 1)",
                pointStrokeColor : "rgba(0,96,134,0.2)",
                data : [

                    <?php

                        $dataString = "";

                        foreach($weeks as $value){

                            if(isset($globalPerformance[$value['id']]))
                                $weekData = $globalPerformance[$value['id']];
                            else
                                $weekData['value'] = 0;

                            $weekData['value'] = number_format(($weekData['value']), 2);

                            $dataString .= "{$weekData['value']},";

                        }

                        $dataString = substr($dataString, 0, -1);

                        echo $dataString;

                    ?>

                ]
            },
            {
                fillColor : "rgba(220,220,220,0.0)",
                strokeColor : "rgba(255,254,255,1)",
                pointColor : "#19cdf3",
                pointStrokeColor : "rgba(0,96,134,1)",
                data : [

                    <?php

                    $dataString = "";


                    foreach($weeks as $value){

                        if(!isset($userPerformance[$value['id']])){
                            $weekData['stat_var'] = 0;
                        }else{
                            $weekData = $userPerformance[$value['id']];
                        }


                        $dataString .= "{$weekData['stat_var']},";

                    }

                    $dataString = substr($dataString, 0, -1);

                    echo $dataString;


                    ?>

                ]
            }

        ]

    }

</script>