<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "arena sport"; // Nama database sesuai file SQL Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>