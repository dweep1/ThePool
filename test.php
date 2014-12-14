<?php

    include "bootstrap.php";

    $credits = credit::loadMultiple(["user_id" => users::returnCurrentUser()]);

    var_dump($credits);