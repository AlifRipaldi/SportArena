<div class="dashboard-shell profile-dashboard settings-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo-mark.png')); ?>" alt="Arena Sport Logo">
            </div>
            <div>
                <strong>Arena</strong>
                <span>Sport</span>
            </div>
        </div>

        <nav class="dashboard-menu" aria-label="Menu dashboard">
            <a href="<?php echo e(app_url('dashboard')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : ''; ?>"><span>&#8962;</span>Dashboard</a>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'lapangan' ? 'active' : ''; ?>"><span>&#128269;</span>Cari Lapangan</a>
            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'booking' ? 'active' : ''; ?>"><span>&#128197;</span>Booking Saya</a>
            <a href="<?php echo e(app_url('dashboard/favorit')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'favorit' ? 'active' : ''; ?>"><span>&#9825;</span>Favorit</a>
            <a href="<?php echo e(app_url('dashboard/riwayat')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'riwayat' ? 'active' : ''; ?>"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'profil' ? 'active' : ''; ?>"><span>&#9786;</span>Profil</a>
            <a class="active" href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo profile-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main settings-match-main">
        <section class="profile-page-head settings-page-head">
            <div>
                <h1>Pengaturan Akun</h1>
                <p>Kelola preferensi akun dan aplikasi Anda.</p>
            </div>
            <div class="profile-head-actions">
                <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="profile-notification" aria-label="Lihat notifikasi booking">
                    <span>&#128276;</span>
                    <sup>1</sup>
                </a>
                <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="profile-account-menu" aria-label="Buka profil">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil" data-settings-avatar-preview>
                    <span>&#8964;</span>
                </a>
            </div>
        </section>

        <?php if (!empty($message)): ?>
            <section class="settings-alert success-message"><?php echo e($message); ?></section>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <section class="settings-alert error-message"><?php echo e($errorMessage); ?></section>
        <?php endif; ?>

        <form class="settings-match-form" method="POST" action="<?php echo e(app_url('settings')); ?>" enctype="multipart/form-data" data-theme-url="<?php echo e(app_url('settings/theme')); ?>">
            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
            <section class="settings-panel settings-profile-panel">
                <div class="settings-panel-title">
                    <span>&#9786;</span>
                    <h2>Informasi Profil</h2>
                </div>

                <div class="settings-profile-body">
                    <div class="settings-avatar-wrap">
                        <img src="<?php echo e($userAvatar); ?>" alt="Foto profil" data-settings-avatar-preview>
                        <label class="settings-avatar-edit" title="Ubah foto profil" aria-label="Ubah foto profil">
                            <span aria-hidden="true">&#128247;</span>
                            <input type="file" name="avatar" accept="image/png,image/jpeg" data-settings-avatar-input>
                        </label>
                    </div>

                    <div class="settings-profile-fields">
                        <label class="settings-line-field">
                            <span>Nama Lengkap</span>
                            <i>:</i>
                            <input name="nama" type="text" value="<?php echo e($userName); ?>" required>
                            <strong>Akun Terverifikasi <em>&#10003;</em></strong>
                        </label>
                        <label class="settings-line-field">
                            <span>Email</span>
                            <i>:</i>
                            <input name="email" type="email" value="<?php echo e($userEmail); ?>" required>
                        </label>
                        <label class="settings-line-field">
                            <span>No. HP</span>
                            <i>:</i>
                            <input name="telepon" type="tel" value="<?php echo e($userPhone); ?>" placeholder="0812xxxxxxxx">
                        </label>
                        <label class="settings-line-field">
                            <span>Kota</span>
                            <i>:</i>
                            <input name="kota" type="text" value="<?php echo e($userCity); ?>">
                        </label>
                        <button type="submit" class="settings-outline-button">Simpan Profil</button>
                    </div>
                </div>
            </section>

            <section class="settings-panel settings-security-panel">
                <div class="settings-panel-title">
                    <span>&#128737;</span>
                    <h2>Keamanan Akun</h2>
                </div>

                <div class="settings-security-grid">
                    <div class="settings-security-list">
                        <div class="settings-security-row">
                            <span>Password</span>
                            <strong>************</strong>
                        </div>
                        <div class="settings-security-row">
                            <span>Verifikasi Email</span>
                            <strong class="active"><i>&#10003;</i>Aktif</strong>
                        </div>
                        <div class="settings-security-row">
                            <span>Verifikasi Nomor HP</span>
                            <strong class="active"><i>&#10003;</i>Aktif</strong>
                        </div>
                    </div>
                    <a href="<?php echo e(app_url('settings/password')); ?>" class="settings-password-button"><span>&#128274;</span>Ganti Password</a>
                </div>
            </section>

            <section class="settings-card-grid">
                <article class="settings-panel settings-mini-panel">
                    <div class="settings-panel-title">
                        <span>&#128187;</span>
                        <h2>Tampilan Aplikasi</h2>
                    </div>

                    <div class="settings-choice-list">
                        <p>Tema</p>
                        <label class="settings-radio-row">
                            <input type="radio" name="theme_mode" value="light" <?php echo $themeMode === 'light' ? 'checked' : ''; ?> data-theme-choice>
                            <span></span>
                            Light Mode
                        </label>
                        <label class="settings-radio-row">
                            <input type="radio" name="theme_mode" value="dark" <?php echo $themeMode !== 'light' ? 'checked' : ''; ?> data-theme-choice>
                            <span></span>
                            Dark Mode
                        </label>
                    </div>

                    <label class="settings-select-field">
                        <span>Bahasa</span>
                        <select name="language">
                            <option value="id" <?php echo $language === 'id' ? 'selected' : ''; ?>>Indonesia</option>
                            <option value="en" <?php echo $language === 'en' ? 'selected' : ''; ?>>English</option>
                        </select>
                    </label>
                </article>

                <article class="settings-panel settings-mini-panel">
                    <div class="settings-panel-title">
                        <span>&#128276;</span>
                        <h2>Notifikasi</h2>
                    </div>

                    <div class="settings-switch-list">
                        <label class="settings-switch-row">
                            <span>Booking Berhasil</span>
                            <input type="checkbox" name="notify_booking" <?php echo $notifyBooking ? 'checked' : ''; ?>>
                            <i></i>
                        </label>
                        <label class="settings-switch-row">
                            <span>Pengingat Jadwal</span>
                            <input type="checkbox" name="notify_schedule" <?php echo $notifySchedule ? 'checked' : ''; ?>>
                            <i></i>
                        </label>
                        <label class="settings-switch-row">
                            <span>Promo &amp; Diskon</span>
                            <input type="checkbox" name="notify_offer" <?php echo $notifyOffer ? 'checked' : ''; ?>>
                            <i></i>
                        </label>
                        <label class="settings-switch-row">
                            <span>Informasi Lapangan Baru</span>
                            <input type="checkbox" name="notify_new" <?php echo $notifyNew ? 'checked' : ''; ?>>
                            <i></i>
                        </label>
                    </div>
                </article>

                <article class="settings-panel settings-mini-panel">
                    <div class="settings-panel-title">
                        <span>&#128179;</span>
                        <h2>Metode Pembayaran</h2>
                    </div>

                    <div class="settings-payment-list">
                        <?php foreach (isset($paymentMethods) ? $paymentMethods : array() as $method): ?>
                            <a href="<?php echo e(app_url('dashboard/booking')); ?>"><span class="bca">&#128179;</span><?php echo e($method['Nama']); ?><i>&#8250;</i></a>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="settings-panel settings-mini-panel">
                    <div class="settings-panel-title">
                        <span>&#9906;</span>
                        <h2>Preferensi Booking</h2>
                    </div>

                    <div class="settings-preference-list">
                        <label>
                            <span>Kota Favorit</span>
                            <i>:</i>
                            <select name="favorite_city">
                                <option value="Parepare" <?php echo $favoriteCity === 'Parepare' ? 'selected' : ''; ?>>Parepare</option>
                                <option value="Makassar" <?php echo $favoriteCity === 'Makassar' ? 'selected' : ''; ?>>Makassar</option>
                                <option value="Pinrang" <?php echo $favoriteCity === 'Pinrang' ? 'selected' : ''; ?>>Pinrang</option>
                            </select>
                        </label>
                        <label>
                            <span>Olahraga Favorit</span>
                            <i>:</i>
                            <select name="favorite_sport">
                                <option value="Futsal" <?php echo $favoriteSport === 'Futsal' ? 'selected' : ''; ?>>Futsal</option>
                                <option value="Badminton" <?php echo $favoriteSport === 'Badminton' ? 'selected' : ''; ?>>Badminton</option>
                                <option value="Mini Soccer" <?php echo $favoriteSport === 'Mini Soccer' ? 'selected' : ''; ?>>Mini Soccer</option>
                                <option value="Basketball" <?php echo $favoriteSport === 'Basketball' ? 'selected' : ''; ?>>Basketball</option>
                            </select>
                        </label>
                        <label>
                            <span>Radius Pencarian</span>
                            <i>:</i>
                            <select name="search_radius">
                                <option value="5" <?php echo $searchRadius === '5' ? 'selected' : ''; ?>>5 KM</option>
                                <option value="10" <?php echo $searchRadius === '10' ? 'selected' : ''; ?>>10 KM</option>
                                <option value="15" <?php echo $searchRadius === '15' ? 'selected' : ''; ?>>15 KM</option>
                                <option value="20" <?php echo $searchRadius === '20' ? 'selected' : ''; ?>>20 KM</option>
                            </select>
                        </label>
                    </div>
                </article>
            </section>

            <div class="settings-save-row">
                <button type="submit" class="settings-save-button"><span>&#10003;</span>Simpan Perubahan</button>
            </div>
        </form>
    </main>
</div>

<script>
    (function () {
        var form = document.querySelector('.settings-match-form');
        var choices = document.querySelectorAll('[data-theme-choice]');

        if (!form) {
            return;
        }

        var avatarInput = form.querySelector('[data-settings-avatar-input]');
        var avatarPreviews = document.querySelectorAll('[data-settings-avatar-preview]');

        if (avatarInput) {
            avatarInput.addEventListener('change', function () {
                var file = avatarInput.files && avatarInput.files[0];

                if (!file || !file.type.match(/^image\/(jpeg|png)$/)) {
                    return;
                }

                var previewUrl = URL.createObjectURL(file);
                avatarPreviews.forEach(function (preview) {
                    preview.src = previewUrl;
                });
            });
        }

        if (!choices.length) {
            return;
        }

        choices.forEach(function (choice) {
            choice.addEventListener('change', function () {
                if (!choice.checked) {
                    return;
                }

                document.body.classList.toggle('light', choice.value === 'light');
                document.body.classList.toggle('dark', choice.value !== 'light');

                var request = new FormData();
                request.append('theme_mode', choice.value);
                request.append('booking_token', form.querySelector('[name="booking_token"]').value);

                fetch(form.getAttribute('data-theme-url'), {
                    method: 'POST',
                    body: request,
                    credentials: 'same-origin'
                }).catch(function () {
                    form.submit();
                });
            });
        });
    })();
</script>
