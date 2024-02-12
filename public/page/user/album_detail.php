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

// Ambil albumID dari parameter URL
$albumID = $_GET['albumID'];

// Default values for update status and message
$updateSuccess = false;
$updateMessage = "";

// Lakukan koneksi dan query untuk mendapatkan userID
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $pengguna = mysqli_fetch_assoc($result);

    // Dapatkan userID dari data pengguna
    $userID = $pengguna['userID'];

    // Ambil data foto dari album yang dipilih
    $queryPhotos = "SELECT photos.photoID, photos.title, photos.description, photos.image_path, photos.createdAt, users.username AS uploader 
    FROM photos
    INNER JOIN users ON photos.userID = users.userID 
    WHERE photos.albumID = $albumID AND photos.userID = $userID
    ORDER BY photos.createdAt DESC";
    $resultPhotos = mysqli_query($conn, $queryPhotos);

    $queryAlbums = "SELECT * FROM albums WHERE userID = $userID AND albumID = $albumID";
    $resultAlbums = mysqli_query($conn, $queryAlbums);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Ambil nilai dari form
        $title = $_POST['title'];
        $description = $_POST['description'];
        $thumbnailName = $_FILES['thumbnail']['name'];
        $thumbnailTmpName = $_FILES['thumbnail']['tmp_name'];
        $thumbnailSize = $_FILES['thumbnail']['size'];
        $thumbnailType = $_FILES['thumbnail']['type'];

        // Tentukan lokasi penyimpanan thumbnail baru
        $thumbnailDirectory = "../../../database/uploads/";
        $thumbnailPath = $thumbnailDirectory . $thumbnailName;

        // Jika thumbnail diupload
        if (!empty($thumbnailName)) {
            // Periksa ukuran gambar
            if ($thumbnailSize > 5 * 1024 * 1024) {
                echo "<script>alert('Ukuran gambar terlalu besar. Maksimal 5MB.');</script>";
            } elseif (!in_array($thumbnailType, ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'])) {
                echo "<script>alert('Tipe gambar tidak didukung. Hanya PNG, JPEG, JPG, dan SVG yang diperbolehkan.');</script>";
            } else {
                // Pindahkan thumbnail yang diupload ke folder penyimpanan baru
                move_uploaded_file($thumbnailTmpName, $thumbnailPath);

                // Perbarui data album di database
                $query = "UPDATE albums SET title='$title', description='$description', thumbnail_album='$thumbnailName' WHERE albumID='$albumID'";
                $result = mysqli_query($conn, $query);

                // Periksa apakah query berhasil dieksekusi
                if ($result) {
                    $updateMessage = "Data successfully updated";
                    $updateSuccess = true;
                } else {
                    $updateMessage = "Data fails to update";
                }
            }
        } else {
            // Jika thumbnail tidak diupload, perbarui hanya title dan description
            $query = "UPDATE albums SET title='$title', description='$description' WHERE albumID='$albumID'";
            $result = mysqli_query($conn, $query);

            // Periksa apakah query berhasil dieksekusi
            if ($result) {
                // echo "<script>alert('Data album berhasil diperbarui.');</script>";
                $updateMessage = "Data successfully updated";
                $updateSuccess = true;
            } else {
                // echo "<script>alert('Terjadi kesalahan saat memperbarui data album: " . mysqli_error($conn) . "');</script>";
                $updateMessage = "Data fails to update";
            }
        }

        // Tutup koneksi database
        mysqli_close($conn);
    }
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
        <div class="relative flex justify-end">
            <button onclick="togglePopup()" id="albumPopupButton" class="text-white block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Album
            </button>
            <button onclick="toggleDeletePopup()" id="deleteAlbumButton" class="text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800" onclick="return confirm('Are you sure you want to delete this album? This action cannot be undone.')">
                <i class="fa-solid fa-trash mr-2"></i> Delete Album
            </button>
        </div>

        <div id="deleteAlbumPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                <h2 class="text-xl font-semibold mb-2">Delete Album</h2>
                <p class="mb-4">Are you sure you want to delete this album? This action cannot be undone.</p>
                <div class="flex justify-center">
                    <form id="deleteAlbumForm" action="delete_album.php" method="post">
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

        <!-- Tampilkan pemberitahuan -->

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!$updateSuccess) {
        ?>
                <div class="p-4 mb-4 text-sm w-1/3 text-red-800 rounded-lg bg-red-50 dark:text-red-400" role="alert">
                    <span class="font-medium">Danger!!!</span> <?= $updateMessage ?>
                </div>
            <?php
            } else {
            ?>
                <div class="p-4 mb-4 text-sm w-1/3 text-green-800 rounded-lg bg-green-50 dark:text-green-600" role="alert">
                    <span class="font-medium">Success!!!</span> <?= $updateMessage ?>
                </div>
        <?php
            }
        }

        ?>

        <?php

        if ($resultAlbums && mysqli_num_rows($resultAlbums) > 0) {
            while ($row = mysqli_fetch_array($resultAlbums)) {
        ?>
                <div class="relative">
                    <p class="font-medium px-5 py-2.5 absolute top-0 left-0">
                        <span>Title: </span> <?= $row['title'] ?>
                    </p>
                </div>
                <div class="px-5 pt-10">
                    <p class="pt-2 text-xs">
                        <span>Created at : </span><?= $row['createdAt'] ?>
                    </p>
                    <p class="pt-2">
                        <?= $row['description'] ?>
                    </p>

                </div>

                <div id="albumPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
                    <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                        <h2 class="text-xl font-semibold mb-2">Edit Album</h2>
                        <form id="albumForm" action="" method="post" enctype="multipart/form-data">
                            <div class="flex items-start mb-2">
                                <div class="w-48 h-64 overflow-hidden mr-4"> <!-- Memperbesar ukuran thumbnail -->
                                    <img id="thumbnailPreview" src="../../../database/uploads/<?= $row['thumbnail_album'] ?>" alt="Thumbnail Preview" class="object-cover rounded-md w-full h-full">
                                </div>
                                <div class="flex-1">
                                    <div class="mb-2">
                                        <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Title :</label>
                                        <input type="text" name="title" id="title" value="<?= $row['title'] ?>" autocomplete="title" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div class="mb-2">
                                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description :</label>
                                        <textarea id="description" name="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= $row['description'] ?></textarea>
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
        <?php
            }
        }
        ?>

        <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 p-4 md:p-2 xl:p-5 m-5">
            <?php
            // Tampilkan setiap foto dari album yang dipilih
            if ($resultPhotos && mysqli_num_rows($resultPhotos) > 0) {
                while ($row = mysqli_fetch_assoc($resultPhotos)) {
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
                                <a href="./user/post.php?photoID=<?= $row['photoID'] ?>">
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
            } else {
                echo "<p>No photos found in this album.</p>";
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
                button.innerHTML = "<i class='fa-solid fa-pen-to-square mr-2'></i>Edit Album";
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
    </script>


</body>

</html>