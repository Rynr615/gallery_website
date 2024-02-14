<?php
include "../../../database/koneksi.php";

session_start();

// Periksa apakah sesi username sudah diset atau belum
if (!isset($_SESSION['username'])) {
    header("Location: ./login.php");
    exit();
}

// Ambil photoID dari parameter URL
$photoID = isset($_GET['photoID']) ? $_GET['photoID'] : null;

$username = $_SESSION["username"];

$queryUserID = "SELECT * FROM users WHERE username = '$username'";
$resultUser = mysqli_query($conn, $queryUserID);

$showButtons = false;
if ($resultUser && mysqli_num_rows($resultUser) > 0) {
    $rowUser = mysqli_fetch_assoc($resultUser);
    $userID = $rowUser["userID"];
    $profile_photo = $rowUser["profile_photo"];
    $username = $rowUser["username"];

    if ($photoID) {
        // Query untuk mendapatkan data foto
        $query = "SELECT photos.photoID, photos.userID, photos.title, photos.description, photos.image_path, photos.createdAt, users.name, users.username
        FROM photos
        INNER JOIN users ON photos.userID = users.userID
        WHERE photos.photoID = $photoID";


        $result = mysqli_query($conn, $query);

        // Periksa apakah query berhasil dieksekusi
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (isset($row['userID']) && $row['userID'] == $userID) {
                $showButtons = true;
            }

            // Ekstrak data dari hasil query
            $title = $row['title'];
            $description = $row['description'];
            $image_path = $row['image_path'];
            $createdAt = date('F j Y, g:i a', strtotime($row['createdAt']));
            $name = $row['name'];
            $username = $row['username'];
        } else {
            // Jika query gagal, atur nilai default
            $title = "Foto Tidak Ditemukan";
            $description = "Maaf, foto tidak ditemukan.";
            $image_path = ""; // Atur path gambar default atau tampilkan placeholder gambar
            $createdAt = "";
            $name = "";
            $username = "";
        }
    } else {
        // Jika photoID tidak ditemukan dalam parameter URL
        $title = "Foto Tidak Ditemukan";
        $description = "Maaf, foto tidak ditemukan.";
        $image_path = ""; // Atur path gambar default atau tampilkan placeholder gambar
        $createdAt = "";
        $name = "";
        $username = "";
    }
}


// Ambil jumlah like untuk foto
$queryLikes = "SELECT COUNT(*) AS totalLikes FROM likes WHERE photoID = $photoID";
$resultLikes = mysqli_query($conn, $queryLikes);

// Periksa apakah query berhasil dieksekusi
if ($resultLikes) {
    $rowLikes = mysqli_fetch_assoc($resultLikes);
    $totalLikes = $rowLikes['totalLikes'];
} else {
    // Jika query gagal, atur nilai default
    $totalLikes = 0;
}

$queryComments = "SELECT comments.commentText, comments.createdAt, users.username
                  FROM comments
                  INNER JOIN users ON comments.userID = users.userID
                  WHERE comments.photoID = $photoID
                  ORDER BY comments.createdAt DESC";

$resultComments = mysqli_query($conn, $queryComments);

$countComments = "SELECT COUNT(*) AS totalComments FROM comments WHERE photoID = $photoID";
$resultCommentsCount = mysqli_query($conn, $countComments);

if ($resultCommentsCount) {
    $rowComments = mysqli_fetch_array($resultCommentsCount);
    $totalComment = $rowComments["totalComments"];
} else {
    $totalComment = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addToAlbum'])) {
        $albumID = $_POST['albumID'];

        // Update kolom albumID pada tabel photos untuk menyimpan postingan ke album yang dipilih
        $updateQuery = "UPDATE photos SET albumID = $albumID WHERE photoID = $photoID";
        $resultUpdate = mysqli_query($conn, $updateQuery);

        if ($resultUpdate) {
            echo "<script>alert('Added to album successfully');</script>";
            // Redirect or do any other action after successfully adding to album
        } else {
            echo "<script>alert('Failed to add to album');</script>";
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
    <div class="container mx-auto ">
        <div class="m-10 border-gray-100 border shadow-md rounded-xl py-12 px-8">
            <div class="mx-auto flex justify-between ">
                <div class="">
                    <p class="font-semibold">
                        <span>Title : </span> <?= $title; ?>
                    </p>
                    <p class="pt-2 text-sm text-gray-400">
                        <span class="font-normal">Author : </span><?= $username ?>
                    </p>
                    <p class="pt-2">
                        <?= $description ?>
                    </p>
                </div>
                <div class="">
                    <p class="text-xs">
                        <span>Published on : </span><?= $createdAt ?>
                    </p>
                    <?php if (isset($showButtons) && $showButtons) : ?>
                        <!-- Tampilkan tombol hanya jika user yang sedang login adalah pemilik foto -->
                        <div class="mt-10 flex gap-4">
                            <a href="edit.php?photoID=<?= $photoID ?>" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Edit <i class="fa-solid fa-pen-to-square ml-2"></i></a>
                            <form method="post" action="delete.php">
                                <input type="hidden" name="photoID" value="<?= $photoID ?>">
                                <button type="submit" name="deletePhoto" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Hapus<i class="fa-solid fa-trash ml-2"></i></button>
                            </form>
                        </div>
                        <label for="album" class="block text-sm font-medium leading-6 text-gray-900">Add to album</label>
                        <div class="mt-2">
                            <form method="post" action="">
                                <select id="album" name="albumID" autocomplete="album-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <!-- Fetch and display user's albums as options -->
                                    <?php
                                    $queryUserAlbums = "SELECT * FROM albums WHERE userID = $userID";
                                    $resultUserAlbums = mysqli_query($conn, $queryUserAlbums);
                                    if ($resultUserAlbums && mysqli_num_rows($resultUserAlbums) > 0) {
                                        while ($rowAlbum = mysqli_fetch_assoc($resultUserAlbums)) {
                                            echo "<option value='" . $rowAlbum['albumID'] . "'>" . $rowAlbum['title'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No albums found</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="addToAlbum" class="text-white mt-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add to Album</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class='flex justify-center mt-5'>
                <img class="rounded-lg w-4/5" src="../../../database/uploads/<?= $image_path; ?>" alt="<?= $title; ?>">
            </div>
            <div class="flex justify-between mt-5">
                <!-- like -->
                <div class="m-5 border-gray-100 border shadow-md rounded-xl py-12 px-8 h-10 flex items-center">
                    <a href="like.php?photoID=<?= $photoID; ?>" type="submit" class="text-gray-800">
                        <i class="fa-solid fa-thumbs-up"></i> <?= $totalLikes ?> Likes
                    </a>
                </div>
                <div id="commentPopup" class="m-10 w-full border-gray-100 border shadow-md rounded-xl py-12 px-8 hidden bg-white">
                    <h2 class="text-lg font-semibold mb-2">Send a comment</h2>
                    <form method="post" action="comment.php">
                        <input type="hidden" name="photoID" value="<?= $photoID ?>">
                        <textarea name="commentText" placeholder="Tulis komentar Anda..." class="w-full h-24 p-2 border rounded-md mb-2"></textarea>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Send<i class="fa-solid fa-paper-plane ml-3"></i> </button>
                    </form>
                </div>
                <!-- comment -->
                <div class="m-5 border-gray-100 border shadow-md rounded-xl py-12 px-8 h-10 flex items-center">
                    <div class="">
                        <button class="text-gray-800" onclick="openCommentPopup()">
                            <i class="fa-regular fa-comment"></i> <?= $totalComment; ?> Comments
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <?php if ($resultComments && mysqli_num_rows($resultComments) > 0) { ?>
                    <div class="border-gray-100 border shadow-md rounded-xl py-12 px-8">
                        <?php while ($comment = mysqli_fetch_assoc($resultComments)) { ?>
                            <div class="mb-3 flex justify-between">
                                <div class="mb-3">
                                    <p class="font-bold mb-3"><?= htmlspecialchars($comment['username']) ?></p>
                                    <p><?= htmlspecialchars($comment['commentText']) ?></p>
                                </div>

                                <div class="">
                                    <p class="text-gray-600 text-xs">Comments on : <?= date("F j, Y, g:i a", strtotime($comment['createdAt'])) ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-600">No comments yet.</p>
                <?php } ?>
            </div>

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
        let open = true

        function openCommentPopup() {
            // Tampilkan popup komentar
            if (open) {
                document.getElementById('commentPopup').style.display = 'block';
                open = false
            } else {
                document.getElementById('commentPopup').style.display = 'none';
                open = true

            }
        }
    </script>

</body>

</html>