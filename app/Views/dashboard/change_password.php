<main class="settings-password-page">
    <header class="settings-password-topbar">
        <a href="<?php echo e(app_url('settings')); ?>" class="settings-password-back">
            <span>&#8592;</span>
            Kembali
        </a>
        <time><?php echo e($displayDate); ?></time>
    </header>

    <section class="settings-password-hero" aria-labelledby="change-password-title">
        <div class="settings-password-shield">
            <span>&#9917;</span>
        </div>
        <h1 id="change-password-title">Ubah Password</h1>
        <p>Perbarui password akun Anda</p>
    </section>

    <section class="settings-password-card">
        <?php if (!empty($message)): ?>
            <div class="settings-password-alert success-message"><?php echo e($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="settings-password-alert error-message"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <form action="<?php echo e(app_url('settings/password')); ?>" method="post" class="settings-password-form">
            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
            <label>
                <span>Password Saat Ini</span>
                <div class="settings-password-input">
                    <i>&#128274;</i>
                    <input type="password" name="current_password" placeholder="Masukkan password saat ini" autocomplete="current-password" required>
                    <button type="button" aria-label="Tampilkan password">&#128065;</button>
                </div>
            </label>

            <label>
                <span>Password Baru</span>
                <div class="settings-password-input">
                    <i>&#128274;</i>
                    <input type="password" name="new_password" placeholder="Masukkan password baru" autocomplete="new-password" minlength="8" required>
                    <button type="button" aria-label="Tampilkan password">&#128065;</button>
                </div>
                <small>Minimal 8 karakter, kombinasi huruf besar, kecil, angka, dan simbol</small>
            </label>

            <label>
                <span>Konfirmasi Password Baru</span>
                <div class="settings-password-input">
                    <i>&#128274;</i>
                    <input type="password" name="confirm_password" placeholder="Masukkan ulang password baru" autocomplete="new-password" minlength="8" required>
                    <button type="button" aria-label="Tampilkan password">&#128065;</button>
                </div>
            </label>

            <div class="settings-password-tips">
                <span>&#128737;</span>
                <div>
                    <strong>Tips Keamanan</strong>
                    <p>Gunakan password yang kuat dan jangan bagikan kepada siapapun.</p>
                </div>
            </div>

            <button class="settings-password-submit" type="submit">
                <span>&#128274;</span>
                Simpan Password Baru
            </button>
        </form>
    </section>

    <p class="settings-password-help">Butuh bantuan? Hubungi <a href="mailto:admin@arenasport.id">admin sistem</a>.</p>
</main>

<script>
    document.querySelectorAll('.settings-password-input button').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = button.parentElement.querySelector('input');
            var isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            button.innerHTML = isPassword ? '&#128584;' : '&#128065;';
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>
