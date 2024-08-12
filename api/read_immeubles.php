<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


include_once '../config/database.php';
include_once '../models/immeubles.php';

$database = new Database();
$db = $database->getConnection();

$immeubles = new immeubles($db);

$stmt = $immeubles->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $immeubless_arr = array();
    $immeubless_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $immeubles_item = array(
            "id" => $id,
            "name" => $name,
            "address" => $address,
            "city" => $city,
            "postal_code" => $postal_code,
            "country" => $country
        );

        array_push($immeubless_arr["records"], $immeubles_item);
    }

    echo json_encode($immeubless_arr);
} else {
    echo json_encode(array("message" => "No immeubless found."));
}
?>
