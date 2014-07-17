<?php

    include_once "./header.php";

    FormValidation::generate();

    global $currentUser;

    if($currentUser === false || !$currentUser->verifyAuth())
        header("Location: ./logout.php");

    var_dump($currentUser);

?>
