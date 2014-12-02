<?php

include "./admin.header.php";

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