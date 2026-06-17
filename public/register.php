<?php
include '../config/connection.php';
$error = '';

function register_table_exists($conn, $table)
{
    $safeTable = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$safeTable'");

    return $result && mysqli_num_rows($result) > 0;
}

function register_user_table($conn)
{
    return register_table_exists($conn, 'users') ? 'users' : 'user';
}

if (isset($_POST['register'])) {
    $id_user = "USR" . rand(100, 999);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telp = trim($_POST['telepon']);
    $role = "customer";
    $table = register_user_table($conn);

    $sql = "INSERT INTO `$table` (ID_User, Nama, Email, Password, Nomor_telepon, Role) VALUES (?, ?, ?, ?, ?, ?)";
    $statement = mysqli_prepare($conn, $sql);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 'ssssss', $id_user, $nama, $email, $pass, $telp, $role);
    }

    if ($statement && mysqli_stmt_execute($statement)) {
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
    <link rel="stylesheet" href="../assets/css/style.css?v=45">
</head>
<body class="login-auth-page register-auth-page">
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <img class="logo-img" src="../assets/img/logo.png" alt="Logo Arena Sport">
                <div>
                    <h1>Buat Akun</h1>
                    <p>Daftar untuk mulai bermain</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form id="registerForm" action="" method="POST" class="login-form">
                <div class="field-group">
                    <div class="field-input">
                        <span class="field-icon">&#9786;</span>
                        <div class="auth-input-copy">
                            <label for="nama">Nama Lengkap</label>
                            <input id="nama" type="text" name="nama" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                        </div>
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-input">
                        <span class="field-icon">&#9993;</span>
                        <div class="auth-input-copy">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" placeholder="Masukkan email Anda" autocomplete="email" autocapitalize="none" spellcheck="false" required>
                        </div>
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-input">
                        <span class="field-icon">&#9742;</span>
                        <div class="auth-input-copy">
                            <label for="telepon">Nomor Telepon</label>
                            <input id="telepon" type="tel" name="telepon" placeholder="08123456789" autocomplete="tel" required maxlength="12" pattern="08[0-9]{9,10}" title="Nomor telepon harus dimulai dengan 08 dan 10-12 digit">
                        </div>
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-input">
                        <span class="field-icon">&#128274;</span>
                        <div class="auth-input-copy">
                            <label for="password">Kata Sandi</label>
                            <input id="password" type="password" name="password" placeholder="Buat kata sandi" autocomplete="new-password" required minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="Minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan karakter khusus">
                        </div>
                        <button type="button" class="password-toggle" aria-label="Tampilkan atau sembunyikan kata sandi">&#128065;</button>
                    </div>
                    <small class="password-help">Gunakan huruf besar, huruf kecil, angka, dan satu karakter khusus.</small>
                </div>

                <button type="submit" name="register" class="btn-primary">Daftar</button>
            </form>

            <div class="divider"><span>atau daftar dengan</span></div>

            <a href="#" class="btn-google">
                <span class="google-logo">G</span>
                Lanjutkan dengan Google
            </a>

            <div class="register-note">
                Sudah punya akun? <a href="login.php">Masuk</a>
            </div>

            <div class="back-home">
                <a href="../index.php">&larr; Kembali ke beranda</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');
        const registerForm = document.getElementById('registerForm');

        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.innerHTML = isPassword ? '&#128584;' : '&#128065;';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (event) => {
                const pattern = new RegExp('(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}');
                if (!pattern.test(passwordInput.value)) {
                    event.preventDefault();
                    passwordInput.setCustomValidity('Kata sandi harus berisi huruf besar, huruf kecil, angka, dan karakter khusus.');
                    passwordInput.reportValidity();
                } else {
                    passwordInput.setCustomValidity('');
                }
            });
        }
    </script>
</body>
</html>
