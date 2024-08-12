<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include_once '../config/database.php';
include_once './remise_de_cle.php';

$database = new Database();
$db = $database->getConnection();

$remise = new RemiseDeCle($db);

// Get the data from the request
$data = json_decode(file_get_contents("php://input"));

// Check if ID is set
if (isset($_GET['id'])) {
    $remise->id = $_GET['id'];
    error_log("ID received: " . $remise->id); // Log the received ID
    if ($remise->delete()) {
        echo json_encode(array("message" => "Remise de clé deleted successfully."));
    } else {
        echo json_encode(array("message" => "Unable to delete remise de clé."));
    }
} else {
    error_log("ID not provided"); // Log error when ID is not provided
    echo json_encode(array("message" => "ID not provided."));
}

?>
