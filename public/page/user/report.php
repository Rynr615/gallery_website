<?php

include '../../../database/koneksi.php';

session_start();

// Pastikan user telah login sebelum melanjutkan
if (!isset($_SESSION['username'])) {
    // Redirect atau tindakan lain sesuai kebutuhan
    header('Location: ./login.php');
}

// Ambil data pengguna dari session
$userID = $_SESSION['userID'];
$photoID = $_POST['photoID'];

// Pastikan file ini dipanggil dari form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report'])) {

    // Validasi input
    if (empty($_POST['reportType']) || empty($_POST['reason'])) {
        // Tambahkan pesan kesalahan jika ada isian yang kosong
        echo "<script>alert('Reason it should not be empty');</script>";
        echo "<script>window.location.href='./post.php?photoID={$photoID}';</script>";

        exit();
    } else {
        // Ambil nilai yang dikirimkan dari form
        $reportType = $_POST['reportType'];
        $reason = $_POST['reason'];
        $additionalInfo = isset($_POST['additionalInfo']) ? $_POST['additionalInfo'] : '';
        // $reportedBy sudah diambil dari session sebelumnya

        // Lakukan penginputan data ke dalam tabel reports
        $sql = "INSERT INTO reports (reportType, photoID, reason, additionalInfo, reportedBy) VALUES ('$reportType', '$photoID','$reason', '$additionalInfo', '$userID')";

        if (mysqli_query($conn, $sql)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Tutup koneksi
    mysqli_close($conn);
}
