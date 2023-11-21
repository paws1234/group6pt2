<?php
include('includes/db.php');

class ReservationValidator {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function validateReservationDate($date) {
        $currentTimestamp = time();
        $reservationTimestamp = strtotime($date);
        return $reservationTimestamp > $currentTimestamp;
    }

    public function isLabAvailable($date, $computerCount) {
        $query = "SELECT SUM(computer_count) AS total_reserved FROM reservations WHERE date = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalReserved = $result['total_reserved'];
        $maxLabCapacity = 40;

        if ($totalReserved === null) {
            $totalReserved = 0;
        }

        return ($totalReserved + $computerCount) <= $maxLabCapacity;
    }
}

$reservationValidator = new ReservationValidator($conn);
$dateToCheck = '2023-10-31';
$computerCount = 1;

if ($reservationValidator->validateReservationDate($dateToCheck)) {
    if ($reservationValidator->isLabAvailable($dateToCheck, $computerCount)) {
        echo "Reservation is valid and the lab is available.";
    } else {
        echo "The lab is not available on this date.";
    }
} else {
    echo "Invalid reservation date.";
}
?>