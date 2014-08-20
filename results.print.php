<?php

    include_once "./_header.php";

    FormValidation::generate();

    $user = users::returnCurrentUser();

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

    foreach($usersList as $key => $tempUser){

        if((int) $tempUser->access_level === -1 && (int) $tempUser->id !== (int) $user->id){
            unset($usersList[$key]);
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
    <link href="./css/index.css?ver=0.3" rel="stylesheet" type="text/css" />

</head>
<body class="height-100">

    <div id="content-area" style="padding:0; margin:0">
        <div class="fluid-row full width-90 slim alignleft">
            <h5 style="display:inline-block">Week #<?php echo $thisWeek->week_number; ?>'s Results</h5>
        </div>

        <div class="fluid-row full width-90 slim aligncenter">

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
</body>
</html>