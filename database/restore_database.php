<?php
// Konfigurasi database
$host = 'localhost'; // Hapus port jika tidak diperlukan
$port = '3306'; // Port default MySQL
$username = 'root';
$password = '';
$database = 'gallery_web';

// Folder untuk menyimpan backup
$backupFolder = './backup/';

// Cari file backup terbaru di dalam folder backup
$latestBackup = '';
$latestBackupTime = 0;

$files = glob($backupFolder . '*.sql');
foreach ($files as $file) {
    $fileTime = filemtime($file);
    if ($fileTime > $latestBackupTime) {
        $latestBackupTime = $fileTime;
        $latestBackup = $file;
    }
}

// Pastikan file backup terbaru ditemukan
if (empty($latestBackup)) {
    echo "Tidak ada file backup yang ditemukan.";
    exit();
}

// Eksekusi perintah untuk merestore backup
exec("C:/xampp/mysql/bin/mysql.exe --user={$username} --password={$password} --host={$host} --port={$port} {$database} < {$latestBackup}");

// Cek apakah restore berhasil
// Anda dapat menambahkan kode untuk memeriksa apakah perintah berhasil dieksekusi
echo "Restore database berhasil menggunakan file: {$latestBackup}";
