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

$albumID = $_POST['albumID'];

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
        $category = $_POST['category'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        if (isset($_FILES['file-upload']) && !empty($_FILES['file-upload']['name'])) {
            // Validasi ekstensi file
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
            foreach ($_FILES['file-upload']['name'] as $index => $fileName) {
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($fileExtension, $allowedExtensions)) {
                    echo "Error: Only JPG, JPEG, PNG, and SVG files are allowed.";
                    exit();
                }
            }

            // Validasi ukuran file
            $maxFileSize = 50 * 1024 * 1024; // 50MB
            foreach ($_FILES['file-upload']['size'] as $index => $fileSize) {
                if ($fileSize > $maxFileSize) {
                    echo "Error: Maximum file size allowed is 50MB.";
                    exit();
                }
            }

            // Tentukan direktori penyimpanan (gantilah sesuai kebutuhan Anda)
            $uploadDirectory = "../../../database/uploads/";

            foreach ($_FILES['file-upload']['tmp_name'] as $index => $fileTmpName) {
                // Generate nama file yang terenkripsi (contoh menggunakan timestamp)
                $encryptedFileName = time() . '_' . $_FILES['file-upload']['name'][$index];

                // Pindahkan file ke direktori tujuan dengan nama terenkripsi
                if (!move_uploaded_file($fileTmpName, $uploadDirectory . $encryptedFileName)) {
                    echo "Error uploading file.";
                    exit();
                }

                $insertQuery = "INSERT INTO photos (userID, albumID, title, description, image_path, category) 
                VALUES ('$userID', $albumID, '$title', '$description', '$encryptedFileName', '$category')";


                if (!mysqli_query($conn, $insertQuery)) {
                    // Handle kesalahan query
                    echo "Error: " . mysqli_error($conn);
                    exit();
                }
            }

            header("Location: ./dashboard.php");
            exit();
        }
    }
}
