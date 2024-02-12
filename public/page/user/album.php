<?php

include "../../../database/koneksi.php";

// Start session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ./login.php");
    exit();
}

// Variabel username sudah pasti terdefinisi jika sampai di sini
$username = $_SESSION['username'];

// Lakukan koneksi dan query untuk mendapatkan userID
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $pengguna = mysqli_fetch_assoc($result);

    // Dapatkan userID dari data pengguna
    $userID = $pengguna['userID'];

    // Proses tambah album
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Cek apakah thumbnail diunggah
        if ($_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $thumbnail_name = $_FILES['thumbnail']['name'];
            $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
            $thumbnail_size = $_FILES['thumbnail']['size'];
            $thumbnail_type = $_FILES['thumbnail']['type'];

            // Tentukan ekstensi file yang diperbolehkan
            $allowed_extensions = array('png', 'jpg', 'jpeg', 'svg');

            // Tentukan ukuran maksimum file (dalam bytes)
            $max_file_size = 5 * 1024 * 1024; // 5 MB

            // Ambil ekstensi file thumbnail
            $thumbnail_extension = strtolower(pathinfo($thumbnail_name, PATHINFO_EXTENSION));

            // Validasi ekstensi file
            if (!in_array($thumbnail_extension, $allowed_extensions)) {
                echo "Ekstensi file thumbnail tidak valid. Hanya file dengan ekstensi png, jpg, jpeg, dan svg yang diperbolehkan.";
                exit();
            }

            // Validasi ukuran file
            if ($thumbnail_size > $max_file_size) {
                echo "Ukuran file thumbnail terlalu besar. Maksimum ukuran file yang diperbolehkan adalah 5 MB.";
                exit();
            }

            // Tentukan direktori penyimpanan thumbnail
            $upload_dir = '../../../database/uploads/';

            // Tentukan nama file thumbnail (tanpa path)
            $thumbnail_name_only = pathinfo($thumbnail_name, PATHINFO_FILENAME);

            // Tentukan ekstensi file thumbnail (tanpa path)
            $thumbnail_extension_only = pathinfo($thumbnail_name, PATHINFO_EXTENSION);

            // Enkripsi nama file thumbnail (contoh menggunakan timestamp)
            $encryptedFileName = time() . '_' . $thumbnail_name_only . '.' . $thumbnail_extension_only;

            // Tentukan path thumbnail dengan nama terenkripsi
            $thumbnail_path = $upload_dir . $encryptedFileName;

            // Pindahkan thumbnail ke direktori penyimpanan
            if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_path)) {
                echo "Terjadi kesalahan saat mengunggah thumbnail.";
                exit();
            }
        } else {
            // Jika thumbnail tidak diunggah, gunakan nilai default
            $thumbnail_path = 'album_default.jpg'; // Tentukan nilai default
        }

        // Hanya simpan nama file thumbnail (tanpa path) ke database
        $thumbnail_name_only = pathinfo($thumbnail_path, PATHINFO_BASENAME);

        // Query untuk menambahkan album baru dengan thumbnail
        $insertQuery = "INSERT INTO albums (userID, title, description, thumbnail_album) VALUES ($userID, '$title', '$description', '$thumbnail_name_only')";

        if (mysqli_query($conn, $insertQuery)) {
            // Jika berhasil, alihkan ke halaman album atau halaman lain yang sesuai
            header("Location: ./album.php");
            exit();
        } else {
            // Handle kesalahan query
            echo "Error: " . mysqli_error($conn);
            exit();
        }
    }

    // Ambil data album yang dibuat oleh pengguna yang sedang login
    $queryAlbums = "SELECT * FROM albums WHERE userID = $userID";
    $resultAlbums = mysqli_query($conn, $queryAlbums);
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
    exit();
}

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
                                <a href="../../page/index.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Search Box -->
                        <div class="hidden sm:block ">
                            <input type="text" placeholder="Search" class="bg-gray-700 text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                            <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                <!-- ... (kode ikon notifikasi) -->
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>
                            <div class="relative ml-3">
                                <div>
                                    <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <!-- ... (kode gambar profil) -->
                                        <span class="absolute -inset-1.5"></span>
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                    </button>
                                </div>
                                <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="./profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                                    <a href="./logout.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <input type="text" placeholder="Search" class="bg-gray-700 w-full mb-2 text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                    <a href="../../page/index.php" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page">Dashboard</a>
                    <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Upload<i class="baseline-add_shopping_cart"></i></a>
                    <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">My Album</a>
                </div>
            </div>
        </nav>

    </div>

    <div class="container p-10">
        <div class="relative">
            <button onclick="togglePopup()" id="albumPopupButton" class="text-white block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 absolute top-0 right-0 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Add Album
            </button>
        </div>
        <div class="relative">
            <h1 class="font-medium px-5 py-2.5 absolute top-0 left-0">My Album</h1>
        </div>

        <div id="albumPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Add Album</h2>
                <form id="albumForm" action="" method="post" enctype="multipart/form-data">
                    <div class="flex items-start mb-2">
                        <div class="w-48 h-64 overflow-hidden mr-4"> <!-- Memperbesar ukuran thumbnail -->
                            <img id="thumbnailPreview" src="../../../database/uploads/album_default.svg" alt="Thumbnail Preview" class="object-cover rounded-md w-full h-full">
                        </div>
                        <div class="flex-1">
                            <div class="mb-2">
                                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Title :</label>
                                <input type="text" name="title" id="title" autocomplete="title" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-2">
                                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description :</label>
                                <textarea id="description" name="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="thumbnail" class="block text-sm font-medium leading-6 text-gray-900">Thumbnail :</label>
                                <input type="file" name="thumbnail" id="thumbnail" accept="image/png, image/jpeg, image/jpg, image/svg+xml" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                            Submit
                        </button>
                        <button type="button" onclick="togglePopup()" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 focus:outline-none">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="w-full mx-auto flex flex-wrap gap-4 justify-center pt-16">
            <?php
            // Tampilkan album yang dibuat oleh pengguna
            if ($resultAlbums && mysqli_num_rows($resultAlbums) > 0) {
                while ($row = mysqli_fetch_assoc($resultAlbums)) {
                    $albumID = $row['albumID'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $thumbnail = $row['thumbnail_album'];
            ?>
                    <a href="./album_detail.php?albumID=<?= $albumID ?>" class="bg-gray-800 w-64 flex flex-col items-center justify-center text-white font-semibold rounded-md p-4 hover:bg-gray-700 transition duration-300 ease-in-out">
                        <div class="h-52 rounded-md w-full overflow-hidden">
                            <img class="object-cover w-full h-full" src="../../../database/uploads/<?= $thumbnail; ?>" alt="<?= $thumbnail; ?>">
                        </div>
                        <div class="mt-4 mb-2">
                            <i class="fa-solid fa-folder"></i>
                            <?= $title ?>
                        </div>
                        <p class="text-center text-xs">
                            <?= $description ?>
                        </p>
                    </a>

            <?php
                }
            } else {
                echo "<p>Belum ada album yang dibuat.</p>";
            }
            ?>

        </div>
    </div>

    <!-- footer -->
    <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="../../page/index.php" aria-label="Go home" title="Company" class="inline-flex items-center">
                    <img src="../../assets/logo/logo-main.svg" class="h-10 w-auto" alt="Numérique Gallery">
                    <span class="ml-2 text-xl font-bold tracking-wide text-gray-800 uppercase">Numérique Gallery</span>
                </a>
                <div class="mt-6 lg:max-w-sm">
                    <p class="text-sm text-gray-800">
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.
                    </p>
                    <p class="mt-4 text-sm text-gray-800">
                        Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <p class="text-base font-bold tracking-wide text-gray-900">Contacts</p>
                <div class="flex">
                    <p class="mr-1 text-gray-800">Phone:</p>
                    <a href="#" aria-label="Our phone" title="Our phone" class="transition-colors duration-300 text-deep-purple-accent-400 hover:text-deep-purple-800">08xxxxxxxxxx</a>
                </div>
                <div class="flex">
                    <p class="mr-1 text-gray-800">Email:</p>
                    <a href="" aria-label="Our email" title="Our email" class="transition-colors duration-300 text-deep-purple-accent-400 hover:text-deep-purple-800">example@gmail.com</a>
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
                    Bacon ipsum dolor amet short ribs pig sausage prosciutto chicken spare ribs salami.
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
        // Function untuk menampilkan/menyembunyikan pop-up
        function togglePopup() {
            var popup = document.getElementById("albumPopup");
            var button = document.getElementById("albumPopupButton");
            if (popup.classList.contains("hidden")) {
                popup.classList.remove("hidden");
                button.innerHTML = "Close Album";
            } else {
                popup.classList.add("hidden");
                button.innerHTML = "Add Album";
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
    </script>
</body>

</html>