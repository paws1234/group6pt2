<?php

$db_host = 'localhost';
$db_user = 'paws';
$db_password = 'paws';
$db_name = 'computer_lab_reservation';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

