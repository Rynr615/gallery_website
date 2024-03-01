<?php

include '../../../../database/koneksi.php';

$reportID = $_GET['reportID'];

if (isset($_GET['reportID'])) {

    $deleteReportsAlbumQuery = "DELETE FROM reports_album WHERE reportID = $reportID";
    mysqli_query($conn, $deleteReportsAlbumQuery);

    // Redirect kembali ke halaman manage-user.php setelah pengguna dan data terkaitnya dihapus
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
