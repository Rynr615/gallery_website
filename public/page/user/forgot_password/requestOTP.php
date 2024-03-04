<?php

include "../../../../database/koneksi.php";

require "../../../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendResetPasswordEmail($email, $reset_code)
{
    // Kirim email menggunakan PHPMailer

    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ryanyanuarpradana@gmail.com';
        $mail->Password = 'xkrohdnqsnkmzhgr';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Siapkan email
        $mail->setFrom('ryanyanuarpradana@gmail.com', 'admin');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body = 'Code Verifikasi Password : ' . $reset_code;

        // Kirim email
        $mail->send();

        return true; // Email berhasil dikirim
    } catch (Exception $e) {
        // Email gagal dikirim, kembalikan false dan tampilkan pesan error
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil email dari form
    $email = $_POST['email'];

    // Periksa apakah email sudah ada di tabel user
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_query);

    if ($check_email_result->num_rows > 0) {
        // Email sudah ada di tabel user, lanjutkan proses reset password

        // Periksa apakah email sudah ada di tabel reset_password
        $check_reset_email_query = "SELECT * FROM reset_password WHERE email = '$email'";
        $check_reset_email_result = $conn->query($check_reset_email_query);

        if ($check_reset_email_result->num_rows > 0) {
            // Email sudah ada di tabel reset_password, update reset_code

            // Generate reset code
            $reset_code = '';

            // Menghasilkan 6 karakter acak
            for ($i = 0; $i < 6; $i++) {
                $reset_code .= rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            }

            // Update reset code di database
            $update_reset_code_query = "UPDATE reset_password SET reset_code = '$reset_code' WHERE email = '$email'";
            if ($conn->query($update_reset_code_query) === TRUE) {
                // Kirim email menggunakan fungsi sendResetPasswordEmail
                if (sendResetPasswordEmail($email, $reset_code)) {
                    // Redirect ke halaman reset password dengan mengirim email sebagai parameter
                    header('location: ./reset_password.php?email=' . urlencode($email));
                    $_SESSION['success'] = "Code Reset Password sudah dikirim";
                } else {
                    // Jika gagal mengirim email, tampilkan pesan error
                    $error_message = "Failed to send reset password email.";
                }
            } else {
                // Redirect atau tampilkan pesan error
                echo "Error updating reset code: " . $conn->error;
            }
        } else {
            // Email belum ada di tabel reset_password, tambahkan data baru

            // Generate reset code
            $reset_code = '';

            // Menghasilkan 6 karakter acak
            for ($i = 0; $i < 6; $i++) {
                $reset_code .= rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            }

            // Simpan reset code ke database
            $sql = "INSERT INTO reset_password (email, reset_code) VALUES ('$email', '$reset_code')";

            if ($conn->query($sql) === TRUE) {
                // Kirim email menggunakan fungsi sendResetPasswordEmail
                if (sendResetPasswordEmail($email, $reset_code)) {
                    // Redirect ke halaman reset password dengan mengirim email sebagai parameter
                    header('location: ./reset_password.php?email=' . urlencode($email));
                    $_SESSION['success'] = "Code Reset Password sudah dikirim";
                } else {
                    // Jika gagal mengirim email, tampilkan pesan error
                    $error_message = "Failed to send reset password email.";
                }
            } else {
                // Redirect atau tampilkan pesan error
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        // Email tidak terdaftar di tabel user
        $error_message = "Email ini tidak terdaftar.";
    }

    $conn->close();
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
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Address</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <?php if (isset($_SESSION['wrong'])) : ?>
                            <p class="text-sm text-red-500"><?php echo $_SESSION['wrong']; ?></p>
                        <?php endif; ?>
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