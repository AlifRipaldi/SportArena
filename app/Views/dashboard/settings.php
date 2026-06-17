<div class="dashboard-shell profile-dashboard settings-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport Logo">
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
                <button type="button" class="profile-notification" aria-label="Notifikasi">
                    <span>&#128276;</span>
                    <sup>1</sup>
                </button>
                <div class="profile-account-menu">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <?php if (!empty($message)): ?>
            <section class="settings-alert success-message"><?php echo e($message); ?></section>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <section class="settings-alert error-message"><?php echo e($errorMessage); ?></section>
        <?php endif; ?>

        <form class="settings-match-form" method="POST" action="<?php echo e(app_url('settings')); ?>">
            <section class="settings-panel settings-profile-panel">
                <div class="settings-panel-title">
                    <span>&#9786;</span>
                    <h2>Informasi Profil</h2>
                </div>

                <div class="settings-profile-body">
                    <div class="settings-avatar-wrap">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=260&auto=format&fit=crop" alt="Foto profil">
                        <button type="button" aria-label="Ubah foto profil">&#128247;</button>
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
                        <button type="button" class="settings-outline-button">Ubah Profil</button>
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
                            <button type="button">Ubah</button>
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
                    <button type="button" class="settings-password-button"><span>&#128274;</span>Ganti Password</button>
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
                            <input type="radio" name="theme_mode" value="light" <?php echo $themeMode === 'light' ? 'checked' : ''; ?>>
                            <span></span>
                            Light Mode
                        </label>
                        <label class="settings-radio-row">
                            <input type="radio" name="theme_mode" value="dark" <?php echo $themeMode !== 'light' ? 'checked' : ''; ?>>
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
                        <a href="#"><span class="bca">BCA</span>BCA Virtual Account<i>&#8250;</i></a>
                        <a href="#"><span class="dana">&#9679;</span>Dana<i>&#8250;</i></a>
                        <a href="#"><span class="ovo">&#9679;</span>OVO<i>&#8250;</i></a>
                        <a href="#"><span class="gopay">&#9679;</span>GoPay<i>&#8250;</i></a>
                    </div>
                    <button type="button" class="settings-add-payment"><span>+</span>Tambah Metode</button>
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

            <section class="settings-panel settings-danger-panel">
                <div class="settings-panel-title danger">
                    <span>&#9888;</span>
                    <h2>Zona Bahaya</h2>
                </div>
                <div class="settings-danger-row">
                    <div>
                        <h3>Hapus seluruh riwayat booking</h3>
                        <p>Tindakan ini akan menghapus semua riwayat booking Anda secara permanen.</p>
                    </div>
                    <button type="button"><span>&#128465;</span>Hapus Riwayat</button>
                </div>
                <div class="settings-danger-row">
                    <div>
                        <h3>Hapus Akun Permanen</h3>
                        <p>Tindakan ini akan menghapus akun Anda secara permanen dan tidak dapat dikembalikan.</p>
                    </div>
                    <button type="button"><span>&#128465;</span>Hapus Akun Permanen</button>
                </div>
            </section>

            <div class="settings-save-row">
                <button type="submit" class="settings-save-button"><span>&#10003;</span>Simpan Perubahan</button>
            </div>
        </form>
    </main>
</div>
