<?php


include('includes/db.php');

class LoginHandler {
    private $db;

    public function __construct($db_host, $db_name, $db_user, $db_password) {
        $this->db = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        session_start();
    }
    

    private function validateInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function validateUsername($username) {
      
        return $this->validateInput($username);
    }

    private function validatePassword($password) {
    
        return $this->validateInput($password);
    }

    private function redirectTo($role) {
        $validRoles = ['admin', 'staff', 'user'];

        if (in_array($role, $validRoles, true)) {
            header("Location: " . htmlspecialchars("$role.php"));
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid role";
            header("Location: index.php");
            exit();
        }
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->validateUsername($_POST['username']);
            $password = $this->validatePassword($_POST['password']);

            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = "Invalid input data";
                header("Location: index.php");
                exit();
            }

            try {
                $stmt = $this->db->prepare("SELECT id, password, role FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $hashedPassword = $row['password'];
                    $isValidPassword = sodium_crypto_pwhash_str_verify($hashedPassword, $password);

                    if ($isValidPassword) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['user_role'] = $row['role'];
                        error_log("User ID: " . $_SESSION['user_id']);
                        error_log("User Role: " . $_SESSION['user_role']);
                        $this->redirectTo($row['role']);
                    } else {
                        $_SESSION['login_error'] = "Invalid username or password";
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    $_SESSION['login_error'] = "Invalid username or password";
                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                error_log("Database Error: " . $e->getMessage());
                $_SESSION['login_error'] = "An error occurred during login. Please try again later.";
                header("Location: index.php");
                exit();
            }
        }
    }
}

$loginHandler = new LoginHandler($db_host, $db_name, $db_user, $db_password);
$loginHandler->setSessionCookieParams();
$loginHandler->handleLogin();
