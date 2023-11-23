<?php
ob_start();

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('includes/db.php');
include('includes/auth.php');

class StaffDashboard
{
    private $conn;
    private $userAuthenticator;

    public function __construct($db_connection, $userAuthenticator)
    {
        $this->conn = $db_connection;
        $this->userAuthenticator = $userAuthenticator;
    }

    public function displayDashboard()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->userAuthenticator->logout();
            header("Location:  staff.php");
            exit();
        }

        if ($this->userAuthenticator->getUserRole() !== 'staff') {
            $_SESSION['login_error'] = "Access denied for staff members";
            header("Location:  staff.php");
            exit();
        }

        $userID = $this->userAuthenticator->getUserId();
        $query = "SELECT r.id, r.time, u.username as user, r.room_count, r.purpose, r.status
        FROM reservations r
        INNER JOIN users u ON r.user_id = u.id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($reservations) === 0) {
                echo "<p class='text-center text-2xl md:text-3xl text-gray-500 my-8'>No reservations found.</p>";
            } else {
                echo "<table class='w-full md:w-full table-auto border border-gray-300'>";
                echo "<thead class='bg-blue-500 text-white'>";
                echo "<tr>
                        <th class='px-6 py-9 md:w-1/5'>Date and Time</th>
                        <th class='px-6 py-9 md:w-1/5'>Room Count</th>
                        <th class='px-6 py-9 md:w-2/5'>Purpose</th>
                        <th class='px-6 py-9 md:w-1/5'>Groupmates</th>
                        <th class='px-6 py-9 md:w-1/5'>Course</th>
                        <th class='px-6 py-9 md:w-2/5'>Status</th>
                        <th class='px-6 py-9 md:w-1/5'>Actions</th>
                    </tr>";
                echo "</thead>";
                echo "<tbody>";
                foreach ($reservations as $reservation) {
                    echo "<tr>";
                    echo "<td class='px-4 py-2 border'>" . $reservation['time'] . "</td>";
                    echo "<td class='px-4 py-2 border'>" . $reservation['room_count'] . "</td>";
                    echo "<td class='px-4 py-2 border'>" . $reservation['purpose'] . "</td>";

                    $groupmates = $this->getGroupmates($reservation['id']);
                    echo "<td class='px-4 py-2 border'>";
                    foreach ($groupmates as $groupmate) {
                        echo htmlspecialchars($groupmate['username']) . '<br>';
                    }
                    echo "</td>";
                    echo "<td class='px-4 py-2 border'>";
                    foreach ($groupmates as $groupmate) {
                        echo htmlspecialchars($groupmate['course']) . '<br>';
                    }
                    echo "</td>";
                    echo "<td class='px-4 py-2 border " . ($reservation['status'] === 'approved' ? 'text-green-500' : ($reservation['status'] === 'pending' ? 'text-blue-500' : 'text-red-500')) . "'>" . $reservation['status'] . "</td>";

                    echo "<td class='px-4 py-2 border'>
                        <form method='post' action='update_status.php' class='flex items-center space-x-2'>
                            <input type='hidden' name='reservation_id' value='" . $reservation['id'] . "'>
                            <select name='new_status' class='border px-2 py-1 rounded'>
                                <option value='pending'>Pending</option>
                                <option value='approved'>Approved</option>
                                <option value='rejected'>Denied</option>
                            </select>
                            <button type='submit' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'>Change Status</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "<form method='post' action=''>
                        <button type='submit' name='logout' class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4'>Logout</button>
                      </form>";
                echo "</div>";
            }
        } catch (PDOException $e) {
            //echo "Database Error: " . $e->getMessage();
        }
    }

    private function getGroupmates($reservationId)
    {
        $query = "SELECT users.username, users.course FROM reservation_groupmates 
                  JOIN users ON reservation_groupmates.user_id = users.id
                  WHERE reservation_groupmates.reservation_id = :reservation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateReservationStatus($reservationId, $newStatus)
    {
        $query = "UPDATE reservations SET status = :new_status WHERE id = :reservation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':new_status', $newStatus, PDO::PARAM_STR);
        $stmt->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);

        try {
            $stmt->execute();
            header("Location: staff.php?success=true");
            exit();
        } catch (PDOException $e) {
            //echo "Error updating reservation status: " . $e->getMessage();
        }
    }
}

$userAuthenticator = new UserAuthenticator($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $userAuthenticator->logout();
}

$staffDashboard = new StaffDashboard($conn, $userAuthenticator);


if (isset($_GET['success']) && $_GET['success'] === 'true' && !isset($_SESSION['refreshed'])) {
    echo '<script>window.onload = function() { window.location.reload(); }</script>';
    $_SESSION['refreshed'] = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="icon" href="images/ctu.png" type="image/x-icon">
</head>

<body>
    <div class="bg-blue-600 p-4 text-white text-center">
        <h1 class="text-4xl md:text-6xl font-bold">Staff Dashboard</h1>
    </div>

    <div class="container mx-auto mt-8 p-4">
        <h2 class="text-2xl md:text-4xl mb-4 font-semibold">Reservations</h2>

        <div class="bg-white rounded shadow-md p-4 overflow-x-auto">
            <?php
            $staffDashboard->displayDashboard();
            ?>
        </div>
    </div>

<script>
    <?php
    if (isset($_GET['success']) && $_GET['success'] === 'true' && !isset($_SESSION['refreshed'])) {
        echo 'window.history.replaceState({}, document.title, "' . $_SERVER['PHP_SELF'] . '");';
        $_SESSION['refreshed'] = true;
    }
    ?>
</script>
</body>
</html>
