<?php
include "../../../database/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deletePhoto'])) {
    $photoID = $_POST['photoID'];

    // Hapus terlebih dahulu data dari tabel terkait (likes dan comments)
    $deleteLikesQuery = "DELETE FROM likes WHERE photoID = $photoID";
    $deleteLikesResult = mysqli_query($conn, $deleteLikesQuery);

    // Hapus terlebih dahulu data dari tabel terkait (reports)
    $deleteLikesQuery = "DELETE FROM reports WHERE photoID = $photoID";
    $deleteLikesResult = mysqli_query($conn, $deleteLikesQuery);

    $deleteCommentsQuery = "DELETE FROM comments WHERE photoID = $photoID";
    $deleteCommentsResult = mysqli_query($conn, $deleteCommentsQuery);

    // Setelah menghapus data dari tabel terkait, hapus data dari tabel photos
    $deleteQuery = "DELETE FROM photos WHERE photoID = $photoID";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        // Hapus berhasil, tambahkan logika tambahan jika diperlukan
        header("location: ./dashboard.php");
    } else { 
        // Hapus gagal, tampilkan pesan kesalahan MySQL
        echo "Error: " . mysqli_error($conn);
    }
}
