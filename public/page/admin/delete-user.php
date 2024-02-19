<?php
include '../../../database/koneksi.php';

if(isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Query untuk menghapus foto-foto pengguna
    $deletePhotosQuery = "DELETE FROM photos WHERE userID = $userID";
    mysqli_query($conn, $deletePhotosQuery);

    // Query untuk menghapus album-album pengguna
    $deleteAlbumsQuery = "DELETE FROM albums WHERE userID = $userID";
    mysqli_query($conn, $deleteAlbumsQuery);

    // Query untuk menghapus komentar-komentar pengguna
    $deleteCommentsQuery = "DELETE FROM comments WHERE userID = $userID";
    mysqli_query($conn, $deleteCommentsQuery);

    // Query untuk menghapus like-like pengguna
    $deleteLikesQuery = "DELETE FROM likes WHERE userID = $userID";
    mysqli_query($conn, $deleteLikesQuery);

    // Query untuk menghapus pengguna
    $deleteUserQuery = "DELETE FROM users WHERE userID = $userID";
    mysqli_query($conn, $deleteUserQuery);

    // Redirect kembali ke halaman album_detail.php setelah pengguna dan data terkaitnya dihapus
    header("Location: ./manage-user.php");
    exit();
} else {
    // Redirect kembali ke halaman album_detail.php jika userID tidak tersedia atau tidak valid
    header("Location: ./manage-user.php");
    exit();
}

