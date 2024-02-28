<?php

include '../../../../database/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil email dari query string
    $email = $_GET['email'];

    // Ambil password baru dan konfirmasi password dari form
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Periksa apakah password dan konfirmasi password cocok
    if ($new_password === $confirm_password) {
        // Hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password baru ke dalam database
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";

        if ($conn->query($sql) === TRUE) {
            // Redirect pengguna ke halaman login atau halaman lainnya setelah berhasil mengatur password baru
            header('location: ../login.php');
        } else {
            // Tampilkan pesan kesalahan jika gagal mengupdate password
            echo "Gagal mengupdate password";
        }
    } else {
        // Password dan konfirmasi password tidak cocok
        echo "Password dan konfirmasi password tidak cocok";
    }
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../../../css/output.css">
    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="stylesheet" href="../../../css/all.min.css">
    <link rel="stylesheet" href="../../../css/fontawesome.min.css">
    <link rel="icon" href="../../../assets/logo/logo-main.svg" type="image/x-icon">

</head>

<body class="h-full font-poppins">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-24 w-auto" src="../../../assets/logo/logo-main.svg" alt="">
            <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-500">Numérique Gallery</h2>
            <h2 class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Reset Password</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="" method="POST">
                <input type="hidden" name="email" value="<?= $_GET['email'] ?>">
                <div>
                    <label for="new_password" class="block text-sm font-medium leading-6 text-gray-900">Set New Password</label>
                    <div class="mt-2">
                        <input id="new_password" name="new_password" type="password" autocomplete="new_password" required required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                    <div class="mt-2">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="confirm_password" required required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit" name="submit" class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Send</button>
                </div>

            </form>
        </div>
    </div>

</body>

</html>