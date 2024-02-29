<?php

include "../../../database/koneksi.php";

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
} else {
    $photoID = $_GET['photoID'];
    $username = $_SESSION["username"];

    // Ambil data pengguna berdasarkan username
    $queryUser = "SELECT userID FROM users WHERE username = '$username'";
    $resultUser = mysqli_query($conn, $queryUser);

    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
        $rowUser = mysqli_fetch_assoc($resultUser);
        $userID = $rowUser['userID'];

        // Periksa apakah pengguna sudah memberikan like sebelumnya
        $queryLike = "SELECT * FROM likes WHERE userID = '$userID' AND photoID = '$photoID'";
        $resultLike = mysqli_query($conn, $queryLike);

        if (mysqli_num_rows($resultLike) == 1) {
            // Jika pengguna sudah memberikan like, berikan opsi untuk membatalkan like
            echo '<a href="unlike.php?photoID=' . $photoID . '" type="submit" class="text-gray-800"><i class="fa-solid fa-thumbs-up text-blue-500"></i> Unlike</a>';
        } else {
            $createdAt = date("Y-m-d H:i:s");
            // Perbaikan pada kueri INSERT
            mysqli_query($conn, "INSERT INTO likes (photoID, userID, createdAt) VALUES ('$photoID', '$userID', '$createdAt')");
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
}
