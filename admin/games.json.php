<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);

include "./admin.header.php";

$objectType = $objData->data;

$item = new $objectType();

$objectType::query(["orderBy" => "date ASC"]);

$items = $item->getList(["week_id" => week::selected()->id, "season_id" => season::selected()->id]);

foreach($items as $key => $value){
    $items[$key]->date = date("D, m/d", strtotime($value->date));
}

echo json_encode($items);

?>