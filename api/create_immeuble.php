 
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/immeubles.php';

$database = new Database();
$db = $database->getConnection();

$immeubles = new immeubles($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->address)) {
    $immeubles->name = $data->name;
    $immeubles->address = $data->address;
    $immeubles->city = $data->city;
    $immeubles->postal_code = $data->postal_code;
    $immeubles->country = $data->country;

    if($immeubles->create()) {
        echo json_encode(["message" => "immeubles created successfully."]);
    } else {
        echo json_encode(["message" => "Failed to create immeubles."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
?>
