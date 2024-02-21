<?php
include "../../../database/koneksi.php";

session_start();

// Periksa apakah sesi username sudah diset atau belum
if (!isset($_SESSION['username'])) {
    header("Location: ../user/login.php");
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
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Hitung jumlah total baris data
$queryTotalRows = "SELECT COUNT(*) as total FROM users";
$resultTotalRows = mysqli_query($conn, $queryTotalRows);
$totalRows = mysqli_fetch_assoc($resultTotalRows)['total'];

// Batasan jumlah baris per halaman
$rowsPerPage = 5;

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
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/all.min.css">
    <link rel="stylesheet" href="../../css/fontawesome.min.css">
    <link rel="icon" href="../../assets/logo/logo-main.svg" type="image/x-icon">
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
                            <img class="h-8 w-auto" src="../../assets/logo/logo-secondary.svg" alt="Numérique Gallery">
                        </div>
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a href="../user/dashboard.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="../user/uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="../user/album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                <a href="./manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Search Box -->
                        <form action="../user/result_search.php" class="flex flex-row gap-2" method="GET">
                            <div class="hidden sm:block">
                                <input type="text" name="search" placeholder="Search" class="bg-gray-700 text-white px-4 py-3 h-8 rounded-md text-xs focus:outline-none focus:shadow-outline">
                            </div>
                            <button type="submit" class="text-white block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  h-8 w-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                        </form>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                            <div class="relative ml-3">
                                <div>
                                    <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <!-- ... (kode gambar profil) -->
                                        <span class="absolute -inset-1.5"></span>
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="../../../database/uploads/<?= $profile ?>" alt="<?= $username ?> profile photo">
                                    </button>
                                </div>
                                <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="../user/profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                    <a href="../user/setting_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                                    <a href="../user/logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <input type="text" placeholder="Search" class="bg-gray-700 w-full mb-2 text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                    <a href="../user/dashboard.php" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>
                    <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Upload<i class="baseline-add_shopping_cart"></i></a>
                    <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">My Album</a>
                </div>
            </div>
        </nav>

    </div>

    <!-- main-content -->
    <div class="container mx-auto ">
        <!-- component -->
        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md m-5">
            <div class="m-5 flex justify-between">
                <form action="search.php" method="GET">
                    <!-- <label for="search" class="block text-sm font-medium text-gray-700">Search:</label> -->
                    <div class="mt-1 relative flex gap-2 rounded-md shadow-sm">
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 w-80 focus:border-indigo-500 inline pr-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by username or email...">
                        <button type="submit" class="inset-y-0 right-0 px-3 py-2 bg-blue-500 text-white text-xs rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
                <div class="">
                    <a href="add-user.php" class="inset-y-0 right-0 w-10 px-3 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600"><i class="fa-solid fa-plus"></i></a>
                </div>
            </div>


            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">User</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Name</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Joined Since</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Role</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    <?php
                    // Query untuk mendapatkan data user dari database dengan batasan berdasarkan halaman yang sedang aktif
                    $queryUser = "SELECT * FROM users LIMIT $rowsPerPage OFFSET $offset";
                    $resultUser = mysqli_query($conn, $queryUser);

                    // Periksa apakah query berhasil dieksekusi
                    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
                        // Loop melalui setiap baris hasil query dan tampilkan data user
                        while ($row = mysqli_fetch_assoc($resultUser)) {
                            // Data user untuk tiap baris
                            $profile_photo = $row['profile_photo'];
                            $username = $row['username'];
                            $iduser = $row['userID'];
                            $email = $row['email'];
                            $name = $row['name'];
                            $role = $row['access_level'];
                            $createdAt = $row['createdAt'];
                    ?>
                            <tr class="hover:bg-gray-50">
                                <!-- profile username, email -->
                                <th class="flex gap-3 px-6 py-4 font-normal text-gray-900">
                                    <div class="relative h-10 w-10">
                                        <!-- profile_photo -->
                                        <img class="h-full w-full rounded-full object-cover object-center" src="../../../database/uploads/<?= $profile_photo ?>" alt="" />
                                    </div>
                                    <div class="text-sm">
                                        <!-- username -->
                                        <div class="font-medium text-gray-700"><?= $username ?></div>
                                        <!-- email -->
                                        <div class="text-gray-400"><?= $email ?></div>
                                    </div>
                                </th>
                                <!-- name -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center w-auto gap-1 rounded-full bg-green-50 px-4 py-2 text-xs font-semibold text-green-600">
                                        <?= $name ?>
                                    </span>
                                </td>
                                <!-- joined since -->
                                <td class="px-6 py-4"><?= $createdAt ?></td>
                                <!-- role -->
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <?php if ($role === 'admin') : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-600">
                                                Admin
                                            </span>
                                        <?php elseif ($role === 'user') : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-4 py-2 text-xs font-semibold text-green-600">
                                                User
                                            </span>
                                        <?php elseif ($role === "super_admin") : ?>
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-4 py-2 text-xs font-semibold text-red-600">
                                                Super Admin
                                            </span>

                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-4">
                                        <?php if ($role === 'admin' || $role === 'user') : ?>
                                            <form action="manage-user.php" method="get">
                                                <button type="submit" name="userID" onclick="togglePopup()" value="<?= $iduser ?>" class="hover:text-sky-600" x-data="{ tooltip: 'Edit' }">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" x-tooltip="tooltip">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <?php

                                            $usernameSession = $_SESSION['username'];

                                            ?>

                                            <?php if ($usernameSession === $username) : ?>
                                                <div></div>
                                            <?php else : ?>
                                                <button onclick="toggleUserPopup()" id="deleteUserButton" class="hover:text-red-600" onclick="return confirm('Are you sure you want to delete this album? This action cannot be undone.')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" x-tooltip="tooltip">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($role === 'super_admin') : ?>
                                            <div></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        // Bebaskan hasil query
                        mysqli_free_result($resultUser);
                    } else {
                        // Jika tidak ada data user yang ditemukan
                        ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No users found.</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

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

            <?php
            // Tangani klik tombol "Edit"
            if (isset($_GET['userID'])) {
                $userId = $_GET['userID'];

                // Query untuk mendapatkan data pengguna yang dipilih dari database
                $queryUser = "SELECT * FROM users WHERE userID = $userId";
                $resultUser = mysqli_query($conn, $queryUser);

                if ($resultUser && mysqli_num_rows($resultUser) > 0) {
                    $userData = mysqli_fetch_assoc($resultUser);
                    // Data pengguna yang dipilih
                    $profile_photo = $userData['profile_photo'];
                    $username = $userData['username'];
                    $id = $userData['userID'];
                    $email = $userData['email'];
                    $name = $userData['name'];
                    $role = $userData['access_level'];
                    $createdAt = $userData['createdAt'];
                }
            }
            ?>

            <!-- modal popup edit-user -->
            <div class="py-12 hidden transition duration-150 ease-in-out z-10 absolute top-0 right-0 bottom-0 left-0" id="userPopup">
                <div role="alert" class="container mx-auto w-11/12 md:w-2/3 max-w-lg">
                    <div class="relative py-8 px-5 md:px-10 bg-white shadow-md rounded border border-gray-400">
                        <div class="w-full flex justify-start text-gray-600 mb-3">
                            <i class="fa-solid fa-user-gear text-4xl"></i>
                        </div>
                        <h1 class="text-gray-800 font-lg font-bold tracking-normal leading-tight mb-4">Edit User</h1>

                        <form action="./edit-user.php" method="post">
                            <input type="hidden" name="userID" value="<?= $id ?>">
                            <!-- username -->
                            <label for="username" class="text-gray-800 text-sm font-bold leading-tight tracking-normal">Username</label>
                            <input id="username" name="username" class="mb-5 mt-2 text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center pl-3 text-sm border-gray-300 rounded border" value="<?= isset($username) ? $username : '' ?>" />

                            <!-- name -->
                            <label for="name" class="text-gray-800 text-sm font-bold leading-tight tracking-normal">Name</label>
                            <input id="name" name="name" class="text-gray-600 mb-5 mt-2 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center pl-3 text-sm border-gray-300 rounded border" value="<?= isset($name) ? $name : '' ?>" />

                            <!-- email -->
                            <label for="email" class="text-gray-800 text-sm font-bold leading-tight tracking-normal">Email</label>
                            <input id="email" name="email" class="text-gray-600 mb-5 mt-2 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center pl-3 text-sm border-gray-300 rounded border" value="<?= isset($email) ? $email : '' ?>" />

                            <!-- access level  -->
                            <label for="access_level" class="text-gray-800 text-sm font-bold leading-tight tracking-normal">Role</label>
                            <select class="text-gray-600 mb-5 mt-2 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center pl-3 text-sm border-gray-300 rounded border" name="access_level" id="access_level">
                                <option value="admin" <?= isset($role) && $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="user" <?= isset($role) && $role === 'user' ? 'selected' : '' ?>>User</option>
                            </select>

                            <div class="flex items-center justify-start">
                                <button name="submit" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 transition duration-150 ease-in-out hover:bg-indigo-600 bg-indigo-700 rounded text-white px-8 py-2 text-sm">Submit</button>
                            </div>
                        </form>
                        <button class="cursor-pointer absolute top-0 right-0 mt-4 mr-5 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out rounded focus:ring-2 focus:outline-none focus:ring-gray-600" onclick="togglePopup()" aria-label="close modal" role="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div id="deleteUserPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
                <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-2">Delete Photo</h2>
                    <p class="mb-4">Are you sure you want to delete this photo? This action cannot be undone.</p>
                    <div class="flex justify-center">
                        <form method="post" action="./delete-user.php">
                            <button type="submit" name="userID" value="<?= $iduser ?>" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" x-data="{ tooltip: 'Delete' }">
                                Yes
                            </button>
                            <button onclick="toggleUserPopup()" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-gray-200 focus:outline-none">
                                No
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- footer -->
    <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="../user/dashboard.php" aria-label="Go home" title="Company" class="inline-flex items-center">
                    <img src="../../assets/logo/logo-main.svg" class="h-10 w-auto" alt="Numérique Gallery">
                    <span class="ml-2 text-xl font-bold tracking-wide text-gray-800 uppercase">Numérique Gallery</span>
                </a>
                <div class="mt-6 lg:max-w-sm">
                    <p class="text-sm text-gray-800">
                        NUMÉRIQUE GALLERY is a website designed to store memories and photos in digital format.
                    </p>
                    <p class="mt-4 text-sm text-gray-800">
                        With NUMÉRIQUE GALLERY, we can upload our photos into our private gallery, as well as easily organize and store them.
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

    <script src="../../js/script.min.js"></script>
    <script>
        let open = false;
        const getFromUrl = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const iduser = urlParams.get("userID")
            return iduser
        }
        var popup = document.getElementById("userPopup");
        let iduser = getFromUrl()

        if (iduser != null) {
            popup.style.display = "block";
            open = true
        }


        function togglePopup() {
            if (!open) {
                popup.style.display = 'block';
                open = true;
            } else {
                popup.style.display = 'none';
                window.location.href = "manage-user.php"
                open = false;
            }
        }

        function toggleUserPopup() {
            var popup = document.getElementById("deleteUserPopup");
            popup.classList.toggle("hidden");
        }
    </script>

</body>

</html>