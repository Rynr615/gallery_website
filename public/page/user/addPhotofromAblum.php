<?php
include "../../../database/koneksi.php";

// Start session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ./login.php");
    exit();
}

// Variabel username sudah pasti terdefinisi jika sampai di sini
$username = $_SESSION['username'];

// Lakukan koneksi dan query untuk mendapatkan userID
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// Periksa apakah query berhasil dieksekusi
if ($result && mysqli_num_rows($result) > 0) {
    // Ambil data pengguna terbaru
    $pengguna = mysqli_fetch_assoc($result);
    $profile_photo = $pengguna['profile_photo'];
    $username = $pengguna['username'];
    $accesLevel = $pengguna['access_level'];

    // Dapatkan userID dari data pengguna
    $userID = $pengguna['userID'];

    // Proses pengiriman file
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check jika file ter-upload
        if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
            // Validasi ekstensi file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
            $fileExtension = strtolower(pathinfo($_FILES['file-upload']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                echo "Error: Only JPG, JPEG, PNG, and SVG files are allowed.";
                exit();
            }

            // Validasi ukuran file
            $maxFileSize = 50 * 1024 * 1024; // 50MB
            if ($_FILES['file-upload']['size'] > $maxFileSize) {
                echo "Error: Maximum file size allowed is 50MB.";
                exit();
            }

            // Dapatkan informasi file
            $fileName = $_FILES['file-upload']['name'];
            $fileTmpName = $_FILES['file-upload']['tmp_name'];

            // Generate nama file yang terenkripsi (contoh menggunakan timestamp)
            $encryptedFileName = time() . '_' . $fileName;

            // Tentukan direktori penyimpanan (gantilah sesuai kebutuhan Anda)
            $uploadDirectory = "../../../database/uploads/";

            // Pindahkan file ke direktori tujuan dengan nama terenkripsi
            move_uploaded_file($fileTmpName, $uploadDirectory . $encryptedFileName);

            // Insert data foto ke tabel photos
            $albumId = $_POST['albumID']; // Ambil id album dari input select, atau gunakan NULL jika tidak ada album yang dipilih

            $insertQuery = "INSERT INTO photos (userID, albumID, image_path) 
                VALUES ('$userID', $albumId, '$encryptedFileName')";

            if (mysqli_query($conn, $insertQuery)) {
                header("Location: ./dashboard.php");
                exit();
            } else {
                // Handle kesalahan query
                echo "Error: " . mysqli_error($conn);
                exit();
            }
        } else {
            // Handle kesalahan upload file
            echo "Error uploading file.";
            exit();
        }
    }
} else {
    // Handle kesalahan query
    echo "Error: " . mysqli_error($conn);
    exit();
}