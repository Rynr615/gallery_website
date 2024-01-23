<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'gallery_web';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
