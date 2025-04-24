<?php
// koneksi.php - File konfigurasi koneksi database

// Konfigurasi koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "kuliah";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
