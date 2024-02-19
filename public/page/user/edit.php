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
$resultUser = mysqli_query($conn, $query);

// Periksa apakah query berhasil dieksekusi
if ($resultUser && mysqli_num_rows($resultUser) > 0) {
    // Ambil data pengguna terbaru
    $pengguna = mysqli_fetch_assoc($resultUser);
    $profile_photo = $pengguna['profile_photo'];
    $username = $pengguna['username'];

    // Dapatkan userID dari data pengguna
    $userID = $pengguna['userID'];
    $accesLevel = $pengguna['access_level'];

    if (isset($_GET['photoID'])) {
        $photoID = mysqli_real_escape_string($conn, $_GET['photoID']);

        // Query untuk mendapatkan data foto berdasarkan photoID dan userID
        $queryPhoto = "SELECT * FROM photos WHERE photoID = $photoID AND userID = $userID";
        $resultPhoto = mysqli_query($conn, $queryPhoto);

        // Periksa apakah query berhasil dieksekusi
        if ($resultPhoto && mysqli_num_rows($resultPhoto) > 0) {
            $rowPhoto = mysqli_fetch_assoc($resultPhoto);
            $titleBeforeEdit = $rowPhoto['title'];
            $descriptionBeforeEdit = $rowPhoto['description'];
            $imagePathBeforeEdit = $rowPhoto['image_path'];
        } else {
            // Jika query gagal, atur nilai default atau redirect ke halaman lain
            header("Location: ../../page/index.php");
            exit();
        }
    } else {
        // Jika photoID tidak ada dalam parameter URL, redirect ke halaman lain
        header("Location: ../../page/index.php");
        exit();
    }

    // Proses pengeditan foto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $photoID = mysqli_real_escape_string($conn, $_POST['photoID']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Mengambil data foto sebelum diedit
        $queryBeforeEdit = "SELECT title, description, image_path FROM photos WHERE photoID = $photoID AND userID = $userID";
        $resultBeforeEdit = mysqli_query($conn, $queryBeforeEdit);

        if ($resultBeforeEdit && mysqli_num_rows($resultBeforeEdit) > 0) {
            $rowBeforeEdit = mysqli_fetch_assoc($resultBeforeEdit);
            $titleBeforeEdit = $rowBeforeEdit['title'];
            $descriptionBeforeEdit = $rowBeforeEdit['description'];
            $imagePathBeforeEdit = $rowBeforeEdit['image_path'];
        } else {
            // Jika query gagal, atur nilai default atau redirect ke halaman lain
            header("Location: ../../page/index.php");
            exit();
        }

        // Cek apakah ada file yang diupload
        if ($_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
            // Hapus foto lama jika ada
            $oldImagePath = "../../../database/uploads/" . $imagePathBeforeEdit;
            unlink($oldImagePath);

            // Proses upload file baru
            $fileName = $_FILES['file-upload']['name'];
            $fileTmpName = $_FILES['file-upload']['tmp_name'];
            $encryptedFileName = time() . '_' . $fileName;
            $uploadDirectory = "../../../database/uploads/";

            move_uploaded_file($fileTmpName, $uploadDirectory . $encryptedFileName);

            // Update data foto di tabel photos, termasuk penggantian gambar
            $updateQuery = "UPDATE photos SET title = '$title', description = '$description', image_path = '$encryptedFileName' WHERE photoID = $photoID AND userID = $userID";
        } else {
            // Update data foto di tabel photos tanpa penggantian gambar
            $updateQuery = "UPDATE photos SET title = '$title', description = '$description' WHERE photoID = $photoID AND userID = $userID";
        }

        if (mysqli_query($conn, $updateQuery)) {
            // Jika berhasil, alihkan ke halaman index.php atau halaman lain yang sesuai
            header("Location: ../../page/index.php");
            exit();
        } else {
            // Handle kesalahan query
            echo "Error: " . mysqli_error($conn);
            exit();
        }
    }
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
                                <a href="../../page/index.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                <?php if ($accesLevel === 'admin') : ?>
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
                                        <img class="h-8 w-8 rounded-full" src="../../../database/uploads/<?= $profile_photo ?>" alt="<?= $username ?> profile photo">
                                    </button>
                                </div>
                                <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="./profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                                    <a href="./setting_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
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

    <!-- main-content -->
    <div class="container">
        <div class="m-10">
            <div class="w-full text-center mt-10">
                <p class="font-semibold text-2xl">Upload Your Photo<i class="fa-solid fa-image ml-2"></i></p>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="photoID" value="<?php echo $photoID; ?>">
                <div class="flex items-center flex-col">
                    <!-- title -->
                    <div class="w-1/3 mt-5">
                        <label for="title" class="block text-sm font-medium leading-6 text-gray-900">New Title</label>
                        <div class="mt-2">
                            <input type="text" name="title" id="title" value="<?= $titleBeforeEdit ?>" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <!-- Upload image -->
                    <div class="w-1/3 mt-5">
                        <label for="cover-photo" class="block text-sm font-medium leading-6 text-gray-900">Photo</label>
                        <div class="mt-2 w-full flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                            <div class="text-center">
                                <label for="file-upload" class="relative w-full cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" onchange="previewImage(this)">
                                </label>
                                <p class="text-xs leading-5 text-gray-600">PNG, JPG, SVG up to 10MB</p>
                                <div class="mt-4 min-h-[250px]" id="image-preview">
                                    <?php
                                    // Tampilkan gambar sebelum diedit
                                    echo '<img src="../../../database/uploads/' . $imagePathBeforeEdit . '" class="rounded-md mt-2" alt="Image Before Edit">';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- description -->
                    <div class="w-1/3 mt-5">
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">New Description</label>
                        <div class="mt-2">
                            <textarea id="description" placeholder="<?= $descriptionBeforeEdit ?>" name="description" rows="5" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><?= $descriptionBeforeEdit ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-center gap-x-6">
                    <button type="submit" class="rounded-md bg-indigo-600 px-10 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- footer -->
    <div class="px-4 pt-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 border-t-2 mt-10">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <a href="../index.php" aria-label="Go home" title="Company" class="inline-flex items-center">
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
        function previewImage(input) {
            const preview = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('rounded-md', 'mt-2');
                    preview.innerHTML = '';
                    preview.appendChild(img);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>

</html>