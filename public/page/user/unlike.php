<?php

include "../../../database/koneksi.php";

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ./login.php");
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
            // Jika pengguna sudah memberikan like, hapus like tersebut dari database
            $queryDeleteLike = "DELETE FROM likes WHERE userID = '$userID' AND photoID = '$photoID'";
            $resultDeleteLike = mysqli_query($conn, $queryDeleteLike);

            if ($resultDeleteLike) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                // Handle jika terjadi kesalahan saat menghapus like
                echo "Error: Failed to unlike";
            }
        } else {
            // Handle jika pengguna belum memberikan like sebelumnya
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
}
