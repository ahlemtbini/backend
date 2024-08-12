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

$remise->id = isset($_GET['id']) ? $_GET['id'] : die();

if($remise->readOne()) {
    $remise_arr = array(
        "id" => $remise->id,
        "lot_id" => $remise->lot_id,
        "donneur" => $remise->donneur,
        "receveur" => $remise->receveur,
        "photo_video" => $remise->photo_video,
        "date_remise" => $remise->date_remise,
        "commentaire" => $remise->commentaire,
        "signature" => $remise->signature
    );

    echo json_encode($remise_arr);
} else {
    echo json_encode(array("message" => "Remise de clé not found."));
}
?>
