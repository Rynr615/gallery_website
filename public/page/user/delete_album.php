<?php
include "../../../database/koneksi.php";

// Periksa apakah albumID telah diset dan merupakan angka
if(isset($_GET['albumID']) && is_numeric($_GET['albumID'])) {
    // Ambil albumID dari form
    $albumID = $_GET['albumID'];

    // Query untuk menghapus semua komentar yang terkait dengan foto yang akan dihapus
    $deleteCommentsQuery = "DELETE FROM comments WHERE photoID IN (SELECT photoID FROM photos WHERE albumID = $albumID)";
    $deleteCommentsResult = mysqli_query($conn, $deleteCommentsQuery);

    if (!$deleteCommentsResult) {
        printf("Error deleting comments: %s\n", mysqli_error($conn));
        exit();
    }

    // Query untuk menghapus semua like yang terkait dengan foto yang akan dihapus
    $deleteLikesQuery = "DELETE FROM likes WHERE photoID IN (SELECT photoID FROM photos WHERE albumID = $albumID)";
    $deleteLikesResult = mysqli_query($conn, $deleteLikesQuery);

    if (!$deleteLikesResult) {
        printf("Error deleting likes: %s\n", mysqli_error($conn));
        exit();
    }

    // Setelah menghapus semua komentar dan like yang terkait, Anda dapat menghapus semua foto yang terkait
    $deletePhotosQuery = "DELETE FROM photos WHERE albumID = $albumID";
    $deletePhotosResult = mysqli_query($conn, $deletePhotosQuery);

    if (!$deletePhotosResult) {
        printf("Error deleting photos: %s\n", mysqli_error($conn));
        exit();
    }

    // Terakhir, Anda dapat menghapus album itu sendiri
    $deleteAlbumQuery = "DELETE FROM albums WHERE albumID = $albumID";
    $deleteAlbumResult = mysqli_query($conn, $deleteAlbumQuery);

    if (!$deleteAlbumResult) {
        printf("Error deleting album: %s\n", mysqli_error($conn));
        exit();
    }

    // Redirect kembali ke halaman album.php dengan pesan sukses
    header("Location: album.php");
    exit();
} else {
    // Redirect kembali ke halaman album.php jika albumID tidak tersedia atau tidak valid
    header("Location: album.php");
    exit();
}
?>
