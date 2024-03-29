<?php
include "../../../database/koneksi.php";

// Start session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ../index.php");
    exit();
}

// Variabel username sudah pasti terdefinisi jika sampai di sini
$username = $_SESSION['username'];

$photoID = $_GET['photoID'];

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
            header("Location: ./dashboard.php");
            exit();
        }
    } else {
        // Jika photoID tidak ada dalam parameter URL, redirect ke halaman lain
        header("Location: ./dashboard.php");
        exit();
    }

    // Proses pengeditan foto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $photoID = mysqli_real_escape_string($conn, $_POST['photoID']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $acces_level = mysqli_real_escape_string($conn, $_POST['acces_level']);

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
            header("Location: ./dashboard.php");
            exit();
        }

        // Cek apakah ada file yang diupload
        if ($_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
            // Validasi ekstensi file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
            $fileExtension = strtolower(pathinfo($_FILES['file-upload']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                echo "<script>alert('Error: Only JPG, JPEG, PNG, and SVG files are allowed.');</script>";
                echo "<script>window.location.href='./dashboard.php'</script>";
                exit();
            }

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
            $updateQuery = "UPDATE photos SET title = '$title', description = '$description', image_path = '$encryptedFileName', category = '$category', acces_level = '$acces_level' WHERE photoID = $photoID AND userID = $userID";
        } else {
            // Update data foto di tabel photos tanpa penggantian gambar
            $updateQuery = "UPDATE photos SET title = '$title', description = '$description', category = '$category', acces_level = '$acces_level' WHERE photoID = $photoID AND userID = $userID";
        }

        if (mysqli_query($conn, $updateQuery)) {
            // Jika berhasil, alihkan ke halaman index.php atau halaman lain yang sesuai
            header("Location: ./post.php?photoID={$photoID}");
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
                            <img class="h-8 hidden sm:block w-auto" src="../../assets/logo/logo-secondary.svg" alt="Numérique Gallery">
                            <a class="block sm:hidden text-gray-400 hover:text-white" href="./edit.php?photoID=<?= $photoID ?>"><i class="fa-regular fa-pen-to-square"></i> Edit Photo</a>
                        </div>
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a href="./dashboard.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="./uploads.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="./album.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                                <?php if ($accesLevel === 'admin' || $accesLevel === 'super_admin') : ?>
                                    <a href="../admin/manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage User</a>
                                    <a href="../admin/report/reportPhoto.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Report Photo</a>
                                    <a href="../admin/report/reportAlbum.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Report Album</a>
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
                            <button type="submit" class="text-white hidden sm:block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  h-8 w-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
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
                    <form action="./result_search.php" class="flex flex-row gap-2 mb-2" method="GET">
                        <input type="text" name="search" placeholder="Search" class="bg-gray-700 w-full text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                    </form>
                    <?php if ($accesLevel === 'admin' || $accesLevel === 'super_admin') : ?>
                        <a href="../admin/manage-user.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Manage User</a>
                        <a href="../admin/report/reportPhoto.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Report Photo</a>
                        <a href="../admin/report/reportAlbum.php" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Report Album</a>
                    <?php elseif ($accesLevel === 'user') : ?>
                        <!-- <a href="../admin/manage-user.php" hidden class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Manage User</a> -->
                    <?php endif; ?>
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

    <!-- main-content -->
    <div class="container">
        <div class="m-10">
            <div class="w-full text-center mt-10">
                <p class="font-semibold text-2xl">Upload Your Photo<i class="fa-solid fa-image ml-2"></i></p>
            </div>
            <form action="" method="post" enctype="multipart/form-data" class="w-full sm:w-1/3 mx-auto">
                <input type="hidden" name="photoID" value="<?php echo $photoID; ?>">
                <div class="flex items-center flex-col w-full">
                    <div class="w-full mt-5">
                        <div class="flex gap-2">
                            <!-- title -->
                            <div class="mt-5 w-full">
                                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Title</label>
                                <div class="mt-2">
                                    <input type="text" name="title" id="title" value="<?= $titleBeforeEdit ?>" required autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                            <div class="mt-5">
                                <div class="mx-auto w-full">
                                    <label for="category" class="block text-sm font-medium leading-6 text-gray-900">Category</label>
                                    <div class="mt-2">
                                        <select id="category" name="category" autocomplete="category-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option value="Food">Food</option>
                                            <option value="Nature">Nature</option>
                                            <option value="Anime">Anime</option>
                                            <option value="Game">Game</option>
                                            <option value="Comic">Comic</option>
                                            <option value="Sport">Sport</option>
                                            <option value="Music">Music</option>
                                            <option value="Idol">Idol</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <!-- album -->
                            <div class="mt-5 w-full">
                                <label for="album" class="block text-sm font-medium leading-6 text-gray-900">Add to Album</label>
                                <div class="mt-2">
                                    <select id="album" name="album" autocomplete="album-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <?php
                                        $userId = $userID;
                                        $albumQuery = "SELECT * FROM albums WHERE userID = '$userId'";
                                        $albumResult = mysqli_query($conn, $albumQuery);

                                        // Periksa apakah ada album yang tersedia
                                        if (mysqli_num_rows($albumResult) > 0) {
                                            // Tampilkan opsi album
                                            echo "<option value='0'></option>";
                                            while ($row = mysqli_fetch_assoc($albumResult)) {
                                                echo "<option value='" . $row['albumID'] . "'>" . $row['title'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value='0'>No Album Available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- acces_level -->
                            <div class="mt-5">
                                <div class="mx-auto w-full">
                                    <label for="acces_level" class="block text-sm font-medium leading-6 mb-2 text-gray-900">Set to :</label>
                                    <select id="acces_level" name="acces_level" autocomplete="acces_level-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="public">Public</option>
                                        <option value="private">Private</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Upload image -->
                    <div class="w-full mt-5">
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
                    <div class="w-full mt-5">
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

    <div class="block sm:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
            <a href="./dashboard.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-house w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Home</span>
            </a>
            <a href="./uploads.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-upload w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11.074 4 8.442.408A.95.95 0 0 0 7.014.254L2.926 4h8.148ZM9 13v-1a4 4 0 0 1 4-4h6V6a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h17a1 1 0 0 0 1-1v-2h-6a4 4 0 0 1-4-4Z" />
                    <path d="M19 10h-6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1Zm-4.5 3.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM12.62 4h2.78L12.539.41a1.086 1.086 0 1 0-1.7 1.352L12.62 4Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Uploads</span>
            </a>
            <a href="./album.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-folder w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">My Album</span>
            </a>
            <a href="./profile.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <img src="../../../database/uploads/<?= $profile_photo ?>" alt="" class="h-8 w-8 rounded-full">
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500"><?= $username ?></span>
            </a>
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

        function toggleSignOutPopup() {
            var popup = document.getElementById("signOutPopup");
            popup.classList.toggle("hidden");
        }
    </script>

</body>

</html>