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
        $queryCheckLike = "SELECT * FROM likes WHERE userID = '$userID' AND photoID = '$photoID'";
        $resultCheckLike = mysqli_query($conn, $queryCheckLike);

        if (mysqli_num_rows($resultCheckLike) > 0) {
            $rowLike = mysqli_fetch_assoc($resultCheckLike);
            $currentType = $rowLike['type'];
            $newType = $_GET['type'];

            // Jika pengguna memilih ikon yang sama, hapus like dari database
            if ($currentType === $newType) {
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
                // Jika pengguna memilih ikon yang berbeda, update tipe ikon reaksi
                $queryUpdateLike = "UPDATE likes SET type = '$newType' WHERE userID = '$userID' AND photoID = '$photoID'";
                $resultUpdateLike = mysqli_query($conn, $queryUpdateLike);

                if ($resultUpdateLike) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    // Handle jika terjadi kesalahan saat memperbarui ikon reaksi
                    echo "Error: Failed to update reaction";
                }
            }
        } else {
            // Handle jika pengguna belum memberikan like sebelumnya
            echo "Error: Like not found";
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
}
