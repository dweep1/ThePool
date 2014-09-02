<?php

global $ROOT_DB_PATH;
$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    week::selected($_POST['selected_week']);
    season::selected($_POST['selected_season']);

    $_SESSION['result'] = "Week and Season Set";

    header("Location: ./index.php");


}
