<div class="dashboard-shell">
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
            <a href="#"><span>&#128197;</span>Booking Saya</a>
            <a href="#"><span>&#9825;</span>Favorit</a>
            <a href="#"><span>&#9201;</span>Riwayat</a>
            <a href="#"><span>&#9734;</span>Ulasan Saya</a>
            <a href="#"><span>&#9786;</span>Profil</a>
            <a class="active" href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Kelola preferensi akun Anda</p>
            <small>Atur keamanan, notifikasi, dan preferensi booking.</small>
            <a href="#preferences">Simpan Preferensi &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main settings-main">
        <?php if (!empty($message)): ?>
            <div class="success-message"><?php echo e($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(app_url('settings')); ?>">
            <section class="settings-topbar">
                <div>
                    <p>Kelola preferensi akun dan aplikasi Anda.</p>
                    <h1>Pengaturan Akun</h1>
                </div>
                <div class="settings-topbar-actions">
                    <div class="settings-status-card">
                        <strong>Aktif</strong>
                        <span>Semua pengaturan tersimpan secara otomatis.</span>
                    </div>
                </div>
            </section>

            <section class="settings-grid">
                <div class="settings-column">
                    <article class="settings-card profile-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Informasi Profil</h2>
                                <p>Nama, email, telepon, dan lokasi Anda.</p>
                            </div>
                            <span class="pill">Akun Terverifikasi</span>
                        </div>

                        <div class="profile-summary">
                            <div class="profile-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                            <div class="profile-details">
                                <div class="profile-row">
                                    <div>
                                        <h3><?php echo e($userName); ?></h3>
                                        <p><?php echo e($userRole); ?></p>
                                    </div>
                                    <button type="button" class="btn-small">Ubah Profil</button>
                                </div>

                                <div class="profile-data profile-form">
                                    <div class="field-row">
                                        <label for="profile-name">Nama Lengkap</label>
                                        <input id="profile-name" name="nama" type="text" class="settings-input" value="<?php echo e($userName); ?>" required>
                                    </div>
                                    <div class="field-row">
                                        <label for="profile-email">Email</label>
                                        <input id="profile-email" name="email" type="email" class="settings-input" value="<?php echo e($userEmail); ?>" required>
                                    </div>
                                    <div class="field-row">
                                        <label for="profile-phone">No. Hp</label>
                                        <input id="profile-phone" name="telepon" type="tel" class="settings-input" value="<?php echo e($userPhone); ?>" placeholder="081234567890">
                                    </div>
                                    <div class="field-row">
                                        <label for="profile-city">Kota</label>
                                        <input id="profile-city" name="kota" type="text" class="settings-input" value="<?php echo e($userCity); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="settings-card tile-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Tampilan Aplikasi</h2>
                                <p>Pilih tema dan bahasa yang Anda gunakan.</p>
                            </div>
                        </div>

                        <div class="settings-fields">
                            <div class="field-row">
                                <div>
                                    <label>Mode Tema</label>
                                    <strong>Dark Mode</strong>
                                </div>
                                <div class="pill small">Aktif</div>
                            </div>
                            <div class="field-row">
                                <div>
                                    <label>Bahasa</label>
                                    <strong>Indonesia</strong>
                                </div>
                                <button type="button" class="btn-small btn-secondary">Ubah</button>
                            </div>
                        </div>
                    </article>

                    <article class="settings-card tile-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Notifikasi</h2>
                                <p>Nyalakan pemberitahuan sesuai kebutuhan.</p>
                            </div>
                        </div>

                        <div class="settings-toggle-list">
                            <label class="toggle-item">
                                <span>Booking Berhasil</span>
                                <input type="checkbox" name="notify_booking" checked>
                                <span class="toggle-switch"></span>
                            </label>
                            <label class="toggle-item">
                                <span>Peringat Jadwal</span>
                                <input type="checkbox" name="notify_schedule" checked>
                                <span class="toggle-switch"></span>
                            </label>
                            <label class="toggle-item">
                                <span>Promo & Diskon</span>
                                <input type="checkbox" name="notify_offer">
                                <span class="toggle-switch"></span>
                            </label>
                            <label class="toggle-item">
                                <span>Informasi Lapangan Baru</span>
                                <input type="checkbox" name="notify_new" checked>
                                <span class="toggle-switch"></span>
                            </label>
                        </div>
                    </article>
                </div>

                <div class="settings-column">
                    <article class="settings-card security-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Keamanan Akun</h2>
                                <p>Periksa kata sandi serta verifikasi akun Anda.</p>
                            </div>
                            <button type="button" class="btn-small">Ubah</button>
                        </div>

                        <div class="security-list">
                            <div class="security-row">
                                <div>
                                    <h3>Password</h3>
                                    <p>••••••••••••••</p>
                                </div>
                                <button type="button" class="btn-small">Ubah</button>
                            </div>
                            <div class="security-row verified">
                                <div>
                                    <h3>Verifikasi Email</h3>
                                    <p>Aktif</p>
                                </div>
                                <span class="pill small success">Aktif</span>
                            </div>
                            <div class="security-row verified">
                                <div>
                                    <h3>Verifikasi Nomor HP</h3>
                                    <p>Aktif</p>
                                </div>
                                <span class="pill small success">Aktif</span>
                            </div>
                            <button type="button" class="btn-primary btn-block">Ganti Password</button>
                        </div>
                    </article>

                    <article class="settings-card payment-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Metode Pembayaran</h2>
                                <p>Tambahkan atau pilih metode favorit.</p>
                            </div>
                            <button type="button" class="btn-small">Tambah Metode</button>
                        </div>

                        <div class="payment-list">
                            <span class="payment-chip">BCA Virtual Account</span>
                            <span class="payment-chip">Dana</span>
                            <span class="payment-chip">GoPay</span>
                        </div>
                    </article>

                    <article class="settings-card booking-card" id="preferences">
                        <div class="settings-card-header">
                            <div>
                                <h2>Preferensi Booking</h2>
                                <p>Atur filter pencarian lapangan favorit Anda.</p>
                            </div>
                        </div>

                        <div class="settings-fields">
                            <div class="field-row">
                                <div>
                                    <label>Kota Favorit</label>
                                    <strong>Parepare</strong>
                                </div>
                                <button type="button" class="btn-small btn-secondary">Ubah</button>
                            </div>
                            <div class="field-row">
                                <div>
                                    <label>Olahraga Favorit</label>
                                    <strong>Futsal</strong>
                                </div>
                                <button type="button" class="btn-small btn-secondary">Ubah</button>
                            </div>
                            <div class="field-row">
                                <div>
                                    <label>Radius Pencarian</label>
                                    <strong>10 KM</strong>
                                </div>
                                <button type="button" class="btn-small btn-secondary">Ubah</button>
                            </div>
                        </div>
                    </article>

                    <article class="settings-card danger-card">
                        <div class="settings-card-header">
                            <div>
                                <h2>Zona Bahaya</h2>
                                <p>Hapus akun atau riwayat dengan aman.</p>
                            </div>
                            <button type="button" class="btn-danger">Hapus Riwayat</button>
                        </div>

                        <div class="danger-actions">
                            <p>Hapus riwayat tidak dapat dikembalikan dan data booking Anda secara permanen.</p>
                            <button type="button" class="btn-danger outline">Hapus Akun Permanen</button>
                        </div>
                    </article>
                </div>
            </section>

            <button type="submit" class="btn-primary btn-block save-button">Simpan Perubahan</button>
            <a href="<?php echo e(app_url('public/logout.php')); ?>" class="btn-logout btn-block">Logout</a>
        </form>
    </main>
</div>
