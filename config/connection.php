<?php
$database = require __DIR__ . '/database.php';

$conn = mysqli_connect(
    $database['host'],
    $database['username'],
    $database['password'],
    $database['database']
);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

if (isset($database['charset'])) {
    mysqli_set_charset($conn, $database['charset']);
}
