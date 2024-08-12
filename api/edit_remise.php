<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once './remise_de_cle.php';

$database = new Database();
$db = $database->getConnection();

$remise = new RemiseDeCle($db);

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->lot_id) && isset($data->donneur) && isset($data->receveur) && isset($data->photo_video) && isset($data->date_remise) && isset($data->commentaire) && isset($data->signature)) {
    $remise->id = $data->id;
    $remise->lot_id = $data->lot_id;
    $remise->donneur = $data->donneur;
    $remise->receveur = $data->receveur;
    $remise->photo_video = $data->photo_video;
    $remise->date_remise = $data->date_remise;
    $remise->commentaire = $data->commentaire;
    $remise->signature = $data->signature;

    if ($remise->update()) {
        echo json_encode(array("message" => "Remise de clé updated successfully."));
    } else {
        echo json_encode(array("message" => "Unable to update remise de clé."));
    }
} else {
    echo json_encode(array("message" => "Incomplete data."));
}
?>
