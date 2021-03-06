<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

include "./bootstrap.php";

$user = users::returnCurrentUser();

if($user === false || !$user->verifyAuth())
    header("Location: ./logout.php");

$season = season::getCurrent();

if($season->type !== "playoff"):

    $teams = teams::getTeamsList();

    $thisWeek = (isset($_GET['week'])) ? new week($_GET['week']) : week::getCurrent();
    $weeks = week::loadMultiple(["season_id" => season::getCurrent()->id]);

    $games = $thisWeek->getGames();

    $picks = new pick();
    $picks = pick::query(["orderBy" => "user_id ASC"])->getList(["week_id" => $thisWeek->id]);

    $rivals = new rivals();
    $rivals = rivals::query(["orderBy" => "rival_id ASC"])->getList(["user_id" => $user->id]);

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

    foreach($usersList as $key => $tempUser){

        if((int) $tempUser->access_level === -1 && (int) $tempUser->id !== (int) $user->id){
            unset($usersList[$key]);
        }

    }

?>
<!DOCTYPE html>
<html>
<head>

    <title>The Pool - Results</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


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
    <div class="width-90 full fluid-row aligncenter first">

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

        <div class="fluid-row alignleft">
            <h5 style="display:inline-block">Week #<?php echo $thisWeek->week_number; ?>'s Results</h5>
            <h5>Pool Size: ~$<?php echo week::getPoolAmount($thisWeek->id); ?></h5>
            <h4 style="display:inline-block; cursor:pointer" data-link="./results.print.php?week=<?php echo $thisWeek->id; ?>" class="float-right" title="Printer Friendly Version" ><i class="fa fa-print"></i></h4>
            <br/>
            Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><b><a href="./settings.php" style="padding:10px;">Buy Credits</a></b>

        </div>

        <div class="fluid-row slim aligncenter">

            <table id="stupid" class="ui-globalpicks alignleft" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="name" style="width:130px;">
                           User <i class="fa fa-info-sign"></i></th>
                        <?php
                            $count = 1;

                            foreach($games as $game){

                                echo "<th class='aligncenter'>{$count}</th>";

                                $count++;
                            }
                        ?>
                        <th data-sort="int"><i class="fa fa-sort"></i> Points</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                        $alt = "";

                        foreach($usersList as $tempUser){

                            $total = 0;
                            $percentage = ["correct" => 0, "total" => 0];

                            $rival = rivals::findRival($tempUser->id, $rivals);

                            if($tempUser->id == $user->id){
                                echo "<tr class='$alt selected'><td class='username'>{$tempUser->username}</td>";
                            }else if($rival !== false){
                                $tempUser->username = (strlen($rival->rival_custom_name) > 2) ? $rival->rival_custom_name : $tempUser->username;
                                echo "<tr class='$alt rival'><td class='username'>{$tempUser->username}</td>";
                            }else{
                                echo "<tr class='$alt'><td class='username'>{$tempUser->username}</td>";
                            }

                            foreach($games as $game){

                                if(isset($tempUser->picks[$game->id]) && $game->isLocked()){

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

                            if($percentage["total"] > 0)
                                $percentage = number_format((($percentage["correct"]/$percentage["total"])*100), 1);
                            else
                                $percentage = number_format(0, 1);

                            echo "<td data-sort-value='{$total}'>{$total} <small title='Pick Percentage'>({$percentage})</small></td>";

                        }

                ?>


                </tbody>
            </table>

        </div>
    </div>

    <div class="clear-fix"></div>

</div>

<?php

else:


    $teams = teams::getTeamsList();

    $thisWeek = (isset($_GET['week'])) ? new week($_GET['week']) : week::getCurrent();

    $weeks = week::query()->getList(["season_id" => season::getCurrent()->id]);

    $games = game::query(["orderBy" => "date ASC"])->getList(["week_id" => $thisWeek->id]);

    foreach($games as $key => $value){
        if((int) $value->home_team === 0)
            unset($games[$key]);
    }

    $tempTeams = [];

    foreach($games as $value){

        $tempTeams[$value->home_team] = $teams[$value->home_team];
        $tempTeams[$value->away_team] = $teams[$value->away_team];

    }

    $teams = $tempTeams;
    unset($tempTeams);

    $picks = new pick();
    $picks = pick::query(["orderBy" => "user_id ASC"])->getList(["week_id" => $thisWeek->id]);

    $rivals = new rivals();
    $rivals = rivals::query(["orderBy" => "rival_id ASC"])->getList(["user_id" => $user->id]);

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
                    $usersList[$key]->picks[$value->team_id] = $value;
                    unset($picks[$pickKey]);
                }

            }

        }

    }

    foreach($usersList as $key => $tempUser){

        if((int) $tempUser->access_level === -1 && (int) $tempUser->id !== (int) $user->id){
            unset($usersList[$key]);
        }

    }

    /**
    * @param $id
    * @param $games
    * @return game|bool
    */

    function getGameFromID($id, &$games){

        foreach($games as $val){
            if($val->id == $id)
                return $val;
        }

        return false;

    }

    ?>
    <!DOCTYPE html>
    <html>
    <head>

        <title>The Pool - Results</title>
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">
        <link rel="icon" type="image/x-icon" href="./favicon.ico">
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="./css/index.css?ver=<?php echo VERSION ?>" rel="stylesheet" type="text/css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


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
        <div class="width-90 full fluid-row aligncenter first">

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

            <div class="fluid-row alignleft">
                <h5 style="display:inline-block">Week #<?php echo $thisWeek->week_number; ?>'s Results</h5>
                <h5>Pool Size: ~$<?php echo week::getPoolAmount(false, true); ?></h5>
                <h4 style="display:inline-block; cursor:pointer" data-link="./results.print.php?week=<?php echo $thisWeek->id; ?>" class="float-right" title="Printer Friendly Version" ><i class="fa fa-print"></i></h4>
                <br/>
                Remaining Credits: <?php echo credit::getCreditCount(null, -1); ?><b><a href="./settings.php" style="padding:10px;">Buy Credits</a></b>

            </div>

            <div class="fluid-row slim aligncenter">

                <table id="stupid" class="ui-globalpicks alignleft" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th class="name" style="width:130px;">
                            User <i class="fa fa-info-sign"></i></th>
                        <?php

                        foreach($teams as $team){

                            echo "<th class='aligncenter'>{$team->abbr}</th>";

                        }
                        ?>
                        <th data-sort="int"><i class="fa fa-sort"></i> Points</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php

                    $alt = "";

                    foreach($usersList as $tempUser){

                        $total = 0;
                        $percentage = ["correct" => "", "total" => 0];

                        $rival = rivals::findRival($tempUser->id, $rivals);

                        if($tempUser->id == $user->id){
                            echo "<tr class='$alt selected'><td class='username'>{$tempUser->username}</td>";
                        }else if($rival !== false){
                            $tempUser->username = (strlen($rival->rival_custom_name) > 2) ? $rival->rival_custom_name : $tempUser->username;
                            echo "<tr class='$alt rival'><td class='username'>{$tempUser->username}</td>";
                        }else{
                            echo "<tr class='$alt'><td class='username'>{$tempUser->username}</td>";
                        }

                        foreach($teams as $team){

                            if(isset($tempUser->picks[$team->id])){
                                $tempGame = getGameFromID($tempUser->picks[$team->id]->game_id, $games);

                                if($tempGame->isLocked()){

                                    if($tempGame->getWinner() == $tempUser->picks[$team->id]->team_id){
                                        $total += $tempUser->picks[$team->id]->value;
                                        $percentage["correct"] .= "{$tempUser->picks[$team->id]->value}, ";

                                        echo "<td class='aligncenter'><b>+{$tempUser->picks[$team->id]->value}</b></td>";

                                    }else{

                                        echo "<td class='aligncenter'>{$tempUser->picks[$team->id]->value}</td>";

                                    }

                                    $percentage["total"]++;

                                }
                            }else{

                                echo "<td></td>";

                            }

                        }

                        $alt = ($alt == "a") ? "" : "a";

                        $cor = $percentage["correct"];
                        $cor = trim($cor, ', ');

                        echo "<td data-sort-value='{$total}'>{$total} <small title='Picks Left'>({$cor})</small></td>";

                    }

                    ?>


                    </tbody>
                </table>

            </div>
        </div>

        <div class="clear-fix"></div>

    </div>


<?php


endif;


?>

<script src="./js/combiner.php?ver=<?php echo VERSION ?>"></script>
<script src="./js/stupidtable.min.js"></script>
<script>
    $("#stupid").stupidtable();
</script>

<?php

    include "./_footer.php";

?>
