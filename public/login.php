<?php
include '../config/connection.php';
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM user WHERE Email='$email' AND Password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['status'] = "login";
        $_SESSION['id_user'] = $data['ID_User'];
        $_SESSION['nama_user'] = $data['Nama'];
        echo "<script>alert('Selamat Datang, " . $data['Nama'] . "!'); window.location='../index.php';</script>";
    } else {
        $error = "Email atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Arena Sport</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-box">
            <h2>Arena<span>Sport</span></h2>
            <p>Silahkan masuk untuk memesan lapangan</p>
            
            <?php if(isset($error)): ?>
                <div style="color: #ff7675; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="auth-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="auth-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan Password" required>
                </div>
                <button type="submit" name="login" class="btn-auth-submit">MASUK</button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="register.php">Daftar Sekarang</a><br><br>
                <a href="../index.php" style="color: rgba(255,255,255,0.6); font-weight: normal;">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>