<?php
include "../../../../database/koneksi.php";

// Start session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ../../index.php");
    exit();
}

// Variabel username sudah pasti terdefinisi jika sampai di sini
$username = $_SESSION['username'];

// Ambil albumID dari parameter URL
$albumID = $_GET['albumID'];

$queryTotalRows = "SELECT COUNT(*) as total FROM photos WHERE albumID = '$albumID'";
$resultTotalRows = mysqli_query($conn, $queryTotalRows);
$totalRows = mysqli_fetch_assoc($resultTotalRows)['total'];

// Batasan jumlah baris per halaman
$rowsPerPage = 12;

// Hitung jumlah halaman
$totalPages = ceil($totalRows / $rowsPerPage);

// Tentukan halaman saat ini
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Hitung offset untuk query
$offset = ($current_page - 1) * $rowsPerPage;

// Lakukan koneksi dan query untuk mendapatkan userID
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $pengguna = mysqli_fetch_assoc($result);

    // Dapatkan userID dari data pengguna
    $userID = $pengguna['userID'];
    $username = $pengguna['username'];
    $profile_photo = $pengguna['profile_photo'];
    $accesLevel = $pengguna['access_level'];

    // Ambil data foto dari album yang dipilih
    $queryPhotos = "SELECT photos.photoID, photos.title, photos.description, photos.image_path, photos.createdAt, users.username AS uploader 
    FROM photos
    INNER JOIN users ON photos.userID = users.userID 
    WHERE photos.albumID = $albumID
    ORDER BY photos.createdAt DESC
    LIMIT $offset, $rowsPerPage";
    $resultPhotos = mysqli_query($conn, $queryPhotos);

    $queryAlbums = "SELECT albums.albumID, albums.title, albums.description, albums.thumbnail_album, albums.createdAt, users.username AS username, users.userID AS userID
    FROM albums
    INNER JOIN users ON albums.userID = users.userID 
    WHERE albums.albumID = $albumID
    ORDER BY albums.createdAt DESC";

    $resultAlbums = mysqli_query($conn, $queryAlbums);
} else {
    // Handle kesalahan query
    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    exit();
}
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
                                <a href="../dashboard.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="../uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="../album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                <?php if ($accesLevel === 'admin' || $accesLevel === 'super_admin') : ?>
                                    <a href="../../admin/manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                    <a href="../../admin/report/reportPhoto.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Report</a>
                                <?php elseif ($accesLevel === 'user') : ?>
                                    <a href="../../admin/manage-user.php" hidden class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Search Box -->
                        <form action="../result_search.php" class="flex flex-row gap-2" method="GET">
                            <div class="hidden sm:block">
                                <input type="text" name="search" placeholder="Search" class="bg-gray-700 text-white px-4 py-3 h-8 rounded-md text-xs focus:outline-none focus:shadow-outline">
                            </div>
                            <button type="submit" class="text-white block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  h-8 w-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                        </form>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                            <a href="../../admin/report/reportPhoto.php" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </a>
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
                                    <a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                    <a href="../setting_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                                    <button type="button" onclick="toggleSignOutPopup()" id="signOutButton" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <input type="text" placeholder="Search" class="bg-gray-700 w-full mb-2 text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                    <a href="../dashboard.php" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>
                    <a href="../uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Upload<i class="baseline-add_shopping_cart"></i></a>
                    <a href="../album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">My Album</a>
                </div>
            </div>
        </nav>

        <div id="signOutPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Sign Out</h2>
                <p class="mb-4">Are you sure you want to Sign Out?</p>
                <div class="flex justify-center">
                    <form id="signOutForm" action="./logout.php">
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

    <div class="container">
        <div id="deleteAlbumPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Delete Album</h2>
                <p class="mb-4">Are you sure you want to delete this album? This action cannot be undone.</p>
                <div class="flex justify-center">
                    <form id="deleteAlbumForm" action="../delete_album.php" method="get">
                        <input type="hidden" name="albumID" value="<?= $albumID ?>">
                        <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                            Yes
                        </button>
                    </form>
                    <button onclick="toggleDeletePopup()" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-gray-200 focus:outline-none">
                        No
                    </button>
                </div>
            </div>
        </div>
        <?php

        if ($resultAlbums && mysqli_num_rows($resultAlbums) > 0) {
            while ($row = mysqli_fetch_array($resultAlbums)) {

                $title = $row['title'];
                $username = $row['username'];
                $createdAt = $row['createdAt'];
                $description = $row['description'];
                $userIDAlbum = $row['userID'];
            }
        }
        ?>
        <div class="flex justify-between items-center m-10">
            <div>
                <p class="font-medium text-xl">
                    <span>Title: </span> <?= $title ?>
                </p>
                <p class="font-light text-gray-500 text-sm">
                    <span>Uploaded by: </span> <?= $username ?>
                </p>
                <p class="text-xs pt-2">
                    <span>Created at: </span><?= $createdAt ?>
                </p>
            </div>
            <div class="flex gap-2">
                <?php if ($accesLevel === 'admin' || $accesLevel === 'super_admin') : ?>
                    <?php if ($userID !== $userIDAlbum) : ?>
                        <div class="relative">
                            <button onclick="toggleReportPopup()" class="absolute bottom-0 right-0 pt-2 text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base px-4 py-2  dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                                <!-- Ganti dengan ikon flag yang diinginkan -->
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </button>
                        </div>
                    <?php endif ?>
                    <button onclick="toggleDeletePopup()" id="deleteAlbumButton" class="text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800" onclick="return confirm('Are you sure you want to delete this album? This action cannot be undone.')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                <?php elseif ($userID === $userIDAlbum) : ?>
                    <button onclick="toggleDeletePopup()" id="deleteAlbumButton" class="text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800" onclick="return confirm('Are you sure you want to delete this album? This action cannot be undone.')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                <?php else : ?>
                    <div class="relative">
                        <button onclick="toggleReportPopup()" class="absolute bottom-0 right-0 pt-2 text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                            <!-- Ganti dengan ikon flag yang diinginkan -->
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </button>
                    </div>
                <?php endif ?>
            </div>

        </div>
        <div class="relative">
            <div class="px-5">
                <p class="pt-2">
                    <?= $description ?>
                </p>
            </div>
        </div>

        <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 p-4 md:p-2 xl:p-5 m-5">
            <?php
            // Tampilkan setiap foto dari album yang dipilih
            if ($resultPhotos && mysqli_num_rows($resultPhotos) > 0) {
                while ($row = mysqli_fetch_assoc($resultPhotos)) {
            ?>
                    <!-- card  -->
                    <div class="relative bg-white border rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 transform transition duration-500 hover:scale-105">
                        <div class="p-2 flex justify-center">
                            <a href="../post.php?photoID=<?= $row['photoID'] ?>" style="display: block; width: 100%; height: 0; padding-bottom: 56.25%; position: relative;">
                                <!-- Tambahkan kelas CSS untuk memastikan rasio 16:9 -->
                                <img class="rounded-md object-cover w-full h-full absolute inset-0" src="../../../../database/uploads/<?php echo $row['image_path']; ?>" loading="lazy" alt="<?php echo $row['title']; ?>">
                            </a>
                        </div>

                        <div class="px-4 pb-3">
                            <div>
                                <a href="../user/post.php?photoID=<?= $row['photoID'] ?>">
                                    <h5 class="text-xl font-semibold tracking-tight hover:text-blue-800 dark:hover:text-blue-300 text-gray-900 dark:text-white ">
                                        <?php echo $row['title']; ?>
                                    </h5>
                                </a>

                                <p class="antialiased text-gray-600 dark:text-gray-400 text-sm break-all">
                                    <?php
                                    $description = $row['description'];
                                    if (strlen($description) > 25) {
                                    ?>
                                        <!-- Jika deskripsi lebih dari 20 karakter, potong dan tambahkan tautan "lihat postingan" -->
                                        <span><?= substr($description, 0, 25) ?>...... </span><br>
                                        <a href="../post.php?photoID=<?= $row['photoID'] ?>" class="text-blue-500 hover:text-white">lihat selengkapnya</a>
                                    <?php
                                    } else {
                                        // Jika kurang dari atau sama dengan 20 karakter, tampilkan normal
                                        echo $description;
                                    }
                                    ?>
                                </p>

                                <p class="mt-2 text-gray-500 text-sm">
                                    Uploaded by
                                    <span class="hover:text-white" style="cursor: default;">
                                        <?php echo $row['uploader']; ?>
                                    </span>

                                    on <br>
                                    <span>
                                        <?php echo date('F j Y, g:i a', strtotime($row['createdAt'])); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No photos found in this album.</p>";
            }
            ?>
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div></div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <?php if ($current_page > 1) : ?>
                        <a href="?albumID=<?= $albumID ?>&page=<?= ($current_page - 1) ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <a href="?albumID=<?= $albumID ?>&page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($current_page < $totalPages) : ?>
                        <a href="?albumID=<?= $albumID ?>&page=<?= ($current_page + 1) ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    <?php endif; ?>

                </nav>
            </div>
        </div>

        <div id="ReportPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Report</h2>
                <p class="">Select one that is relevant</p>
                <div class="flex justify-center">
                    <form method="post" action="report_album.php">
                        <input type="hidden" name="userID" value="<?= $userID ?>">
                        <input type="hidden" name="reportedUser" value="<?= $userIDAlbum ?>">
                        <input type="hidden" name="albumID" value="<?= $albumID ?>">
                        <fieldset class="m-3">
                            <div class="mt-3 mb-3 space-y-6">
                                <!-- Checkbox for Spam -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="spam" name="reportType" type="radio" value="spam" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="spam" class="font-medium text-gray-900">Spam</label>
                                        <p class="text-gray-500">Irrelevant or unwanted comments on a post</p>
                                    </div>
                                </div>
                                <!-- Checkbox for Nudity -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="nudity" name="reportType" type="radio" value="nudity" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="nudity" class="font-medium text-gray-900">Nudity</label>
                                        <p class="text-gray-500">Inappropriate content or not in line with ethics.</p>
                                    </div>
                                </div>
                                <!-- Checkbox for Violence -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="violence" name="reportType" type="radio" value="violence" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="violence" class="font-medium text-gray-900">Violence</label>
                                        <p class="text-gray-500">Threats or content posing a risk of violence.</p>
                                    </div>
                                </div>
                                <!-- Checkbox for Terrorism -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="terrorism" name="reportType" type="radio" value="terrorism" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="terrorism" class="font-medium text-gray-900">Terrorism</label>
                                        <p class="text-gray-500">Content related to terrorism or radicalism.</p>
                                    </div>
                                </div>
                                <!-- Checkbox for Hate Speech -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="hate_speech" name="reportType" type="radio" value="hate_speech" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="hate_speech" class="font-medium text-gray-900">Hate speech</label>
                                        <p class="text-gray-500"> Hate speech or discrimination.</p>
                                    </div>
                                </div>
                                <!-- Checkbox for Sexual Harassment -->
                                <div class="relative flex gap-x-3">
                                    <div class="flex h-6 items-center">
                                        <input id="sexual_harassment" name="reportType" type="radio" value="sexual_harassment" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    </div>
                                    <div class="text-sm leading-6">
                                        <label for="sexual_harassment" class="font-medium text-gray-900">Sexual Harassment</label>
                                        <p class="text-gray-500">Inappropriate behavior or sexual harassment.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <!-- Reason Input -->
                                <div class="col-span-full pt-3 gap-x-3">
                                    <label for="reason" class="block text-sm font-medium leading-6 text-gray-900">Reason</label>
                                    <div class="mt-2">
                                        <input id="reason" name="reason" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>
                                <!-- Additional Info Input -->
                                <div class="col-span-full gap-x-3">
                                    <label for="additionalInfo" class="block text-sm font-medium leading-6 text-gray-900">Additional Info</label>
                                    <div class="mt-2">
                                        <textarea id="additionalInfo" name="additionalInfo" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <!-- Submit Buttons -->
                        <div class="mx-auto">
                            <button type="submit" name="report" class="text-white mt-2 bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">Report</button>
                            <button onclick="toggleReportPopup()" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-gray-200 focus:outline-none">No</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- footer -->
    <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="../../dashboard.php" aria-label="Go home" title="Company" class="inline-flex items-center">
                    <img src="../../../assets/logo/logo-main.svg" class="h-10 w-auto" alt="Numérique Gallery">
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

    <script src="../../../js/script.min.js"></script>

    <script>
        // Function untuk menampilkan/menyembunyikan pop-up
        function togglePopup() {
            var popup = document.getElementById("albumPopup");
            var button = document.getElementById("albumPopupButton");
            if (popup.classList.contains("hidden")) {
                popup.classList.remove("hidden");
                button.innerHTML = "<i class='fa-solid fa-pen-to-square'></i>";
            } else {
                popup.classList.add("hidden");
                button.innerHTML = "<i class='fa-solid fa-pen-to-square'></i>";
            }
        }

        function togglealbumPhotoPopup() {
            var popup = document.getElementById("albumPhotoPopup");
            var button = document.getElementById("albumPhotoPopupButton");
            if (popup.classList.contains("hidden")) {
                popup.classList.remove("hidden");
                button.innerHTML = "<div class='border border-dashed h-52 rounded-md w-full overflow-hidden'><i class='fa-regular fa-file-image pt-16' style='text-align: center; font-size: 56px; color: rgb(226, 232, 240);'></i></div>";
            } else {
                popup.classList.add("hidden");
                button.innerHTML = "<div class='border border-dashed h-52 rounded-md w-full overflow-hidden'><i class='fa-regular fa-file-image pt-16' style='text-align: center; font-size: 56px; color: rgb(226, 232, 240);'></i></div>";
            }
        }

        document.getElementById("thumbnail").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("thumbnailPreview").src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById("thumbnailPreview").src = "#";
            }
        });

        // Hilangkan alert setelah 3 detik
        setTimeout(function() {
            var alertElement = document.querySelector('.p-4.mb-4.text-sm.rounded-lg');
            alertElement.remove();
        }, 5000);

        function toggleDeletePopup() {
            var popup = document.getElementById("deleteAlbumPopup");
            popup.classList.toggle("hidden");
        }

        function toggleSignOutPopup() {
            var popup = document.getElementById("signOutPopup");
            popup.classList.toggle("hidden");
        }

        function toggleReportPopup() {
            var popup = document.getElementById("ReportPopup");
            popup.classList.toggle("hidden");
        }
    </script>


</body>

</html>