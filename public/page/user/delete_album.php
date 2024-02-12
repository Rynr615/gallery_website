<?php
include "../../../database/koneksi.php";

// Periksa apakah albumID telah diset dan merupakan angka
if(isset($_POST['albumID']) && is_numeric($_POST['albumID'])) {
    // Ambil albumID dari form
    $albumID = $_POST['albumID'];

    // Query untuk menghapus album berdasarkan albumID
    $query = "DELETE FROM albums WHERE albumID = $albumID";
    
    // Eksekusi query
    $result = mysqli_query($conn, $query);

    // Periksa apakah query berhasil dieksekusi
    if($result) {
        // Redirect kembali ke halaman album_detail.php dengan pesan sukses
        header("Location: ./album.php");
        exit();
    } else {
        // Redirect kembali ke halaman album_detail.php dengan pesan gagal
        header("Location: ./album.php");
        exit();
    }
} else {
    // Redirect kembali ke halaman album_detail.php jika albumID tidak tersedia atau tidak valid
    header("Location: album_detail.php");
    exit();
}

