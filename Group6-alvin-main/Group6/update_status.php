<?php

include('includes/db.php');
include('staff.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAuthenticator = new UserAuthenticator($conn);
    $staffDashboard = new StaffDashboard($conn, $userAuthenticator);

    if (!$userAuthenticator->isLoggedIn() || $userAuthenticator->getUserRole() !== 'staff') {
        header("Location: login.php?error=Unauthorized");
        exit();
    }

    $reservationId = $_POST['reservation_id'];
    $newStatus = $_POST['new_status'];

  
    $staffDashboard->updateReservationStatus($reservationId, $newStatus);
}
