<?php

include "../../../database/koneksi.php";

// Pastikan tombol submit telah ditekan
if (isset($_POST['submit'])) {
    // Ambil data yang dikirimkan melalui formulir
    $userId = $_POST['userID'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['access_level'];

    // Query untuk mengupdate data pengguna
    $queryUpdate = "UPDATE users SET username='$username', name='$name', email='$email', access_level='$role' WHERE userID='$userId'";
    
    // Jalankan query
    $resultUpdate = mysqli_query($conn, $queryUpdate);

    if ($resultUpdate) {
        // Data berhasil diperbarui
        header("Location: ./manage-user.php");
        exit();
    } else {
        // Gagal mengupdate data
        echo "Error: " . mysqli_error($conn);
    }
}
