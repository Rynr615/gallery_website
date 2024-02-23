<?php
include "../../../database/koneksi.php";

session_start();

if (!isset($_SESSION['username'])) {
    // Pengguna belum login
    header("Location: ./login.php");
    exit();
}

$username = $_SESSION['username'];

$queryUser  = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $queryUser);

if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $row = mysqli_fetch_assoc($result);
    $profile_photo = $row['profile_photo'];
    $username = $row['username']; // Inisialisasi variabel username
    $accesLevel = $row['access_level']; //
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Tangkap data yang dikirimkan melalui formulir komentar
    $photoID = $_POST["photoID"];
    $commentText = $_POST["commentText"];
    $commentID = $_POST["commentID"];
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

        $queryCommentUser = "SELECT userID FROM comments WHERE commentID = '$commentID'";
        $resultCommentUser = mysqli_query($conn, $queryCommentUser);

        if ($resultCommentUser && mysqli_num_rows($resultCommentUser) > 0) {
            $rowCommentUser = mysqli_fetch_assoc($resultCommentUser);
            $commentUserID = $rowCommentUser['userID'];

            // Periksa apakah pengguna yang saat ini masuk adalah pemilik komentar
            if ($userID == $commentUserID || $accesLevel === 'admin' || $accesLevel === 'super_admin') {
                // Lakukan penghapusan komentar
                $queryDeleteComment = "DELETE FROM comments WHERE commentID = '$commentID'";
                $resultDeleteComment = mysqli_query($conn, $queryDeleteComment);

                if ($resultDeleteComment) {
                    // Redirect kembali ke halaman yang sesuai setelah berhasil menghapus komentar
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                    exit();
                } else {
                    // Handle kesalahan jika penghapusan komentar gagal
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                // Handle jika pengguna tidak memiliki izin untuk menghapus komentar
                echo "<script>alert('You do not have permission to delete this comment.');</script>";
                header("Location: {$_SERVER["HTTP_REFERER"]}");
            }
        } else {
            // Handle jika komentar tidak ditemukan
            echo "<script>alert('Comment not found.');</script>";
            header("Location: {$_SERVER["HTTP_REFERER"]}");
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
