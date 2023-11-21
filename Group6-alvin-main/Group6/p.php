<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="icon" href="images/ctu.png" type="image/x-icon">
</head>

<body class="bg-gray-100 font-sans p-4 sm:p-6 md:p-8 lg:p-12 xl:p-16">
    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl 2xl:text-8xl font-bold text-center mb-6 sm:mb-12">User Dashboard</h1>
    <form method="post" action="" class="flex justify-center">
        <button type="submit" name="logout"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-4 px-6 sm:py-6 sm:px-8 lg:px-10 rounded">Logout</button>
        <a href="profile.php"
            class="bg-green-500 hover-bg-green-700 text-white font-bold py-4 px-6 mx-2 sm:mx-4 sm:py-6 sm:px-8 lg:px-10 rounded">Profile</a>
    </form>

    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-semibold mt-6 sm:mt-12">Your Reservations</h2>
    <div class="overflow-x-auto mt-4 sm:mt-6 md:mt-8 lg:mt-12 xl:mt-16">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="w-full table-auto border border-collapse sm:w-auto">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-3 sm:py-6 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Date and Time</th>
                        <th class="px-4 py-3 sm:py-6 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Room Count</th>
                        <th class="px-4 py-3 sm:py-6 md:w-2/4 lg:w-2/4 xl:w-1/5 border">Purpose</th>
                        <th class="px-4 py-3 sm:py-6 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Groupmates</th>
                        <th class="px-4 py-3 sm:py-6 md:w-1/4 lg:w-1/4 xl:w-1/5 border">Course</th>
                        <th class="px-4 py-3 sm:py-6 md:w-2/4 lg:w-2/4 xl:w-3/5 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userReservations as $reservation): ?>
                        <tr>
                            <td class="px-4 py-3 sm:py-6 border">
                                <?= htmlspecialchars(formatDateTime($reservation['time'])) ?>
                            </td>
                            <td class="px-4 py-3 sm:py-6 border">
                                <?= htmlspecialchars($reservation['room_count']) ?>
                            </td>
                            <td class="px-4 py-3 sm:py-6 border">
                                <?= htmlspecialchars($reservation['purpose']) ?>
                            </td>
                            <td class="px-4 py-3 sm:py-6 border">
                                <?php
                                $groupmates = $this->getGroupmates($reservation['id']);
                                foreach ($groupmates as $groupmate) {
                                    echo htmlspecialchars($groupmate['username']) . '<br>';
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 sm:py-6 border">
                                <?php
                                foreach ($groupmates as $groupmate) {
                                    echo htmlspecialchars($groupmate['course']) . '<br>';
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 sm:py-6 border <?= $reservation['status'] === 'pending' ? 'bg-blue-600' : ($reservation['status'] === 'approved' ? 'bg-green-500' : 'bg-red-500') ?>">
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
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 sm:py-6 sm:px-8 lg:px-10 rounded">Create Reservation</button>
    </form>
</body>

</html>
