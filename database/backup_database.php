<?php
// Konfigurasi database
$host = 'localhost'; // Hapus port jika tidak diperlukan
$port = '3306'; // Port default MySQL
$username = 'root';
$password = '';
$database = 'gallery_web';

// Folder untuk menyimpan backup
$backupFolder = './backup/';

// Pastikan folder backup tersedia
if (!file_exists($backupFolder)) {
    mkdir($backupFolder, 0755, true);
}

// Nama file backup
$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

// Eksekusi perintah untuk membuat backup
exec("C:/xampp/mysql/bin/mysqldump.exe --user={$username} --password={$password} --host={$host} --port={$port} {$database} > {$backupFolder}{$backupFile}");

// Cek apakah backup berhasil dibuat
if (file_exists($backupFolder . $backupFile)) {
    echo "Backup database berhasil dibuat: {$backupFile}";
} else {
    echo "Gagal membuat backup database.";
}

