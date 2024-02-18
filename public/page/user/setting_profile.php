<?php
include "../../../database/koneksi.php";
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ./login.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil data pengguna
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

$failUpdate = "";
$failUpdatePhoto = "";
$succesUpdate = "";
$succesUpdatePhoto = "";

// Periksa apakah query berhasil dieksekusi
if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $row = mysqli_fetch_assoc($result);
    $userID = $row['userID']; // Ambil userID
    $accesLevel = $row['access_level'];

    // Ambil jumlah postingan pengguna
    $query_postingan = "SELECT COUNT(*) AS jumlah_postingan FROM photos WHERE userID = '$userID'";
    $result_postingan = mysqli_query($conn, $query_postingan);
    $jumlah_postingan = 0;
    if ($result_postingan && mysqli_num_rows($result_postingan) > 0) {
        $row_postingan = mysqli_fetch_assoc($result_postingan);
        $jumlah_postingan = $row_postingan['jumlah_postingan'];
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Ambil jumlah album pengguna
    $query_album = "SELECT COUNT(*) AS jumlah_album FROM albums WHERE userID = '$userID'";
    $result_album = mysqli_query($conn, $query_album);
    $jumlah_album = 0;
    if ($result_album && mysqli_num_rows($result_album) > 0) {
        $row_album = mysqli_fetch_assoc($result_album);
        $jumlah_album = $row_album['jumlah_album'];
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle profile photo upload
        if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type'];

            // Tentukan ekstensi file yang diperbolehkan
            $allowedExtensions = array('png', 'jpg', 'jpeg', 'svg');

            // Tentukan ukuran maksimum file (dalam bytes)
            $maxFileSize = 5 * 1024 * 1024; // 5 MB

            // Ambil ekstensi file
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validasi ekstensi file
            if (!in_array($fileExt, $allowedExtensions)) {
                echo "Invalid file type. Allowed types: png, jpg, jpeg, svg.";
                exit();
            }

            // Validasi ukuran file
            if ($fileSize > $maxFileSize) {
                echo "File size exceeds 5MB limit.";
                exit();
            }

            // Generate unique file name
            $newFileName = time() . "profile_photo_" . $userID . "." . $fileExt;
            $fileDestination = "../../../database/uploads/" . $newFileName;

            // Upload file
            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                echo "Error uploading file.";
                exit();
            }

            // Update profile photo in database
            $queryUpdatePhoto = "UPDATE users SET profile_photo = '$newFileName' WHERE userID = '$userID'";
            $resultUpdatePhoto = mysqli_query($conn, $queryUpdatePhoto);
            if ($resultUpdatePhoto) {
                // Success message
                $succesUpdatePhoto = "Profile photo updated successfully.";
            } else {
                // Error message
                $failUpdatePhoto = "Error updating profile photo: " . mysqli_error($conn);
            }
        }

        // Handle other profile details update
        // Sanitize input to prevent SQL injection
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        if (isset($_POST['cancelButton'])) {
            $succesUpdate = "";
        } else {
            // Update profile details in database
            $queryUpdateDetails = "UPDATE users SET name = '$name', email = '$email' WHERE userID = '$userID'";
            $resultUpdateDetails = mysqli_query($conn, $queryUpdateDetails);
            if ($resultUpdateDetails) {
                // Success message
                // header("Location: ./setting_profile.php");
                $succesUpdate = "Profile updated successfully.";
            } else {
                // Error message
                $failUpdate = "Error updating profile";
            }
        }
    }
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
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
                                        <img class="h-8 w-8 rounded-full" src="../../../database/uploads/<?= $row['profile_photo'] ?>" alt="<?= $row['username'] ?> profile photo">
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
    <div class="container mx-auto p-10">
        <div class="relative flex flex-col w-full min-h-[320px] min-w-0 mb-6 break-words border border-dashed bg-clip-border rounded-2xl border-stone-200 bg-light/30 draggable">
            <!-- card body -->
            <div class="px-9 pt-9 flex-auto min-h-[70px] pb-0 bg-transparent">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="flex flex-wrap mb-6 xl:flex-nowrap">
                        <div class="mb-5 mr-5">
                            <div class="relative inline-block w-48 shrink-0 rounded-2xl">
                                <img id="profilePhotoPreview" class="flex shrink-0 mb-2 rounded-full w-[80px] h-[80px] lg:w-[160px] lg:h-[160px]" src="../../../database/uploads/<?= $row['profile_photo'] ?>" alt="<?= $row['username'] ?> profile photo" />
                                <button type="button" id="photoPopupButton" onclick="togglePopup()" class="cursor-pointer ml-5 rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">Select a photo</button>
                            </div>
                        </div>

                        <div id="photoPopup" class="fixed inset-0 z-10 overflow-y-auto hidden bg-black bg-opacity-50 justify-center items-center">
                            <div class="my-8 mx-auto p-4 bg-white w-full max-w-md rounded shadow-md">
                                <h2 class="text-xl font-semibold mb-2">Preview Profile Photo</h2>
                                <img id="profilePreview" class="border border-gray-400 ml-12 rounded-full  w-[160px] h-[160px] lg:w-[320px] lg:h-[320px] mb-4" src="../../../database/uploads/<?= $row['profile_photo'] ?>" alt="Profile Photo">
                                <div class="flex justify-center m-5 pr-2">
                                    <input id="profile_photo" name="profile_photo" type="file" hidden>
                                    <label for="profile_photo" class="cursor-pointer ml-5 bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">Select a photo</label>
                                </div>
                                <div class="flex justify-center ml-2">
                                    <button type="submit" name="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">Confirm</button>
                                    <button onclick="togglePopup()" name="cancelButton" class="text-gray-700 bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grow">
                            <div class="flex flex-wrap items-start justify-between mb-2">
                                <div class="flex flex-col">
                                    <div class="flex items-center mb-2">
                                        <div class="sm:col-span-4">
                                            <div class="mt-2">
                                                <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                                    <input type="text" value="<?= $username ?>" name="username" id="username" autocomplete="username" disabled class="block flex-1 border-0 bg-transparent w-64 py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap pr-2 font-medium">
                                        <div class="sm:col-span-4">
                                            <div class="mt-2">
                                                <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                                    <input type="text" value="<?= $row['name'] ?>" name="name" id="name" autocomplete="name" class="block flex-1 border-0 bg-transparent w-64 py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap pr-2 font-medium">
                                        <div class="sm:col-span-4">
                                            <div class="mt-2">
                                                <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                                    <input type="email" value="<?= $row['email'] ?>" name="email" id="email" autocomplete="email" class="block flex-1 border-0 bg-transparent w-64 py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-normal text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save Changes</button>
                                    </div>
                                    <div>
                                        <p class="flex items-center mb-2 mt-5 mr-5 text-secondary-dark hover:text-primary" href="javascript:void(0)">
                                            Joined since : <?= date('F j Y', strtotime($row['createdAt'])); ?>
                                        </p>
                                        <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
                                            <?php if ($failUpdate) : ?>
                                                <!-- Fail update -->
                                                <div id="failUpdate" class="p-4 mb-4 text-sm w-full text-red-800 rounded-lg bg-red-50 dark:text-red-400" role="alert">
                                                    <span class="font-medium"><?= $failUpdate ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($succesUpdate) : ?>
                                                <!-- Succes update -->
                                                <div id="succesUpdate" class="p-4 mb-4 text-sm w-full text-green-800 rounded-lg bg-green-50 dark:text-green-600" role="alert">
                                                    <span class="font-medium"><?= $succesUpdate ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($failUpdatePhoto) : ?>
                                                <!-- Fail update -->
                                                <div id="failUpdatePhoto" class="p-4 mb-4 text-sm w-full text-red-800 rounded-lg bg-red-50 dark:text-red-400" role="alert">
                                                    <span class="font-medium"><?= $failUpdatePhoto ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($succesUpdatePhoto) : ?>
                                                <!-- Succes update -->
                                                <div id="succesUpdatePhoto" class="p-4 mb-4 text-sm w-full text-green-800 rounded-lg bg-green-50 dark:text-green-600" role="alert">
                                                    <span class="font-medium"><?= $succesUpdatePhoto ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
        // function togglePhotoPopup() {
        //     var profilePhotoInput = document.getElementById('profile_photo');
        //     var profilePhotoPreview = document.getElementById('selectedPhotoPreview');
        //     var photoPopup = document.getElementById('photoPopup');

        //     // Jika popup sebelumnya tersembunyi, tampilkan popup dan preview foto
        //     if (photoPopup.classList.contains('hidden')) {
        //         profilePhotoInput.addEventListener('change', function(event) {
        //             var file = event.target.files[0];
        //             var reader = new FileReader();

        //             reader.onload = function(e) {
        //                 profilePhotoPreview.src = e.target.result;
        //             }

        //             reader.readAsDataURL(file);
        //         });

        //         photoPopup.classList.remove('hidden');
        //     } else {
        //         // Jika popup sebelumnya ditampilkan, sembunyikan popup dan batalkan pemilihan foto
        //         profilePhotoInput.value = ''; // Hapus file yang dipilih
        //         profilePhotoPreview.src = ''; // Hapus pratinjau gambar
        //         photoPopup.classList.add('hidden');
        //     }
        // }

        function togglePopup() {
            var popup = document.getElementById("photoPopup");
            var button = document.getElementById("photoPopupButton");
            if (popup.classList.contains("hidden")) {
                popup.classList.remove("hidden");
                button.innerHTML = "Close";
            } else {
                popup.classList.add("hidden");
                button.innerHTML = "Add";
            }
        }

        document.getElementById("profile_photo").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("profilePreview").src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById("profilePreview").src = "#";
            }
        });

        setTimeout(function() {
            var alertElement = document.getElementById('succesUpdate');
            var alertElement = document.getElementById('succesUpdatePhoto');
            var alertElement2 = document.getElementById('failUpdate');
            var alertElement2 = document.getElementById('failUpdatePhoto');
            alertElement.remove();
            alertElement.remove();
            alertElement.remove();
            alertElement.remove();
        }, 3000);

        function toggleDeletePopup() {
            var popup = document.getElementById("photoPopup");
            popup.classList.toggle("hidden");
        }
    </script>

</body>

</html>