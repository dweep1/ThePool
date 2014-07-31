<?php

    include_once "./_header.php";

    FormValidation::generate();

    $user = users::returnCurrentUser();

    if($user === false || !$user->verifyAuth())
        header("Location: ./logout.php");

    $teams = teams::getTeamsList();

    $thisWeek = (isset($_GET['week'])) ? week::selected($_GET['week']) : ((week::selected() !== false) ? week::selected() :  week::getCurrent());
    //$games->getList("week_id asc", array("week_id" => $this->id));
    $weeks = $thisWeek->getList("week_id asc", array("season_id" => $thisWeek->season_id));

    $games = $thisWeek->getGames();

    $picks = new pick();
    $picks = $picks->getList("user_id asc", array("week_id" => $thisWeek->id));

    $usersList = $user->getList();
    $usersKeys = [];

    foreach($picks as $value){

        if(!array_key_exists($value->user_id, $usersKeys)){
            $usersKeys[$value->user_id] = $value->user_id;
        }

    }

    foreach($usersList as $key => $tempUser){

        if(!array_key_exists($tempUser->id, $usersKeys)){
            unset($usersList[$key]);
        }else{

            $usersList[$key]->picks = [];

            foreach($picks as $pickKey => $value){

                if($value->user_id === $tempUser->id){
                    $usersList[$key]->picks[$value->game_id] = $value;
                    unset($picks[$pickKey]);
                }

            }

        }

    }

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

</head>
<body class="height-100">

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

<div id="content-area" class="aligncenter">
    <div class="width-90 full fluid-row aligncenter">

        <div class="fluid-row alignleft">
            <h4 style="display:inline-block; padding-right:40px; vertical-align: middle;">Pick Results</h4>
            <div style="display:inline-block">
                <form action="./_listeners/listn.results.php" method="post">
                    <select id="week_selection" name="week_selection">
                        <?php
                        foreach($weeks as $value){

                            if($thisWeek->id == $value->id)
                                echo "<option value='{$value->id}' selected>Week {$value->week_number}</option>";
                            else
                                echo "<option value='{$value->id}'>Week {$value->week_number}</option>";
                        }
                        ?>
                    </select>
                    <button class="ui-button dark">Go!</button>
                </form>
            </div>
        </div>

        <div class="fluid-row slim alignleft">
            <h5 style="display:inline-block">Week #<?php echo $thisWeek->week_number; ?>'s Results</h5>
            <h4 style="display:inline-block; cursor:pointer" data-link="./results.print.php?week=<?php echo week::selected()->id; ?>" class="float-right" title="Printer Friendly Version" ><i class="fa fa-print"></i></h4>
        </div>

        <div class="fluid-row slim aligncenter">

            <table class="ui-globalpicks alignleft" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="name" style="width:80px;">
                           User <i class="fa fa-info-sign"></i></th>
                        <?php
                            $count = 1;

                            foreach($games as $game){

                                echo "<th>{$count}</th>";

                                $count++;
                            }
                        ?>
                        <th data-sort="int"> Points</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                        $alt = "";

                        foreach($usersList as $tempUser){

                            $total = 0;
                            $percentage = ["correct" => 0, "total" => 0];

                            if($tempUser->id == $user->id){
                                echo "<tr class='$alt selected'><td class='username'>{$tempUser->username}</td>";
                            }else{
                                echo "<tr class='$alt'><td class='username'>{$tempUser->username}</td>";
                            }

                            foreach($games as $game){

                                if(isset($tempUser->picks[$game->id])){

                                    if($game->getWinner() == $tempUser->picks[$game->id]->team_id){
                                        $total += $tempUser->picks[$game->id]->value;
                                        $percentage["correct"]++;

                                        echo "<td>{$teams[$tempUser->picks[$game->id]->team_id]->abbr}  <small><b>+{$tempUser->picks[$game->id]->value}</b></small></td>";

                                    }else{

                                        echo "<td>{$teams[$tempUser->picks[$game->id]->team_id]->abbr} <small>{$tempUser->picks[$game->id]->value}</small></td>";

                                    }

                                    $percentage["total"]++;

                                }else{

                                    echo "<td></td>";

                                }

                            }

                            $alt = ($alt == "a") ? "" : "a";

                            $percentage = number_format((($percentage["correct"]/$percentage["total"])*100), 1);

                            echo "<td>{$total} <small title='Pick Percentage'>({$percentage})</small></td>";

                        }

                ?>


                </tbody>
            </table>

        </div>
    </div>

    <div class="clear-fix"></div>

</div>

<?php

    include "./_footer.php";

?>