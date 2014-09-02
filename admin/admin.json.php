<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include "./admin.header.php";

$objectType = $objData->data;

$item = new $objectType();

echo json_encode($item->getList());

?>