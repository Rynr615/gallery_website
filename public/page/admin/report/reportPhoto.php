<?php

include "../../../../database/koneksi.php";

session_start();

// Periksa apakah sesi username sudah diset atau belum
if (!isset($_SESSION['username']) && $_SESSION['access_level'] !== 'admin' || $_SESSION['access_level'] !== 'super_admin') {
    header("Location: ../../user/dashboard.php");
    exit();
}

$username = $_SESSION["username"];

$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Periksa apakah query berhasil dieksekusi
if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $row = mysqli_fetch_assoc($result);
    $profile = $row['profile_photo'];
    $username = $row['username']; // Inisialisasi variabel username
    $accessLevel = $row['access_level']; //
    $profile_photo = $row['profile_photo']; //
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
}

// Hitung jumlah total baris data
$queryTotalRows = "SELECT COUNT(*) as total FROM reports";
$resultTotalRows = mysqli_query($conn, $queryTotalRows);
$totalRows = mysqli_fetch_assoc($resultTotalRows)['total'];

// Batasan jumlah baris per halaman
$rowsPerPage = 10;

// Hitung jumlah halaman
$totalPages = ceil($totalRows / $rowsPerPage);

// Tentukan halaman saat ini
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Hitung offset untuk query
$offset = ($current_page - 1) * $rowsPerPage;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Numérique Gallery</title>
    <link rel="stylesheet" href="../../../css/output.css">
    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="stylesheet" href="../../../css/all.min.css">
    <link rel="stylesheet" href="../../../css/fontawesome.min.css">
    <link rel="icon" href="../../../assets/logo/logo-main.svg" type="image/x-icon">
</head>

<body class="h-screen overflow-x-hidden font-poppins">
    <!-- navbar -->
    <div x-data="{ open: false, profileMenuOpen: false }">
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <button type="button" @click="open = !open" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                            <!-- ... (kode ikon menu) -->
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <div class="flex flex-shrink-0 items-center">
                            <img class="h-8 w-auto" src="../../../assets/logo/logo-secondary.svg" alt="Numérique Gallery">
                        </div>
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a href="../../user/dashboard.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="../../user/uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="../../user/album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                <a href="../manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                <a href="./reportPhoto.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Report Photo</a>
                                <a href="./reportAlbum.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Report Album</a>
                                <a href="../manage-user.php" hidden class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Search Box -->
                        <form action="../user/result_search.php" class="flex flex-row gap-2" method="GET">
                            <div class="hidden sm:block">
                                <input type="text" name="search" placeholder="Search" class="bg-gray-700 text-white px-4 py-3 h-8 rounded-md text-xs focus:outline-none focus:shadow-outline">
                            </div>
                            <button type="submit" class="text-white hidden sm:block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  h-8 w-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                        </form>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                            <div class="relative ml-3">
                                <div>
                                    <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <!-- ... (kode gambar profil) -->
                                        <span class="absolute -inset-1.5"></span>
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="../../../../database/uploads/<?= $profile_photo ?>" alt="<?= $row['username'] ?> profile photo">
                                    </button>
                                </div>
                                <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="../../user//profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                    <a href="../../user/setting_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                                    <button type="button" onclick="toggleSignOutPopup()" id="signOutButton" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <form action="../user/result_search.php" class="flex flex-row gap-2 mb-2" method="GET">
                        <input type="text" name="search" placeholder="Search" class="bg-gray-700 w-full text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                    </form>
                    <a href="../manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Manage User</a>
                    <a href="./reportPhoto.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Report Photo</a>
                    <a href="./reportAlbum.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Report Album</a>
                </div>
            </div>
        </nav>

        <div id="signOutPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Sign Out</h2>
                <p class="mb-4">Are you sure you want to Sign Out?</p>
                <div class="flex justify-center">
                    <form id="signOutForm" action="../../user/logout.php">
                        <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                            Yes
                        </button>
                    </form>
                    <button onclick="toggleSignOutPopup()" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-gray-200 focus:outline-none">
                        No
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- main-content -->
    <div class="container mx-auto ">
        <!-- component -->
        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
            <div class="m-5 flex justify-between">
                <form action="search.php" method="GET">
                    <!-- <label for="search" class="block text-sm font-medium text-gray-700">Search:</label> -->
                    <div class="mt-1 relative flex gap-2 rounded-md shadow-sm">
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 w-80 focus:border-indigo-500 inline pr-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by username">
                        <button type="submit" class="inset-y-0 right-0 px-3 py-2 bg-blue-500 text-white text-xs rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white text-left text-xs text-gray-500">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Reported By</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Reported</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Photo ID</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Reported on</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Report Type</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Reason</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900">additional Info</th>
                            <th scope="col" class="px-6 py-4 font-medium text-gray-900"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                        <?php
                        // Query untuk mendapatkan data laporan dari database dengan batasan berdasarkan halaman yang sedang aktif
                        $queryReport = "SELECT reports.reportID, reports.reportType, reports.photoID, reports.reason, reports.additionalInfo, users.username AS reportedBy, 
                    reported_users.username AS reportedUser, reports.reportedAt
                    FROM reports
                    INNER JOIN users ON reports.reportedBy = users.userID
                    INNER JOIN users AS reported_users ON reports.reportedUser = reported_users.userID
                    ORDER BY reports.reportedAt DESC LIMIT $rowsPerPage OFFSET $offset";
                        $resultReport = mysqli_query($conn, $queryReport);

                        // Periksa apakah query berhasil dieksekusi
                        if ($resultReport && mysqli_num_rows($resultReport) > 0) {
                            // Loop melalui setiap baris hasil query dan tampilkan data laporan
                            while ($row = mysqli_fetch_assoc($resultReport)) {
                        ?>
                                <tr class="hover:bg-gray-50">
                                    <!-- reportedBy -->
                                    <td class="px-6 py-4"><?= $row['reportedBy'] ?></td>
                                    <!-- reportedUser -->
                                    <td class="px-6 py-4"><?= $row['reportedUser'] ?></td>
                                    <!-- photoID -->
                                    <td class="px-6 py-4"><?= $row['photoID'] ?></td>
                                    <!-- reportedAt -->
                                    <td class="px-6 py-4"><?= $row['reportedAt'] ?></td>
                                    <!-- reportType -->
                                    <td class="px-6 py-4">
                                        <?php if ($row['reportType'] === 'spam') : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-4 py-2 text-xs font-semibold text-green-600">Spam</span>
                                        <?php elseif ($row['reportType'] === 'nudity') : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-4 py-2 text-xs font-semibold text-yellow-600">Nudity</span>
                                        <?php elseif ($row['reportType'] === "violence") : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-4 py-2 text-xs font-semibold text-orange-600">Violence</span>
                                        <?php elseif ($row['reportType'] === "terrorism") : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-4 py-2 text-xs font-semibold text-red-600">Terrorism</span>
                                        <?php elseif ($row['reportType'] === "hate_speech") : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-4 py-2 text-xs font-semibold text-purple-600">Hate Speech</span>
                                        <?php elseif ($row['reportType'] === "sexual_harrasment") : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-600">Sexual Harassment</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- reason -->
                                    <td class="px-6 py-4"><?= $row['reason'] ?></td>
                                    <!-- additionalInfo -->
                                    <td class="px-6 py-4"><?= $row['additionalInfo'] ?></td>
                                    <!-- Link to Album -->
                                    <td class="px-6 py-4">
                                        <!-- Trash Icon for Delete -->
                                        <a href="./detele_photo_reports.php?reportID=<?= $row['reportID'] ?>" class="hover:text-red-600 ease-in-out mr-2">
                                            <i class="fa-solid fa-trash-can font-normal"></i>
                                        </a>
                                        <a href="../../user/albumUser/photoAlbum.php?photoID=<?= $row['photoID'] ?>" class="hover:text-blue-600 ease-in-out">
                                            <i class="fa-solid fa-arrow-right font-normal"></i>
                                        </a>
                                    </td>
                                </tr>

                            <?php
                            }
                            // Bebaskan hasil query
                            mysqli_free_result($resultReport);
                        } else {
                            // Jika tidak ada data laporan yang ditemukan
                            ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No reports found.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>

                </table>
            </div>

            <!-- pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                    <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div></div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <?php if ($current_page > 1) : ?>
                                <a href="?page=<?= ($current_page - 1) ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <a href="?page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($current_page < $totalPages) : ?>
                                <a href="?page=<?= ($current_page + 1) ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block sm:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
            <a href="../../user/dashboard.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-house w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Home</span>
            </a>
            <a href="../../user/uploads.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-upload w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11.074 4 8.442.408A.95.95 0 0 0 7.014.254L2.926 4h8.148ZM9 13v-1a4 4 0 0 1 4-4h6V6a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h17a1 1 0 0 0 1-1v-2h-6a4 4 0 0 1-4-4Z" />
                    <path d="M19 10h-6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1Zm-4.5 3.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM12.62 4h2.78L12.539.41a1.086 1.086 0 1 0-1.7 1.352L12.62 4Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Uploads</span>
            </a>
            <a href="../../user/album.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-folder w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">My Album</span>
            </a>
            <a href="../../user/profile.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <img src="../../../../database/uploads/<?= $profile_photo ?>" alt="" class="h-8 w-8 rounded-full">
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"><?= $username ?></span>
            </a>
        </div>
    </div>

    <!-- footer -->
    <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="../../user/dashboard.php" aria-label="Go home" title="Company" class="inline-flex items-center">
                    <img src="../../../assets/logo/logo-main.svg" class="h-10 w-auto" alt="Numérique Gallery">
                    <span class="ml-2 text-xl font-bold tracking-wide text-gray-800 uppercase">Numérique Gallery</span>
                </a>
                <div class="mt-6 lg:max-w-sm">
                    <p class="text-sm text-gray-800">
                        NUMÉRIQUE GALLERY is a website designed to store memories and photos in digital format.
                    </p>
                    <p class="mt-4 text-sm text-gray-800">
                        With NUMÉRIQUE GALLERY, we can upload our photos into our private gallery, as well as easily
                        organize and store them.
                    </p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <p class="text-base font-bold tracking-wide text-gray-900">Contacts</p>
                <div class="flex">
                    <p class="mr-1 text-gray-800">Phone:</p>
                    <span class="transition-colors duration-300 text-deep-purple-accent-400 hover:text-deep-purple-800">087718603438</span>
                </div>
                <div class="flex">
                    <p class="mr-1 text-gray-800">Email:</p>
                    <span class="transition-colors duration-300 text-deep-purple-accent-400 hover:text-deep-purple-800">ryanyanuar184@gmail.com</span>
                </div>
            </div>
            <div>
                <span class="text-base font-bold tracking-wide text-gray-900">Social</span>
                <div class="flex items-center mt-1 space-x-3">
                    <!-- github -->
                    <a href="https://github.com/Rynr615" target="_blank" class="text-gray-500 transition-colors duration-300 hover:text-gray-500">
                        <i class="fa-brands fa-github text-xl"></i>
                    </a>
                    <!-- instagram -->
                    <a href="https://www.instagram.com/ryn_ynr/" target="_blank" class="text-gray-500 transition-colors duration-300 hover:hover:text-gray-500">
                        <i class="fa-brands fa-instagram text-xl"></i>
                    </a>
                    <!-- facebook -->
                    <a href="https://www.facebook.com/profile.php?id=100055706964435" target="_blank" class="text-gray-500 transition-colors duration-300 hover:hover:text-gray-500">
                        <i class="fa-brands fa-facebook text-xl"></i>
                    </a>
                </div>
                <p class="mt-4 text-sm text-gray-500">
                    Don't forget to follow.
                </p>
            </div>
        </div>
        <div class="flex flex-col-reverse justify-between pt-5 pb-10 border-t lg:flex-row">
            <p class="text-sm text-gray-600">
                © Copyright 2024 Ryan Yanuar Pradana. All rights reserved.
            </p>
        </div>
    </div>

    <script src="../../../js/script.min.js"></script>
    <script>
        function toggleUserPopup() {
            var popup = document.getElementById("deleteUserPopup");
            popup.classList.toggle("hidden");
        }

        function toggleSignOutPopup() {
            var popup = document.getElementById("signOutPopup");
            popup.classList.toggle("hidden");
        }
    </script>

</body>

</html>