<?php
session_start();
require('includes/db.php');
include('includes/auth.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {

    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $reservation_id = $_GET['id'];

    try {
        $query = "DELETE FROM reservations WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $reservation_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
       
            header("Location: admin.php");
            exit();
        } else {
            echo "Failed to delete the reservation.";
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
  
    echo "Invalid request.";
}
?>
