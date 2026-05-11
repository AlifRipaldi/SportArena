<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Arena Sport</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=4">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <div class="logo-icon">AS</div>
                <div>
                    <h1>Arena Sport</h1>
                    <p>Selamat datang kembali! Masuk untuk melanjutkan.</p>
                </div>
            </div>

            <form id="loginForm" action="" method="POST" class="login-form">
                <div class="field-group">
                    <label for="email">Email</label>
                    <div class="field-input">
                        <span class="field-icon">✉</span>
                        <input id="email" type="email" name="email" placeholder="Masukkan email Anda" required>
                    </div>
                </div>
                <div class="field-group">
                    <label for="password">Kata Sandi</label>
                    <div class="field-input">
                        <span class="field-icon">🔒</span>
                        <input id="password" type="password" name="password" placeholder="Masukkan kata sandi" required minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="Minimal 8 karakter, berisi huruf besar, huruf kecil, angka, dan karakter khusus">
                        <button type="button" class="password-toggle" aria-label="Tampilkan atau sembunyikan kata sandi">👁</button>
                    </div>
                    <small class="password-help">Gunakan huruf besar, huruf kecil, angka, dan satu karakter khusus.</small>
                </div>

                <div class="forgot-link">
                    <a href="#">Lupa kata sandi?</a>
                </div>

                <button type="submit" name="login" class="btn-primary">Masuk</button>
            </form>

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
        const loginForm = document.getElementById('loginForm');

        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.textContent = isPassword ? '🙈' : '👁';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        }

        if (loginForm) {
            loginForm.addEventListener('submit', (event) => {
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