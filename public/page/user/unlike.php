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

        // Hapus like dari database
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
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
}
?>
