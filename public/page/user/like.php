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

        if (mysqli_num_rows($resultLike) == 0) {
            // Jika pengguna belum memberikan like, tambahkan like ke database
            $createdAt = date("Y-m-d H:i:s");
            $type = $_GET['type'];
            $queryInsertLike = "INSERT INTO likes (photoID, userID, createdAt, type) VALUES ('$photoID', '$userID', '$createdAt', '$type')";
            $resultInsertLike = mysqli_query($conn, $queryInsertLike);

            if ($resultInsertLike) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                // Handle jika terjadi kesalahan saat menambahkan like
                echo "Error: Failed to like";
            }
        } else {
            // Handle jika pengguna sudah memberikan like sebelumnya
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
}