<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
    <title>Reservation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen relative">
<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    session_start();
    
    if (isset($_SESSION['reservation_errors']) && is_array($_SESSION['reservation_errors'])) {
        echo '<div class="absolute top-0 left-0 z-10 w-full bg-red-100 border border-red-400 text-red-700 rounded-md inline-block">';
        echo '<strong class="font-bold">Error!</strong>';
        foreach ($_SESSION['reservation_errors'] as $error) {
            echo '<p class="py-2">' . $error . '</p>';
        }
        echo '<button class="float-right text-red-500" onclick="dismissError(this)">x</button>';
        echo '</div>';
        unset($_SESSION['reservation_errors']);
    }
?>


<div class="max-w-md p-6 bg-white rounded-md shadow-md flex flex-col items-center relative z-0">
    <form action="process_reservation.php" method="post" class="w-full space-y-4">

        <div class="flex w-full space-x-4">
            <div class="w-1/2 pr-4">
                <label for="date" class="block text-lg font-semibold text-gray-600">Date and Time:</label>
                <input type="datetime-local" name="date" required
                       class="w-full px-4 py-2 border rounded-md text-lg focus:outline-none focus:border-blue-500">

                <label for="room_count" class="block text-lg font-semibold text-gray-600">Room Count:</label>
                <input type="number" name="room_count" required
                       class="w-full px-4 py-2 border rounded-md text-lg focus:outline-none focus:border-blue-500">

                <label for="purpose" class="block text-lg font-semibold text-gray-600">Purpose:</label>
                <input type="text" name="purpose" required
                       class="w-full px-4 py-2 border rounded-md text-lg focus:outline-none focus:border-blue-500">
            </div>

            <div class="w-1/2 pl-4">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label for="groupmate<?= $i ?>" class="block text-lg font-semibold text-gray-600">
                        Groupmate <?= $i ?>:
                    </label>
                    <input type="text" name="groupmate<?= $i ?>" required
                           class="w-full px-4 py-2 border rounded-md text-lg focus:outline-none focus:border-blue-500">
                <?php endfor; ?>
            </div>
        </div>

        <div class="flex w-full mt-6 space-x-4">
            <button type="submit" class="w-1/2 bg-green-500 text-white rounded-md px-4 py-2 cursor-pointer transition duration-300 hover:bg-green-600 text-lg">
                Submit Reservation
            </button>
            <a href="user.php" class="w-1/2 bg-blue-500 text-white rounded-md px-4 py-2 cursor-pointer transition duration-300 hover:bg-blue-600 text-lg text-center">
                Go Back
            </a>
        </div>
    </form>
</div>

<script>
    function dismissError(button) {
        button.parentElement.style.display = 'none';
    }
</script>
</body>
</html>
