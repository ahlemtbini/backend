<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");


include_once '../config/database.php';
include_once '../models/immeubles.php';

$database = new Database();
$db = $database->getConnection();

$immeubles = new immeubles($db);

$immeubles->id = isset($_GET['id']) ? $_GET['id'] : die();

if($immeubles->read_one()) {
    $immeubles_arr = array(
        "id" => $immeubles->id,
        "name" => $immeubles->name,
        "address" => $immeubles->address,
        "city" => $immeubles->city,
        "postal_code" => $immeubles->postal_code,
        "country" => $immeubles->country
    );

    echo json_encode($immeubles_arr);
} else {
    echo json_encode(array("message" => "immeubles not found."));
}
?>
