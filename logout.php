<?php

    include_once "./header.php";

    users::deAuth();

    header("Location: ./index.php");

?>
