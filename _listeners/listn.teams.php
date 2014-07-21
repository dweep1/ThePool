<?php

    include_once "./listn.header.php";

    echo json_encode(teams::getTeamsList());

?>