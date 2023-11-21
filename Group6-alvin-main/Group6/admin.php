<?php
session_start();
include('includes/db.php');
include('includes/auth.php');

function formatDateTime($dateTimeString)
{
    $dateTime = new DateTime($dateTimeString);
    return $dateTime->format('Y-m-d H:i:s');
}

class AdminDashboard
{
    private $conn;
    private $authenticator;

    public function __construct($db_connection, $userAuthenticator)
    {
        $this->conn = $db_connection;
        $this->authenticator = $userAuthenticator;
    }

    public function adminDashboard()
    {
        if (!$this->authenticator->isAdmin()) {
            header("Location: index.php?error=Unauthorized");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->authenticator->logout();
            header("Location: index.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_reservation'])) {
            $reservationId = $_GET['delete_reservation'];
            $this->deleteReservation($reservationId);
        }

        $reservations = $this->getReservations();

        ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-100 font-sans p-4 sm:p-6 md:p-8 lg:p-12 overflow-x-auto">
    <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl 2xl:text-8xl font-bold text-center mb-8 sm:mb-16">Admin Dashboard</h1>
    <form method="post" action="" class="flex justify-center">
        <button type="submit" name="logout"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 sm:py-4 sm:px-6 lg:px-8 rounded">Logout</button>
    </form>

    <h2 class="text-2xl sm:text-4xl md:text-5xl font-semibold mt-8 sm:mt-16">All Reservations</h2>
    <div class="mt-4 sm:mt-8 overflow-x-auto">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="sm:hidden"> 
                <div class="max-w-screen-lg mx-auto p-4 sm:p-6 md:p-8 lg:p-12 overflow-x-auto">
                    <table class="w-full table-auto sm:table">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Date and Time</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Room Count</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Purpose</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Groupmates</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Courses</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Status</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars(formatDateTime($reservation['time'])) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['room_count']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['purpose']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['groupmates']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['courses']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3 <?= $reservation['status'] === 'pending' ? 'bg-blue-600' : ($reservation['status'] === 'approved' ? 'bg-green-500' : 'bg-red-500') ?>">
                                        <?= htmlspecialchars($reservation['status']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <a href="?delete_reservation=<?= $reservation['id'] ?>"
                                            class="text-red-500 hover:text-red-700">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="hidden sm:block">  
                    <table class="w-full table-auto sm:table">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Date and Time</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Room Count</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Purpose</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Groupmates</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Courses</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Status</th>
                                <th class="px-2 py-2 sm:py-3 sm:px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars(formatDateTime($reservation['time'])) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['room_count']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['purpose']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['groupmates']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <?= htmlspecialchars($reservation['courses']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3 <?= $reservation['status'] === 'pending' ? 'bg-blue-600' : ($reservation['status'] === 'approved' ? 'bg-green-500' : 'bg-red-500') ?>">
                                        <?= htmlspecialchars($reservation['status']) ?>
                                    </td>
                                    <td class="px-2 py-2 sm:py-3 sm:px-3">
                                        <a href="?delete_reservation=<?= $reservation['id'] ?>"
                                            class="text-red-500 hover:text-red-700">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            
            
        </div>
    </div>
</body>

</html>



        <?php
    }

    private function getReservations()
    {
        $query = "SELECT reservations.id, reservations.time, reservations.room_count, reservations.purpose, reservations.status, GROUP_CONCAT(users.username) as groupmates, GROUP_CONCAT(users.course) as courses
                  FROM reservations
                  LEFT JOIN reservation_groupmates ON reservations.id = reservation_groupmates.reservation_id
                  LEFT JOIN users ON reservation_groupmates.user_id = users.id
                  GROUP BY reservations.id";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteReservation($reservationId)
    {
        try {
            $query = "DELETE FROM reservations WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: admin.php");
                exit();
            } else {
                echo "Failed to delete the reservation.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$userAuthenticator = new UserAuthenticator($conn);
$adminDashboard = new AdminDashboard($conn, $userAuthenticator);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $userAuthenticator->logout();
}

$adminDashboard->adminDashboard();
?>