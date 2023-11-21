<?php

include('includes/db.php');

class UserAuthenticator {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function isAdmin() {
    
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function isUserAuthorized($userID, $role) {
        try {
            $stmt = $this->conn->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userID]);
            $userRole = $stmt->fetchColumn();

            if ($userRole === $role) {
                return true;
            }

            return false; 
        } catch (PDOException $e) {
            return false; 
        }
    }

    public function login($username, $password) {
        $query = "SELECT id, role, password FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
    
        
            error_log("User ID: " . $_SESSION['user_id']);
            error_log("User Role: " . $_SESSION['user_role']);
    
            return true;
        }
    
        return false;
    }
    

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getUserRole() {
        return $this->isLoggedIn() ? $_SESSION['user_role'] : 'role';
    }
}

$userAuthenticator = new UserAuthenticator($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $userAuthenticator->logout();
}
?>
