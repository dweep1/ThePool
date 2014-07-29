<?php

    include_once "./_header.php";

    users::deAuth();

    header("Location: ./index.php");

?>
