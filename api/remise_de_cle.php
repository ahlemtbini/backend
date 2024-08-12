<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

class RemiseDeCle {
    private $conn;
    private $table_name = "RemiseDeCle";

    public $id;
    public $lot_id;
    public $donneur;
    public $receveur;
    public $photo_video;
    public $date_remise;
    public $commentaire;
    public $signature;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->lot_id = $row['lot_id'];
            $this->donneur = $row['donneur'];
            $this->receveur = $row['receveur'];
            $this->photo_video = $row['photo_video'];
            $this->date_remise = $row['date_remise'];
            $this->commentaire = $row['commentaire'];
            $this->signature = $row['signature'];

            return true;
        }

        return false;
    }

    function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET lot_id = :lot_id, donneur = :donneur, receveur = :receveur, photo_video = :photo_video, date_remise = :date_remise, commentaire = :commentaire, signature = :signature WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':lot_id', $this->lot_id);
        $stmt->bindParam(':donneur', $this->donneur);
        $stmt->bindParam(':receveur', $this->receveur);
        $stmt->bindParam(':photo_video', $this->photo_video);
        $stmt->bindParam(':date_remise', $this->date_remise);
        $stmt->bindParam(':commentaire', $this->commentaire);
        $stmt->bindParam(':signature', $this->signature);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET lot_id=:lot_id, donneur=:donneur, receveur=:receveur, 
                      photo_video=:photo_video, date_remise=:date_remise, 
                      commentaire=:commentaire, signature=:signature";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->lot_id = htmlspecialchars(strip_tags($this->lot_id));
        $this->donneur = htmlspecialchars(strip_tags($this->donneur));
        $this->receveur = htmlspecialchars(strip_tags($this->receveur));
        $this->date_remise = htmlspecialchars(strip_tags($this->date_remise));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        $this->signature = htmlspecialchars(strip_tags($this->signature));

        // Handle file uploads
        if (isset($_FILES['photo_video']) && $_FILES['photo_video']['error'] == 0) {
            $upload_dir = '../uploads/';
            $photo_video_name = basename($_FILES['photo_video']['name']);
            $photo_video_path = $upload_dir . $photo_video_name;
            
            // Debugging output
            error_log("Attempting to upload file to: " . $photo_video_path);
            
            if (move_uploaded_file($_FILES['photo_video']['tmp_name'], $photo_video_path)) {
                $this->photo_video = $photo_video_name; // Save only the file name to the database
            } else{
                $this->photo_video = $photo_video_name; // Save only the file name to the database
            }
        } else {
            // Debugging output for file upload errors
            if (isset($_FILES['photo_video'])) {
                error_log("File upload error: " . $_FILES['photo_video']['error']);
            }
            $this->photo_video = ''; // Ensure this is set even if no file is uploaded
        }

        // Bind parameters
        $stmt->bindParam(":lot_id", $this->lot_id);
        $stmt->bindParam(":donneur", $this->donneur);
        $stmt->bindParam(":receveur", $this->receveur);
        $stmt->bindParam(":photo_video", $this->photo_video);
        $stmt->bindParam(":date_remise", $this->date_remise);
        $stmt->bindParam(":commentaire", $this->commentaire);
        $stmt->bindParam(":signature", $this->signature);

        return $stmt->execute();
    }
}

// Instantiate the database and the "RemiseDeCle" object
$database = new Database();
$db = $database->getConnection();

$remise = new RemiseDeCle($db);

// Process POST request for file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form data is being sent
    if (isset($_POST['lot_id']) && isset($_POST['donneur']) && isset($_POST['receveur']) && isset($_POST['date_remise']) && isset($_POST['commentaire']) && isset($_POST['signature'])) {
        $remise->lot_id = $_POST['lot_id'];
        $remise->donneur = $_POST['donneur'];
        $remise->receveur = $_POST['receveur'];
        $remise->date_remise = $_POST['date_remise'];
        $remise->commentaire = $_POST['commentaire'];
        $remise->signature = $_POST['signature'];

        // Handle file uploads
        if (isset($_FILES['photo_video']) && $_FILES['photo_video']['error'] == 0) {
            $upload_dir = '../uploads/';
            $photo_video_name = basename($_FILES['photo_video']['name']);
            $photo_video_path = $upload_dir . $photo_video_name;
            
            // Debugging output
            error_log("Attempting to upload file to: " . $photo_video_path);
            
            if (move_uploaded_file($_FILES['photo_video']['tmp_name'], $photo_video_path)) {
                $remise->photo_video = $photo_video_name; // Save only the file name to the database
            } else {
                error_log("Failed to move uploaded file: " . $_FILES['photo_video']['error']);
                echo json_encode(array("message" => "Failed to upload the file."));
                exit;
            }
        } else {
            // Debugging output for file upload errors
            if (isset($_FILES['photo_video'])) {
                error_log("File upload error: " . $_FILES['photo_video']['error']);
            }
            $remise->photo_video = ''; // Ensure this is set even if no file is uploaded
        }

        // Create the new "Remise de Cle"
        if ($remise->create()) {
            echo json_encode(array("message" => "Remise de clé was created."));
        } else {
            echo json_encode(array("message" => "Unable to create remise de clé."));
        }
    } else {
        echo json_encode(array("message" => "Incomplete data provided."));
    }
}
?>
