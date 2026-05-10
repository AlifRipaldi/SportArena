<?php
include '../config/connection.php';
if (isset($_POST['register'])) {
    $id_user = "USR" . rand(100, 999);
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $telp = $_POST['telepon'];
    $role = "User";

    $sql = "INSERT INTO user (ID_User, Nama, Email, Password, Nomor_telepon, Role) 
            VALUES ('$id_user', '$nama', '$email', '$pass', '$telp', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
    }
}
?>