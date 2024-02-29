<?php

include '../../../../database/koneksi.php';

session_start();

// Pastikan user telah login sebelum melanjutkan
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ../../index.php");
    exit();
}

// Ambil data pengguna dari session
$userID = $_SESSION['userID'];
$albumID = $_POST['albumID'];
$reportedUserID = $_POST['reportedUser'];

// Pastikan file ini dipanggil dari form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report'])) {

    // Validasi input
    if (empty($_POST['reportType']) || empty($_POST['reason'])) {
        // Tambahkan pesan kesalahan jika ada isian yang kosong
        echo "<script>alert('Reason it should not be empty');</script>";
        echo "<script>window.location.href='./photoAlbum.php?albumID={$albumID}';</script>";

        exit();
    } else {
        // Ambil nilai yang dikirimkan dari form
        $reportType = $_POST['reportType'];
        $reason = $_POST['reason'];
        $additionalInfo = isset($_POST['additionalInfo']) ? $_POST['additionalInfo'] : '';
        // $reportedBy sudah diambil dari session sebelumnya

        // Lakukan penginputan data ke dalam tabel reports
        $sql = "INSERT INTO reports_album (reportType, albumID, reason, additionalInfo, reportedBy, reportedUser) VALUES ('$reportType', '$albumID','$reason', '$additionalInfo', '$userID', '$reportedUserID')";

        if (mysqli_query($conn, $sql)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Tutup koneksi
    mysqli_close($conn);
} else {
    echo "<script>window.location.href='./photoAlbum.php?albumID={$albumID}';</script>";
}
