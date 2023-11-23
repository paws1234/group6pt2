<?php
session_start();
include('includes/db.php');
require 'includes/auth.php';

function formatDateTime($dateTimeString)
{
    $dateTime = new DateTime($dateTimeString);
    return $dateTime->format('Y-m-d H:i:s');
}
class UserDashboard
{
    private $conn;
    private $authenticator;

    public function __construct($db_connection, $userAuthenticator)
    {
        $this->conn = $db_connection;
        $this->authenticator = $userAuthenticator;
    }

    public function userDashboard()
    {
       

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->authenticator->logout();
            header("Location: index.php");
            exit();
        }

        $userID = $this->authenticator->getUserId();
        $query = "SELECT id,time, room_count, purpose, status FROM reservations WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
        $stmt->execute();
        $userReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="icon" href="images/ctu.png" type="image/x-icon">
</head>

<body class="bg-gray-100 font-sans p-4 sm:p-6 md:p-8 lg:p-12 xl:p-16">
    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold text-center mb-6 sm:mb-12">User Dashboard</h1>
    
    <form method="post" action="" class="flex justify-center space-x-2 sm:space-x-4">
        <button type="submit" name="logout"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 sm:py-3 sm:px-6 lg:px-8 rounded">Logout</button>
        <a href="profile.php"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 sm:py-3 sm:px-6 lg:px-8 rounded">Profile</a>
    </form>

    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-semibold mt-6 sm:mt-12">Your Reservations</h2>
    
    <div class="overflow-x-auto mt-4 sm:mt-6 md:mt-8 lg:mt-12 xl:mt-16">
        <div class="bg-white shadow overflow-x-auto sm:rounded-lg">
            <table class="w-full table-auto border border-collapse">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Date and Time</th>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Room Count</th>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-2/4 lg:w-2/4 xl:w-1/5 border">Purpose</th>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Groupmates</th>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Course</th>
                        <th class="px-2 py-2 sm:py-3 sm:px-3 md:w-2/4 lg:w-2/4 xl:w-3/5 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userReservations as $reservation): ?>
                        <tr>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border">
                                <?= htmlspecialchars(formatDateTime($reservation['time'])) ?>
                            </td>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border">
                                <?= htmlspecialchars($reservation['room_count']) ?>
                            </td>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border">
                                <?= htmlspecialchars($reservation['purpose']) ?>
                            </td>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border">
                                <?php
                                $groupmates = $this->getGroupmates($reservation['id']);
                                foreach ($groupmates as $groupmate) {
                                    echo htmlspecialchars($groupmate['username']) . '<br>';
                                }
                                ?>
                            </td>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border">
                                <?php
                                foreach ($groupmates as $groupmate) {
                                    echo htmlspecialchars($groupmate['course']) . '<br>';
                                }
                                ?>
                            </td>
                            <td class="px-2 py-2 sm:py-3 sm:px-3 border <?= $reservation['status'] === 'pending' ? 'bg-blue-600' : ($reservation['status'] === 'approved' ? 'bg-green-500' : 'bg-red-500') ?>">
                                <?= htmlspecialchars($reservation['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form action="reserve.php" class="text-center mt-4 sm:mt-6 md:mt-8 lg:mt-12 xl:mt-16">
        <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 sm:py-3 sm:px-6 lg:px-8 rounded">Create Reservation</button>
    </form>
</body>

</html>


        <?php
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
}

$userAuthenticator = new UserAuthenticator($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $userAuthenticator->logout();
}

$userDashboard = new UserDashboard($conn, $userAuthenticator);
$userDashboard->userDashboard();
?>
