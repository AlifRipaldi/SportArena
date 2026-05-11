<?php
include '../config/connection.php';
$error = '';

if (isset($_POST['register'])) {
    $id_user = "USR" . rand(100, 999);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $telp = mysqli_real_escape_string($conn, $_POST['telepon']);
    $role = "User";

    $sql = "INSERT INTO user (ID_User, Nama, Email, Password, Nomor_telepon, Role) 
            VALUES ('$id_user', '$nama', '$email', '$pass', '$telp', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
        exit;
    } else {
        $error = 'Terjadi kesalahan saat mendaftar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Arena Sport</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <h2>Join <span>Sport</span></h2>
            <p>Buat akun untuk mulai berolahraga</p>

            <?php if ($error): ?>
                <div style="color: #ff7675; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="auth-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="auth-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Masukkan email" required>
                </div>
                <div class="auth-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="telepon" placeholder="Contoh: 08123456789" required>
                </div>
                <div class="auth-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Buat password" required>
                </div>
                <button type="submit" name="register" class="btn-auth-submit">DAFTAR SEKARANG</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="login.php">Masuk di sini</a><br><br>
                <a href="../index.php" style="color: rgba(255,255,255,0.6); font-weight: normal;">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>