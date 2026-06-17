<?php
session_start();
include '../config/connection.php';

$error = '';
$oldEmail = '';
$alreadyLoggedIn = false;
$userName = '';
$dashboardUrl = '../dashboard';

function table_exists($conn, $table)
{
    $safeTable = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$safeTable'");

    return $result && mysqli_num_rows($result) > 0;
}

function auth_user_table($conn)
{
    return table_exists($conn, 'users') ? 'users' : 'user';
}

function normalized_role($role)
{
    return strtolower(str_replace(array('_', '-'), ' ', trim((string) $role)));
}

function auth_type_for_role($role)
{
    $normalizedRole = normalized_role($role);

    if (in_array($normalizedRole, array('admin', 'administrator', 'superadmin'), true)) {
        return 'admin';
    }

    if (in_array($normalizedRole, array('pemilik', 'pemilik lapangan', 'owner', 'mitra'), true)) {
        return 'pemilik';
    }

    return 'user';
}

function find_account_by_email($conn, $email)
{
    $table = auth_user_table($conn);
    $sql = 'SELECT `ID_User` AS account_id, `Nama`, `Email`, `Password`, `Nomor_telepon`, `Role` '
        . 'FROM `' . $table . '` WHERE `Email` = ? LIMIT 1';
    $statement = mysqli_prepare($conn, $sql);

    if (!$statement) {
        return null;
    }

    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);
    $account = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($statement);

    if ($account) {
        $account['Role'] = trim((string) $account['Role']) !== '' ? $account['Role'] : 'customer';
        $account['auth_table'] = $table;
        $account['auth_type'] = auth_type_for_role($account['Role']);
    }

    return $account;
}

function password_is_valid($password, $storedPassword)
{
    $storedPassword = (string) $storedPassword;

    return password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);
}

function dashboard_url_for_role($role)
{
    $normalizedRole = normalized_role($role);

    if (in_array($normalizedRole, array('admin', 'administrator', 'superadmin'), true)) {
        return '../admin/dashboard';
    }

    if (in_array($normalizedRole, array('pemilik', 'pemilik lapangan', 'owner', 'mitra'), true)) {
        return '../pemilik/dashboard';
    }

    return '../dashboard';
}

function set_login_session($account)
{
    session_regenerate_id(true);

    $_SESSION['id_user'] = $account['account_id'];
    $_SESSION['nama_user'] = $account['Nama'];
    $_SESSION['email_user'] = $account['Email'];
    $_SESSION['telepon_user'] = isset($account['Nomor_telepon']) ? $account['Nomor_telepon'] : '';
    $_SESSION['role_user'] = $account['Role'];
    $_SESSION['auth_table'] = $account['auth_table'];
    $_SESSION['auth_type'] = $account['auth_type'];
    $_SESSION['nama'] = $account['Nama'];
    $_SESSION['role'] = $account['Role'];
    $_SESSION['user_id'] = $account['account_id'];

    if ($account['auth_type'] === 'admin') {
        $_SESSION['id_admin'] = $account['account_id'];
    }

    if ($account['auth_type'] === 'pemilik') {
        $_SESSION['id_pemilik'] = $account['account_id'];
    }
}

if (!empty($_SESSION['id_user'])) {
    $alreadyLoggedIn = true;
    $userName = isset($_SESSION['nama_user']) ? $_SESSION['nama_user'] : 'Pengguna';
    $dashboardUrl = dashboard_url_for_role(isset($_SESSION['role_user']) ? $_SESSION['role_user'] : 'User');
}

if (!$alreadyLoggedIn && isset($_POST['login'])) {
    $oldEmail = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $account = find_account_by_email($conn, $oldEmail);

    if ($account && password_is_valid($password, $account['Password'])) {
        if (trim((string) $account['account_id']) === '') {
            $error = 'Akun ditemukan, tetapi ID_User masih kosong. Lengkapi ID_User pada tabel users.';
        } else {
            set_login_session($account);

            header('Location: ' . dashboard_url_for_role($account['Role']));
            exit;
        }
    } else {
        $error = 'Email atau kata sandi tidak sesuai.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Arena Sport</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=40">
</head>
<body class="login-auth-page">
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <img class="logo-img" src="../assets/img/logo.png" alt="Logo Arena Sport">
                <div>
                    <h1>Selamat Datang!</h1>
                    <p>Masuk untuk melanjutkan permainan</p>
                </div>
            </div>

            <?php if ($alreadyLoggedIn): ?>
                <div class="success-message">
                    Anda sudah masuk sebagai <strong><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></strong>.
                </div>
                <div class="already-logged-in">
                    <a href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn-primary">Lanjut ke Dasbor <span aria-hidden="true">&#8594;</span></a>
                    <a href="logout.php" class="btn-google">Keluar</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form id="loginForm" action="" method="POST" class="login-form">
                    <div class="field-group">
                        <div class="field-input">
                            <span class="field-icon">&#9993;</span>
                            <div class="auth-input-copy">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" placeholder="Masukkan email Anda" value="<?php echo htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" autocapitalize="none" spellcheck="false" required>
                            </div>
                        </div>
                    </div>

                    <div class="field-group">
                        <div class="field-input">
                            <span class="field-icon">&#128274;</span>
                            <div class="auth-input-copy">
                                <label for="password">Kata Sandi</label>
                                <input id="password" type="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                            </div>
                            <button type="button" class="password-toggle" aria-label="Tampilkan atau sembunyikan kata sandi">&#128065;</button>
                        </div>
                    </div>

                    <div class="forgot-link">
                        <a href="#">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" name="login" class="btn-primary">Masuk <span aria-hidden="true">&#8594;</span></button>
                </form>
            <?php endif; ?>

            <div class="divider"><span>atau lanjutkan dengan</span></div>

            <a href="#" class="btn-google">
                <span class="google-logo">G</span>
                Lanjutkan dengan Google
            </a>

            <div class="register-note">
                Belum punya akun? <a href="register.php">Daftar</a>
            </div>

            <div class="back-home">
                <a href="../index.php">&larr; Kembali ke beranda</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');

        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.innerHTML = isPassword ? '&#128584;' : '&#128065;';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        }
    </script>
</body>
</html>
