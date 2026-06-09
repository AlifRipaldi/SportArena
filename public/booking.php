<?php
include '../config/connection.php';
session_start();

$id_jadwal = null;
if (isset($_GET['id_jadwal'])) {
    $id_jadwal = $_GET['id_jadwal'];
} elseif (isset($_GET['id'])) {
    $id_jadwal = $_GET['id'];
}

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

if ($id_jadwal) {
    $id_booking = "BK" . rand(1000, 9999);
    $id_user = $_SESSION['id_user'];
    $waktu = date("Y-m-d H:i:s");
    $harga = 150000;

    $stmt = mysqli_prepare($conn, "INSERT INTO booking (ID_Booking, ID_Jadwal, ID_User, Waktu_transaksi, Total_harga) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssssd', $id_booking, $id_jadwal, $id_user, $waktu, $harga);
        if (mysqli_stmt_execute($stmt)) {
            $safe_id = mysqli_real_escape_string($conn, $id_jadwal);
            mysqli_query($conn, "UPDATE jadwal SET Status = 'Booked' WHERE ID_Jadwal = '$safe_id'");
            echo "<script>alert('Booking Tersimpan!'); window.location='../index.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menyimpan booking. Silakan coba lagi.'); window.location='../index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Terjadi kesalahan server.'); window.location='../index.php';</script>";
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}
?>