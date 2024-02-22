<?php
include "../../../database/koneksi.php";
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ./login.php");
    exit();
}

// Inisialisasi variabel username
$username = $_SESSION['username'];

// Lakukan query untuk mendapatkan data pengguna berdasarkan username
$queryUser = "SELECT * FROM users WHERE username = '$username'";
$resultUser = mysqli_query($conn, $queryUser);

// Periksa apakah query berhasil dieksekusi
if ($resultUser && mysqli_num_rows($resultUser) > 0) {
    // Ambil data pengguna terbaru
    $row = mysqli_fetch_assoc($resultUser);
    $profile_photo = $row['profile_photo'];
    $username = $row['username']; // Inisialisasi variabel username
    $accesLevel = $row['access_level'];
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Periksa apakah parameter pencarian tersedia dalam URL
if (isset($_GET['search'])) {
    // Escape input pencarian untuk menghindari SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    // Buat query untuk mencari posting berdasarkan judulnya
    $query = "SELECT photos.photoID, photos.title, photos.description, photos.image_path, photos.createdAt, users.username AS uploader 
    FROM photos 
    INNER JOIN users ON photos.userID = users.userID 
    WHERE photos.title LIKE '%$search%' 
    ORDER BY photos.createdAt DESC";

    $result = mysqli_query($conn, $query);


    // Periksa apakah query berhasil dieksekusi
    if ($result && mysqli_num_rows($result) > 0) {
        // Query berhasil, tampilkan hasil pencarian
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

        <body class="h-screen font-poppins">
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
                                        <a href="./dashboard.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                        <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                        <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                        <?php if ($accesLevel === 'admin' || $accesLevel === 'super_admin') : ?>
                                            <a href="../admin/manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                        <?php elseif ($accesLevel === 'user') : ?>
                                            <a href="../admin/manage-user.php" hidden class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <!-- Search Box -->
                                <form action="./result_search.php" class="flex flex-row gap-2" method="GET">
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
                                                <img class="h-8 w-8 rounded-full" src="../../../database/uploads/<?= $profile_photo ?>" alt="<?= $row['username'] ?> profile photo">
                                            </button>
                                        </div>
                                        <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                            <a href="./profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                            <a href="./setting_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
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
                            <a href="./dashboard.php" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>
                            <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Upload<i class="baseline-add_shopping_cart"></i></a>
                            <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">My Album</a>
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

            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <input type="text" placeholder="Search" class="bg-gray-700 w-full mb-2 text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                    <a href="./dashboard.php" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>
                    <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Upload<i class="baseline-add_shopping_cart"></i></a>
                    <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">My Album</a>
                </div>
            </div>
            </nav>

            </div>

            <!-- main-content -->
            <div class="container">
                <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 p-4 md:p-2 xl:p-5 m-5">

                    <?php
                    // Loop through hasil query dan tampilkan data
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <!-- card  -->
                        <div class="relative bg-white border rounded-lg shadow-md dark:bg-gray-800 dark:border-gray-700 transform transition duration-500 hover:scale-105">
                            <div class="p-2 flex justify-center">
                                <a href="./post.php?photoID=<?= $row['photoID'] ?>" style="display: block; width: 100%; height: 0; padding-bottom: 56.25%; position: relative;">
                                    <!-- Tambahkan kelas CSS untuk memastikan rasio 16:9 -->
                                    <img class="rounded-md object-cover w-full h-full absolute inset-0" src="../../../database/uploads/<?php echo $row['image_path']; ?>" loading="lazy" alt="<?php echo $row['title']; ?>">
                                </a>
                            </div>

                            <div class="px-4 pb-3">
                                <div>
                                    <a href="./post.php?photoID=<?= $row['photoID'] ?>">
                                        <h5 class="text-xl font-semibold tracking-tight hover:text-blue-800 dark:hover:text-blue-300 text-gray-900 dark:text-white ">
                                            <?php echo $row['title']; ?>
                                        </h5>
                                    </a>

                                    <p class="antialiased text-gray-600 dark:text-gray-400 text-sm break-all">
                                        <?php
                                        $description = $row['description'];
                                        if (strlen($description) > 20) {
                                        ?>
                                            <!-- Jika deskripsi lebih dari 20 karakter, potong dan tambahkan tautan "lihat postingan" -->
                                            <span><?= substr($description, 0, 20) ?></span>
                                            <a href="./post.php?photoID=<?= $row['photoID'] ?>" class="text-blue-500 hover:text-white">lihat postingan</a>
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
                    // Bebaskan hasil query
                    mysqli_free_result($result);
                    // Tutup koneksi
                    mysqli_close($conn);
                    ?>

                </div>
            </div>

            <!-- footer -->
            <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
                <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2">
                        <a href="./dashboard.php" aria-label="Go home" title="Company" class="inline-flex items-center">
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
                function toggleSignOutPopup() {
                    var popup = document.getElementById("signOutPopup");
                    popup.classList.toggle("hidden");
                }
            </script>

        </body>

        </html>
<?php
    }
} else {
    // Tidak ada hasil pencarian yang ditemukan
    echo "No results found.";
}
?>