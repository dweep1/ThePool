<?php

    include "./_header.php";

    users::deAuth();

    //remove all the variables in the session
    session_unset();

    // destroy the session
    session_destroy();

    header("Location: ./index.php");

?>
