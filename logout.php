<?php

    include "./_header.php";

    users::deAuth();

    header("Location: ./index.php");

?>
