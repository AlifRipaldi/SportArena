<?php
session_start();
include '../config/connection.php';

$error = '';
$oldEmail = '';
$alreadyLoggedIn = false;
$userName = '';
$dashboardUrl = '../dashboard';

function account_sources()
{
    return array(
        array(
            'table' => 'admin',
            'id_column' => 'ID_Admin',
            'default_role' => 'Admin',
            'type' => 'admin',
        ),
        array(
            'table' => 'pemilik_lapangan',
            'id_column' => 'ID_Pemilik',
            'default_role' => 'Pemilik',
            'type' => 'pemilik',
        ),
        array(
            'table' => 'user',
            'id_column' => 'ID_User',
            'default_role' => 'User',
            'type' => 'user',
        ),
    );
}

function find_account_by_email($conn, $email)
{
    foreach (account_sources() as $source) {
        $sql = 'SELECT `' . $source['id_column'] . '` AS account_id, `Nama`, `Email`, `Password`, `Nomor_telepon`, `Role` '
            . 'FROM `' . $source['table'] . '` WHERE `Email` = ? LIMIT 1';
        $statement = mysqli_prepare($conn, $sql);

        if (!$statement) {
            continue;
        }

        mysqli_stmt_bind_param($statement, 's', $email);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $account = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        if ($account) {
            $account['Role'] = trim((string) $account['Role']) !== '' ? $account['Role'] : $source['default_role'];
            $account['auth_table'] = $source['table'];
            $account['auth_type'] = $source['type'];

            return $account;
        }
    }

    return null;
}

function password_is_valid($password, $storedPassword)
{
    $storedPassword = (string) $storedPassword;

    return password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);
}

function dashboard_url_for_role($role)
{
    $normalizedRole = strtolower(trim((string) $role));

    if (in_array($normalizedRole, array('admin', 'administrator', 'superadmin'), true)) {
        return '../admin/dashboard';
    }

    if (in_array($normalizedRole, array('pemilik', 'owner', 'mitra'), true)) {
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
        set_login_session($account);

        header('Location: ' . dashboard_url_for_role($account['Role']));
        exit;
    }

    $error = 'Email atau kata sandi tidak sesuai.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Arena Sport</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=10">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <img class="logo-img" src="../assets/img/logo.png" alt="Arena Sport Logo">
                <div>
                    <h1>Arena Sport</h1>
                    <p>Selamat datang kembali! Masuk untuk melanjutkan.</p>
                </div>
            </div>

            <?php if ($alreadyLoggedIn): ?>
                <div class="success-message">
                    Anda sudah masuk sebagai <strong><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></strong>.
                </div>
                <div class="already-logged-in">
                    <a href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn-primary">Lanjut ke Dashboard</a>
                    <a href="logout.php" class="btn-google" style="width:auto; margin-top:12px;">Logout</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form id="loginForm" action="" method="POST" class="login-form">
                    <div class="field-group">
                        <label for="email">Email</label>
                        <div class="field-input">
                            <span class="field-icon">✉</span>
                            <input id="email" type="email" name="email" placeholder="Masukkan email Anda" value="<?php echo htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="password">Kata Sandi</label>
                        <div class="field-input">
                            <span class="field-icon">🔒</span>
                            <input id="password" type="password" name="password" placeholder="Masukkan kata sandi" required>
                            <button type="button" class="password-toggle" aria-label="Tampilkan atau sembunyikan kata sandi">👁</button>
                        </div>
                    </div>

                    <div class="forgot-link">
                        <a href="#">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" name="login" class="btn-primary">Masuk</button>
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
                <a href="../index.php">← Kembali ke beranda</a>
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
                passwordToggle.textContent = isPassword ? '🙈' : '👁';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        }
    </script>
</body>
</html>
