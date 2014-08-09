<?php

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include_once "./admin.header.php";

$id = (int)$_GET['id'];
$objectType = $_GET['className'];

$item = new $objectType($id);

?>

<ul class="data-row">
    <?php
    foreach($item->toArray() as $key => $value){

        echo "<li><h6>$key</h6> - $value</li>";

    }
    ?>
</ul>