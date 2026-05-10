<?php
// Memanggil koneksi database
include '../config/connection.php';
session_start();

// Proses Login
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Mencocokkan data dengan tabel 'user' dari database arena sport
    $query = mysqli_query($conn, "SELECT * FROM user WHERE Email='$email' AND Password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Menyimpan data user ke dalam session
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
    <style>
        /* Tambahan style khusus halaman login agar berada di tengah */
        body {
            background-color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-box h2 {
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
        .btn-login-submit {
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
        .btn-login-submit:hover {
            background-color: #27ae60;
        }
        .error-msg {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Login Arena<span>Sport</span></h2>
        
        <?php if(isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan Email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" name="login" class="btn-login-submit">MASUK</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php" style="color: #3498db; text-decoration: none;">Daftar di sini</a>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <a href="../index.php" style="font-size: 0.8rem; color: #7f8c8d; text-decoration: none;">&larr; Kembali ke Beranda</a>
        </div>
    </div>

</body>
</html>