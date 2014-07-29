<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $weekNumber = intval($_POST['week_selection']);

    header("Location: ../results.php?week=$weekNumber");

}

?>