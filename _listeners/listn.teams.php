<?php

    include "./listn.header.php";

    echo json_encode(teams::getTeamsList());

?>