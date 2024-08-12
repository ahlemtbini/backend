<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once './remise_de_cle.php';

$database = new Database();
$db = $database->getConnection();

$remise = new RemiseDeCle($db);

$stmt = $remise->readAll();
$num = $stmt->rowCount();

if ($num > 0) {
    $remises_arr = array();
    $remises_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $remise_item = array(
            "id" => $id,
            "lot_id" => $lot_id,
            "donneur" => $donneur,
            "receveur" => $receveur,
            "photo_video" => $photo_video,
            "date_remise" => $date_remise,
            "commentaire" => $commentaire,
            "signature" => $signature
        );

        array_push($remises_arr["records"], $remise_item);
    }

    echo json_encode($remises_arr);
} else {
    echo json_encode(array("message" => "No remises de clé found."));
}
?>
