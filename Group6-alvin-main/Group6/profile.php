<?php
session_start();
include('includes/db.php');
include('includes/auth.php');

class UserProfile {
    private $conn;
    private $userAuthenticator;

    public function __construct($db_connection, $authenticator) {
        $this->conn = $db_connection;
        $this->userAuthenticator = $authenticator;
    }

    public function getUserInfo() {
        $userID = $this->userAuthenticator->getUserId();
        $query = "SELECT username, email, age, mobile, gender, course FROM users WHERE id = :userID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$userAuthenticator = new UserAuthenticator($conn);
$userProfile = new UserProfile($conn, $userAuthenticator);
$userInfo = $userProfile->getUserInfo();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css">
    <link rel="icon" href="images/ctu.png" type="image/x-icon">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="container mx-auto p-4 sm:p-12 md:p-24 lg:p-32 w-full max-w-screen-lg">
            <h1 class="text-3xl sm:text-6xl md:text-4xl lg:text-5xl font-semibold text-center mb-4">User Profile</h1>
            <div class="bg-white p-4 sm:p-6 md:p-8 lg:p-10 rounded-md shadow-md">
                <div class="text-lg sm:text-3xl font-semibold text-center mb-4">
                    <p>Welcome, <?php echo htmlspecialchars($userInfo['username']); ?>!</p>
                </div>
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-4xl font-semibold mb-2">Email:</h2>
                    <p class="text-gray-700 text-xl sm:text-2xl"><?php echo htmlspecialchars($userInfo['email']); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-4xl font-semibold mb-2">Age:</h2>
                    <p class="text-gray-700 text-xl sm:text-2xl"><?php echo htmlspecialchars($userInfo['age']); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-4xl font-semibold mb-2">Mobile Number:</h2>
                    <p class="text-gray-700 text-xl sm:text-2xl"><?php echo htmlspecialchars($userInfo['mobile']); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-4xl font-semibold mb-2">Gender:</h2>
                    <p class="text-gray-700 text-xl sm:text-2xl"><?php echo htmlspecialchars($userInfo['gender']); ?></p>
                </div>
                <div class="mb-4">
                    <h2 class="text-2xl sm:text-4xl font-semibold mb-2">Course:</h2>
                    <p class="text-gray-700 text-xl sm:text-2xl"><?php echo htmlspecialchars($userInfo['course']); ?></p>
                </div>
                <h2 class="text-2xl sm:text-4xl font-semibold mb-4">Change Password</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-4">
                        <label for="old_password" class="block text-gray-700 mb-2 text-lg sm:text-xl">Old Password:</label>
                        <input type="password" id="old_password" name="old_password" required class="border border-gray-300 px-3 py-2 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 mb-2 text-lg sm:text-xl">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required class="border border-gray-300 px-3 py-2 rounded-md w-full">
                    </div>
                    <div class="text-center">
                        <button type="submit" name="change_password" class="bg-blue-500 text-white text-lg sm:text-xl px-4 py-2 rounded-md hover:bg-blue-600">Change Password</button>
                    </div>
                </form>
                </div>
            <p class="text-center mt-4">
                <a href="user.php" class="text-blue-500 hover:underline text-lg sm:text-xl">Return to User Dashboard</a>
            </p>
        </div>
    </div>
</body>
</html>


