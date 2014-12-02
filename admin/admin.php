<?php

include "./admin.header.php";

global $si;

if(isset($_GET['si']))
    $si = $_GET['si'];
else
    $si = 1;

header("Location: index.php");

?>