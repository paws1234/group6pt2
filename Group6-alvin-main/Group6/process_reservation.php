<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

include('includes/db.php');
include('includes/auth.php');

class ReservationHandler {
    private $conn;
    private $userAuthenticator;

    public function __construct($db_connection, $userAuthenticator) {
        $this->conn = $db_connection;
        $this->userAuthenticator = $userAuthenticator;
    }

    public function handleReservation() {
        if (!$this->userAuthenticator->isLoggedIn()) {
            $_SESSION['reservation_errors'] = array("Error: You are not logged in. Please log in and try again.");
            header("Location: reserve.php");
            exit();
        }

        $userID = $this->userAuthenticator->getUserId();

       
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $time = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');

            if (!$this->userAuthenticator->isUserAuthorized($userID, 'user')) {
                $_SESSION['reservation_errors'] = array("Error: You are not authorized to make reservations.");
                header("Location: reserve.php");
                exit();
            }

            $room_count = htmlspecialchars($_POST['room_count'], ENT_QUOTES, 'UTF-8');
            $purpose = htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8');

            $groupmates = $this->prepareGroupmatesArray($userID);

            $groupUsersCount = $this->getGroupUsersCount($time);

            $this->validateGroupmates($groupmates, $groupUsersCount, $time);

            try {
                $this->conn->beginTransaction();

                $this->insertReservation($userID, $time, $room_count, $purpose);

                $reservationId = $this->conn->lastInsertId();

                $this->storeGroupmates($reservationId, $groupmates);

                $this->conn->commit();

                header("Location: confirmation.php");
                exit();
            } catch (PDOException $e) {
                $this->conn->rollBack();
                $_SESSION['reservation_errors'] = array("Error: Reservation failed. Please check your inputs and try again. Error: " . $e->getMessage());
                header("Location: reserve.php");
                exit();
            }
        }
    }

    private function prepareGroupmatesArray($userID) {
        $groupmates = array();

        if (!$this->isUserPartOfGroup($userID)) {
            echo "Debug: User is not part of a group. Adding the user to the group...<br>";
            $groupmates[] = $userID;
        } else {
            echo "Debug: User is already part of a group. Checking previous reservation...<br>";
            echo "Debug: Previous reservation details - date, room count, purpose, etc.<br>";
        }

        for ($i = 1; $i <= 5; $i++) {
            $groupmateUsername = htmlspecialchars($_POST['groupmate' . $i], ENT_QUOTES, 'UTF-8');

            if (!empty($groupmateUsername)) {
                $groupmateID = $this->getUserIdByUsername($groupmateUsername);

                if ($groupmateID !== false && !$this->isUserPartOfGroup($groupmateID)) {
                    $groupmates[] = $groupmateID;
                } else {
                    $_SESSION['reservation_errors'] = array("Error: Invalid input for groupmate $i. Make sure each groupmate is registered and not part of another group.");
                    header("Location: reserve.php");
                    exit();
                }
            }
        }

        return $groupmates;
    }

    private function validateGroupmates($groupmates, $groupUsersCount, $time) {
        $errors = array();

        if (count($groupmates) < 5) {
            $errors[] = "Error: You need a minimum of 6 valid groupmates for the reservation.";
        }

        if ($groupUsersCount >= 10) {
            $errors[] = "Error: The group has reached the maximum number of users (10).";
        }

        foreach ($groupmates as $groupmate) {
            if ($this->isUserInAnotherGroup($groupmate, $time) || $this->isUserPartOfGroupWithReservation($groupmate, $time)) {
                $errors[] = "Error: User $groupmate is already part of another group with a reservation on the same date and time.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['reservation_errors'] = $errors;
            header("Location: reserve.php");
            exit();
        }
    }

    private function isUserPartOfGroupWithReservation($userID, $time) {
        $query = "SELECT COUNT(*) AS group_count FROM reservation_groupmates rg
                  JOIN reservations r ON rg.reservation_id = r.id
                  WHERE rg.user_id = :userID AND r.time = :time AND r.status = 'pending' AND rg.user_id <> r.user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $groupCount = $result['group_count'];

        return $groupCount > 0;
    }

    private function isUserInAnotherGroup($userID, $time) {
        $query = "SELECT COUNT(*) AS group_count FROM reservation_groupmates rg
                  JOIN reservations r ON rg.reservation_id = r.id
                  WHERE rg.user_id = :userID AND r.time = :time AND r.status = 'pending' AND rg.user_id <> r.user_id AND rg.user_id <> :userID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $groupCount = $result['group_count'];

        return $groupCount > 0;
    }

    private function insertReservation($userID, $time, $room_count, $purpose) {
        $query = "INSERT INTO `reservations` (`user_id`, `time`, `room_count`, `purpose`, `status`) VALUES (:userID, :time, :room_count, :purpose, 'pending')";

        echo "Debug: SQL Query - " . $query . "<br>";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':room_count', $room_count, PDO::PARAM_INT);
        $stmt->bindParam(':purpose', $purpose, PDO::PARAM_STR);

        echo "Debug: Time - " . $time . "<br>";
        echo "Debug: Room Count - " . $room_count . "<br>";
        echo "Debug: Purpose - " . $purpose . "<br>";

        if (!$stmt->execute()) {
            $this->conn->rollBack();
            $_SESSION['reservation_errors'] = array("Error: Reservation failed. Please check your inputs and try again. Error: " . implode(" ", $stmt->errorInfo()));
            header("Location: reserve.php");
            exit();
        }
    }

    private function isUserRegistered($userID) {
        if (!is_numeric($userID)) {
            $query = "SELECT COUNT(*) AS user_count, id FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $userID, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $userCount = $result['user_count'];

            if ($userCount > 0) {
                return $result['id'];
            } else {
                return false;
            }
        } else {
            $query = "SELECT COUNT(*) AS user_count FROM users WHERE id = :userID";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $userCount = $result['user_count'];

            if ($userCount > 0) {
                return $userID;
            } else {
                return false;
            }
        }
    }

    private function isUserPartOfGroup($userID) {
        $query = "SELECT COUNT(*) AS group_count FROM reservations WHERE user_id = :userID AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $groupCount = $result['group_count'];

        return $groupCount > 0;
    }

    private function storeGroupmates($reservationId, $groupmates) {
        try {
            $groupmateQuery = "INSERT INTO reservation_groupmates (reservation_id, user_id) VALUES (:reservationId, :userId)";
            $groupmateStmt = $this->conn->prepare($groupmateQuery);

            foreach ($groupmates as $groupmate) {
                if ($this->isUserRegistered($groupmate)) {
                    if ($this->isUserId($groupmate)) {
                        $userId = $groupmate;
                    } elseif ($this->isUsername($groupmate)) {
                        $userId = $this->getUserIdByUsername($groupmate);
                    } else {
                        $_SESSION['reservation_errors'] = array("Error: User with ID or username $groupmate not found in the users table.");
                        header("Location: reserve.php");
                        exit();
                    }

                    $groupmateStmt->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
                    $groupmateStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $groupmateStmt->execute();
                } else {
                    $_SESSION['reservation_errors'] = array("Error: User with ID or username $groupmate is not registered.");
                    header("Location: reserve.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            echo "Error: Failed to execute groupmate query. " . $e->getMessage() . "<br>";
            exit();
        }
    }

    private function isUserId($userID) {
        return is_numeric($userID);
    }

    private function isUsername($userID) {
        $query = "SELECT COUNT(*) AS user_count FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $userID, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $userCount = $result['user_count'];

        return $userCount > 0;
    }

    private function getUserIdByUsername($username) {
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result !== false) ? $result['id'] : false;
    }

    private function isLabAvailable($time, $roomCount) {
        $query = "SELECT COUNT(*) AS total_reserved FROM reservations WHERE time = :time AND HOUR(time) = HOUR(:time)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalReserved = $result['total_reserved'];

        $maxRoomsPerHour = 2;

        return ($totalReserved + $roomCount) <= $maxRoomsPerHour;
    }

    private function validateReservationDate($time) {
        $minTime = strtotime('08:00:00');
        $maxTime = strtotime('17:00:00');
        $reservationTime = strtotime($time);

        return $reservationTime >= $minTime && $reservationTime <= $maxTime;
    }

    private function getGroupUsersCount($time, $depth = 0) {
        $maxDepth = 10;

        if ($depth > $maxDepth) {
            $_SESSION['reservation_errors'] = array("Error: Maximum recursion depth exceeded.");
            header("Location: reserve.php");
            exit();
        }

        $query = "SELECT COUNT(DISTINCT user_id) AS user_count FROM reservations WHERE time = :time";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $userCount = $result['user_count'];

        echo "Debug: Time for getGroupUsersCount - " . $time . "<br>";
        echo "Debug: Group Users Count - " . $userCount . "<br>";

        return $userCount;
    }
}

$userAuthenticator = new UserAuthenticator($conn);
$reservationHandler = new ReservationHandler($conn, $userAuthenticator);
$reservationHandler->handleReservation();
?>