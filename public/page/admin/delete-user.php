<?php
include '../../../database/koneksi.php';

if(isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Query penghapusan data terkait pengguna
    $deleteQueries = "
        SET FOREIGN_KEY_CHECKS=0;
        DELETE FROM comments WHERE userID = $userID;
        DELETE FROM likes WHERE userID = $userID;
        DELETE FROM reports WHERE reportedBy = $userID OR reportedUser = $userID;
        DELETE FROM reports_album WHERE reportedBy = $userID OR reportedUser = $userID;
        DELETE FROM photos WHERE userID = $userID;
        DELETE FROM albums WHERE userID = $userID;
        SET FOREIGN_KEY_CHECKS=1;
        DELETE FROM users WHERE userID = $userID;
    ";

    // Jalankan query penghapusan data terkait pengguna
    if(mysqli_multi_query($conn, $deleteQueries)) {
        // Redirect kembali ke halaman manage-user.php setelah pengguna dan data terkaitnya dihapus
        header("Location: ./manage-user.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Redirect kembali ke halaman manage-user.php jika userID tidak tersedia atau tidak valid
    header("Location: ./manage-user.php");
    exit();
}

