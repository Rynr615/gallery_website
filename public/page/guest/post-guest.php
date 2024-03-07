<?php
include "../../../database/koneksi.php";

// Ambil photoID dari parameter URL
$photoID = isset($_GET['photoID']) ? $_GET['photoID'] : null;

// Query untuk mendapatkan data foto
$query = "SELECT photos.photoID, photos.userID, photos.title, photos.description, photos.category, photos.image_path, photos.createdAt, users.name, users.username, users.profile_photo
        FROM photos
        INNER JOIN users ON photos.userID = users.userID
        WHERE photos.photoID = $photoID";


$result = mysqli_query($conn, $query);

// Periksa apakah query berhasil dieksekusi
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Ekstrak data dari hasil query
    $title = $row['title'];
    $description = $row['description'];
    $image_path = $row['image_path'];
    $createdAt = date('F j Y, g:i a', strtotime($row['createdAt']));
    $name = $row['name'];
    $username = $row['username'];
    $profile_photo = $row['profile_photo'];
    $category = $row['category'];
} else {
    // Jika query gagal, atur nilai default
    $title = "Foto Tidak Ditemukan";
    $description = "Maaf, foto tidak ditemukan.";
    $image_path = ""; // Atur path gambar default atau tampilkan placeholder gambar
    $createdAt = "";
    $name = "";
    $username = "";
}

// Query untuk memeriksa apakah ada komentar yang terkait dengan foto
$queryShowButton = "SELECT * FROM comments WHERE photoID = $photoID";
$resultShowButton = mysqli_query($conn, $queryShowButton);

$showButtonsComment = $resultShowButton && mysqli_num_rows($resultShowButton) > 0;

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

$queryComments = "SELECT comments.commentText, comments.createdAt, users.username, comments.userID, comments.commentID, users.userID, users.profile_photo
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

$queryCheckLike = "SELECT * FROM likes WHERE photoID = '$photoID'";
$resultCheckLike = mysqli_query($conn, $queryCheckLike);

$userHasLiked = mysqli_num_rows($resultCheckLike) > 0;

if ($queryCheckLike && mysqli_num_rows($resultCheckLike) > 0) {
    while ($row = mysqli_fetch_array($resultCheckLike)) {
        $type = $row['type'];
    }
};

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
                                <a href="../index.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
                                <a href="../user/register.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Upload</a>
                                <a href="../user/register.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">My Album</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <form action="../guest/result-search_guest.php" class="flex flex-row gap-2" method="GET">
                            <div class="hidden sm:block">
                                <input type="text" name="search" placeholder="Search" class="bg-gray-700 text-white px-4 py-3 h-8 rounded-md text-xs focus:outline-none focus:shadow-outline">
                            </div>
                            <button type="submit" class="text-white hidden sm:block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  h-8 w-8 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                        </form>
                    </div>

                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                        <div class="relative ml-3">
                            <div>
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <!-- ... (kode gambar profil) -->
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full" src="../../../database/uploads/default_profile.svg" alt="">
                                </button>
                            </div>
                            <div x-show="profileMenuOpen" @click.away="profileMenuOpen = false" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="../user/register.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Register</a>
                                <a href="../user/login.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sm:hidden" id="mobile-menu" x-show="open" @click.away="open = false">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <form action="../guest/result-search_guest.php" class="flex gap-2 mb-2" method="GET">
                        <input type="text" name="search" placeholder="Search" class="bg-gray-700 w-full text-white px-3 py-2 rounded-md focus:outline-none focus:shadow-outline">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm  px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"><i class="fa-solid fa-magnifying-glass text-xs mx-auto"></i></button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <!-- main-content -->
    <div class="container mx-auto ">
        <div class="border-gray-100 border rounded-xl py-12 px-8 m-5">
            <div class="flex justify-between ">
                <div class="flex items-center">
                    <img src="../../../database/uploads/<?= $profile_photo ?>" alt="" class="w-10 mr-2 rounded-full">
                    <div>
                        <p class="font-semibold">
                            <?= $title; ?>
                        </p>
                        <p class="text-sm text-gray-400">
                            <?= $username ?>
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <p class="text-xs">
                        <span>Published on : </span><?= $createdAt ?>
                    </p>
                </div>

            </div>
            <p class="pt-2">
                <?= $description ?>
            </p>
            <!-- category -->
            <div class="mt-5">
                <p>
                    <?php if ($category === 'Anime') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-600">
                                <i class="fas fa-tv mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Food') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-orange-50 px-4 py-2 text-xs font-semibold text-orange-600">
                                <i class="fas fa-utensils mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Nature') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-green-50 px-4 py-2 text-xs font-semibold text-green-600">
                                <i class="fas fa-tree mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Game') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-violet-50 px-4 py-2 text-xs font-semibold text-violet-600">
                                <i class="fas fa-gamepad mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Comic') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600">
                                <i class="fas fa-book mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Sport') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600">
                                <i class="fas fa-football-ball mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Music') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-cyan-50 px-4 py-2 text-xs font-semibold text-cyan-600">
                                <i class="fas fa-music mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php elseif ($category === 'Idol') : ?>
                        <a href="./category-guest.php?category=<?= $category ?>">
                            <span class="rounded-full bg-purple-50 px-4 py-2 text-xs font-semibold text-purple-600">
                                <i class="fas fa-star mr-1"></i> <?= $category ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </p>

            </div>
            <div class='flex justify-center mt-5'>
                <img class="rounded-lg w-full" src="../../../database/uploads/<?= $image_path; ?>" alt="<?= $title; ?>">
            </div>
            <div class="flex justify-between gap-2">
                <!-- before like -->
                <div class="mt-2 py-8 px-8 h-10">
                    <div class="flex gap-2">
                        <a href="../user/register.php" name="type" value="like" class="text-blue-500 text-xl">
                            <i class="fa-regular fa-thumbs-up"></i>
                        </a href="../user/register.php">
                        <a href="../user/register.php" name="type" value="love" class="text-red-500 text-xl">
                            <i class="fa-regular fa-heart"></i>
                        </a href="../user/register.php">
                        <a href="../user/register.php" name="type" value="cry" class="text-orange-500 text-xl">
                            <i class="fa-regular fa-face-sad-cry"></i>
                        </a href="../user/register.php">
                        <a href="../user/register.php" name="type" value="lol" class="text-yellow-500 text-xl">
                            <i class="fa-regular fa-face-grin-squint-tears"></i>
                        </a href="../user/register.php">
                        <a href="../user/register.php" name="type" value="shessh" class="text-blue-500 text-xl">
                            <i class="fa-regular fa-face-grimace"></i>
                        </a href="../user/register.php">
                        <a href="../user/register.php" name="type" value="angry" class="text-red-500 text-xl">
                            <i class="fa-regular fa-face-angry"></i>
                        </a href="../user/register.php">
                    </div>
                    <div class="text-center">
                        <span><?= $totalLikes ?> Reaction </span>
                    </div>
                </div>

                <!-- comment -->
                <div class="mt-2 py-8 px-8 h-10">
                    <div class="">
                        <a href="../user/register.php" class="text-gray-800">
                            <i class="fa-regular fa-comment"></i> <?= $totalComment; ?> Comments
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="m-5 overflow-y-auto max-h-80">
            <?php if ($resultComments && mysqli_num_rows($resultComments) > 0) { ?>
                <div class="border-gray-100 border shadow-md rounded-xl py-12 px-8">
                    <?php while ($comment = mysqli_fetch_assoc($resultComments)) { ?>
                        <div class="flex justify-between mb-3">
                            <div class="flex">
                                <div>
                                    <img src="../../../database/uploads/<?= $comment['profile_photo'] ?>" alt="" class="h-10 rounded-full">
                                </div>
                                <div class="ml-3 flex flex-col justify-between">
                                    <div class="">
                                        <p class="font-bold"><?= htmlspecialchars($comment['username']) ?></p>
                                        <p class="text-gray-600 text-xs"><?= date("F j, Y, g:i a", strtotime($comment['createdAt'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 mb-3">
                            <p><?= htmlspecialchars($comment['commentText']) ?></p>
                        </div>

                        <hr>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="text-gray-600 text-center">No comments yet.</p>
            <?php } ?>
        </div>

    </div>

    <div class="block sm:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-700 dark:border-gray-600">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
            <a href="../index.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-house w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Home</span>
            </a>
            <a href="../user/register.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-upload w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11.074 4 8.442.408A.95.95 0 0 0 7.014.254L2.926 4h8.148ZM9 13v-1a4 4 0 0 1 4-4h6V6a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h17a1 1 0 0 0 1-1v-2h-6a4 4 0 0 1-4-4Z" />
                    <path d="M19 10h-6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1Zm-4.5 3.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM12.62 4h2.78L12.539.41a1.086 1.086 0 1 0-1.7 1.352L12.62 4Z" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Uploads</span>
            </a>
            <a href="../user/register.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <i class="fa-solid fa-folder w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
                </i>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">My Album</span>
            </a>
            <a href="../user/register.php" type="button" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 dark:hover:bg-gray-800 group">
                <svg class="w-5 h-5 mb-2 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                </svg>
                <span class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-500">Profile</span>
            </a>
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

</body>

</html>