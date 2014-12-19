<?php
    include "./listn.header.php";

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $submitType = intval($_POST['submitType']);

        if($submitType == 0){ // display the pick count;

            $current_week = week::getCurrent();
            $pickCount = pick::getPickCount();
            $season = season::getCurrent();

            $gameCount = $current_week->getGameCount();

            if($season->type == "playoff")
                $gameCount = $gameCount*2;

            echo "{$pickCount}/{$gameCount}";

        }else if($submitType == 1){//timezone offset

            echo week::getNextLock($_POST['offset']) ?: "false";

        }

        exit;

}

?>