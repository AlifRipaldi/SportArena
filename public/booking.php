<?php
include '../config/connection.php';
session_start();

if (isset($_GET['id_jadwal']) && isset($_SESSION['id_user'])) {
    $id_booking = "BK" . rand(1000, 9999);
    $id_jadwal = $_GET['id_jadwal'];
    $id_user = $_SESSION['id_user'];
    $waktu = date("Y-m-d H:i:s");
    $harga = 150000; 

    $sql = "INSERT INTO booking (ID_Booking, ID_Jadwal, ID_User, Waktu_transaksi, Total_harga) 
            VALUES ('$id_booking', '$id_jadwal', '$id_user', '$waktu', '$harga')";

    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE jadwal SET Status = 'Booked' WHERE ID_Jadwal = '$id_jadwal'");
        echo "<script>alert('Booking Tersimpan!'); window.location='../index.php';</script>";
    }
}
?>