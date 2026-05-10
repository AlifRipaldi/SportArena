<?php
include '../config/connection.php';
$error = '';

if (isset($_POST['register'])) {
    $id_user = "USR" . rand(100, 999);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $telp = mysqli_real_escape_int($conn, $_POST['telepon']);
    $role = "User";

    $sql = "INSERT INTO user (ID_User, Nama, Email, Password, Nomor_telepon, Role) 
            VALUES ('$id_user', '$nama', '$email', '$pass', '$telp', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
        exit;
    } else {
        $error = 'Terjadi kesalahan saat mendaftar. Coba lagi.';
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
    <style>
        body {
            background-color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .register-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-register-submit {
            width: 100%;
            padding: 12px;
            background-color: #2ecc71;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-register-submit:hover {
            background-color: #27ae60;
        }
        .error-msg {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Daftar Arena<span>Sport</span></h2>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <div class="form-group">
                <label>Nomor Telepon</label>
                <input type="tel" name="telepon" placeholder="Masukkan nomor telepon" required>
            </div>
            <button type="submit" name="register" class="btn-register-submit">DAFTAR</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="login.php" style="color: #3498db; text-decoration: none;">Masuk di sini</a>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <a href="../index.php" style="font-size: 0.8rem; color: #7f8c8d; text-decoration: none;">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
