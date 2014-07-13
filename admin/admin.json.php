<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);

global $ROOT_DB_PATH;

$ROOT_DB_PATH = "../_db/";

include_once "{$ROOT_DB_PATH}header.php";
include_once "{$ROOT_DB_PATH}security.php";
include_once "{$ROOT_DB_PATH}objects.php";
include_once "./admin.functions.php";

$objectType = $objData->data;

if(!FormValidation::validate())
    exit;

$users = new $objectType();

echo json_encode($users->getList());

?>