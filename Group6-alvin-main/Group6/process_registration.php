<?php
session_start();
include('includes/db.php');

class UserRegistration {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function setSessionCookieParams() {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true,
            true
        );
    }

    private function validateInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function isValidUsername($username) {
       
        return preg_match("/^[a-zA-Z0-9_]+$/", $username);
    }

    private function isValidPassword($password) {

        return strlen($password) >= 8;
    }

    public function registerUser($username, $password, $confirmPassword, $email, $mobile, $gender, $age, $course) {
        $username = $this->validateInput($username);
        $password = $this->validateInput($password);
        $confirmPassword = $this->validateInput($confirmPassword);
        $email = $this->validateInput($email);
        $mobile = $this->validateInput($mobile);
        $gender = $this->validateInput($gender);
        $age = (int)$this->validateInput($age);
        $course = $this->validateInput($course);

        if ($this->isValidUsername($username) && $this->isValidPassword($password) && $password === $confirmPassword) {            if (extension_loaded('sodium')) {
                $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);
                $opsLimit = defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE') ? SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE : SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE;
                $memLimit = defined('SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE') ? SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE : SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE;
                $hashedPassword = sodium_crypto_pwhash_str(
                    $password,
                    $opsLimit,
                    $memLimit
                );
                if ($hashedPassword !== false) {
                    try {
                        $query = "INSERT INTO users (username, password, email, mobile, gender, age, course, role) VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
                        $stmt = $this->conn->prepare($query);
                        $stmt->execute([$username, $hashedPassword, $email, $mobile, $gender, $age, $course]);
                        session_regenerate_id(true);
                        header("Location: index.php");
                        exit();
                    } catch (PDOException $e) {
                        error_log("Database Error: " . $e->getMessage());
                        echo "Registration failed. Please try again.";
                    }
                } else {
                    echo "Password hashing failed.";
                }
            } else {
                echo "Sodium extension is not available. Please install and enable it to use password hashing.";
            }
        } else {
            $_SESSION['registration_error'] = "Invalid username or password for registration. Username should only contain alphanumeric characters and underscore, and the password should be at least 8 characters long and match the confirmation password.";
            header("Location: index.php");
            exit();
        }
    }
}

$userRegistration = new UserRegistration($conn);
$userRegistration->setSessionCookieParams();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $course = $_POST['course'];

    $userRegistration->registerUser($username, $password, $confirmPassword, $email, $mobile, $gender, $age, $course);
}
?>
