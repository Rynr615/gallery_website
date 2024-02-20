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
    $commentID = $_POST["commentID"];
    $username = $_SESSION["username"];

    // Ambil userID berdasarkan username
    $queryUser = "SELECT userID FROM users WHERE username = '$username'";
    $resultUser = mysqli_query($conn, $queryUser);

    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
        $rowUser = mysqli_fetch_assoc($resultUser);
        $userID = $rowUser['userID'];

        $queryUpdateCommentUser = "SELECT userID FROM comments WHERE commentID = '$commentID'";
        $resultUpdateCommentUser = mysqli_query($conn, $queryUpdateCommentUser);

        if($resultUpdateCommentUser && mysqli_num_rows($resultUpdateCommentUser) > 0) {
            $rowUpdateCommentUser = mysqli_fetch_assoc($resultUpdateCommentUser);
            $commentUserID = $rowUpdateCommentUser['userID'];

            if($userID == $commentUserID) {
                // Lakukan pembaruan teks komentar dalam database
                $queryUpdateComment = "UPDATE comments SET commentText = '$commentText' WHERE commentID = '$commentID'";
                $resultUpdateComment = mysqli_query($conn, $queryUpdateComment);

                if ($resultUpdateComment) {
                    // Redirect kembali ke halaman yang sesuai setelah berhasil memperbarui komentar
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                    exit();
                } else {
                    // Handle kesalahan jika pembaruan komentar gagal
                    echo "Error: " . mysqli_error($conn);
                    exit();
                }
            } else {
                // Handle jika pengguna tidak memiliki izin untuk mengedit komentar
                echo "<script>alert('You do not have permission to edit this comment.');</script>";
                header("Location: {$_SERVER["HTTP_REFERER"]}");
                exit();
            }
        } else {
            // Handle jika komentar tidak ditemukan
            echo "<script>alert('Comment not found.');</script>";
            header("Location: {$_SERVER["HTTP_REFERER"]}");
            exit();
        }
    } else {
        // Handle jika data pengguna tidak ditemukan
        echo "Error: User not found";
        exit();
    }
} else {
    // Redirect jika bukan metode POST
    header("Location: ./dashboard.php");
    exit();
}
