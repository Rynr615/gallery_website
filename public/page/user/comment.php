<?php
include "../../../database/koneksi.php";

session_start();

if (!isset($_SESSION['username'])) {
    // Pengguna belum login
    header("Location: ./login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Tangkap data yang dikirimkan melalui formulir komentar
    $photoID = $_POST["photoID"];
    $commentText = $_POST["commentText"];
    $username = $_SESSION["username"];

    // Ambil userID berdasarkan username
    $queryUser = "SELECT userID FROM users WHERE username = '$username'";
    $resultUser = mysqli_query($conn, $queryUser);

    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
        $rowUser = mysqli_fetch_assoc($resultUser);
        $userID = $rowUser['userID'];

        // Lakukan penyisipan data komentar ke dalam tabel comments
        $queryInsertComment = "INSERT INTO comments (userID, photoID, commentText, createdAt) 
                               VALUES ('$userID', '$photoID', '$commentText', CURRENT_TIMESTAMP)";
        $resultInsertComment = mysqli_query($conn, $queryInsertComment);

        if ($resultInsertComment) {
            // Redirect kembali ke halaman post setelah berhasil menambahkan komentar
            header("Location: ./post.php?photoID=$photoID");
            exit();
        } else {
            // Handle jika terjadi kesalahan saat menyisipkan komentar
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
    }
} else {
    // Redirect jika bukan metode POST
    header("Location: ../index.php");
    exit();
}

