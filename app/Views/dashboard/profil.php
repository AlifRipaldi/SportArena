<?php
$profileMetrics = isset($profileMetrics) ? $profileMetrics : array('bookings' => 0, 'completed' => 0, 'paid' => 'Rp0', 'favorites' => 0, 'reviews' => 0, 'rating' => '0.0', 'notifications' => 0);
$profileRecentBookings = isset($profileRecentBookings) && is_array($profileRecentBookings) ? $profileRecentBookings : array();
$userAvatar = isset($userAvatar) ? $userAvatar : 'https://ui-avatars.com/api/?name=User&background=20314a&color=ffffff';
?>
<div class="dashboard-shell profile-dashboard">
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
            <a href="<?php echo e(app_url('settings')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'settings' ? 'active' : ''; ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo profile-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main" id="profil">
        <section class="profile-page-head">
            <div>
                <h1><?php echo e(isset($pageHeading) ? $pageHeading : 'Profil Saya'); ?></h1>
                <p><?php echo e(isset($pageSubheading) ? $pageSubheading : 'Kelola informasi profil dan aktivitas Anda.'); ?></p>
            </div>
            <div class="profile-head-actions">
                <?php require __DIR__ . '/partials/customer_notifications.php'; ?>
                <a href="<?php echo e(app_url('settings')); ?>" class="profile-account-menu" aria-label="Buka pengaturan akun">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                    <span>&#8964;</span>
                </a>
            </div>
        </section>

        <?php if (!empty($profileMessage)): ?><section class="settings-alert success-message" role="status"><?php echo e($profileMessage); ?></section><?php endif; ?>
        <?php if (!empty($profileError)): ?><section class="settings-alert error-message" role="alert"><?php echo e($profileError); ?></section><?php endif; ?>

        <section class="profile-overview">
            <div class="profile-photo-wrap">
                <div class="profile-photo-ring">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                </div>
            </div>

            <div class="profile-summary-copy">
                <div class="profile-name-row">
                    <h2><?php echo e($userName); ?></h2>
                    <span class="profile-verified"><?php echo !empty($userVerified) ? 'Akun Terverifikasi' : 'Belum Terverifikasi'; ?> <span>&#10003;</span></span>
                </div>
                <ul class="profile-contact-list">
                    <li><span>&#9993;</span><?php echo e($userEmail); ?></li>
                    <li><span>&#9742;</span><?php echo e($userPhone); ?></li>
                    <li><span>&#9906;</span><?php echo e($userCity); ?>, Sulawesi Selatan</li>
                    <li><span>&#128197;</span>Bergabung sejak <?php echo e($userJoined); ?></li>
                </ul>
                <div class="profile-action-row">
                    <a href="#informasi" class="profile-btn primary"><span>&#9998;</span>Edit Profil</a>
                </div>
            </div>

            <div class="profile-quick-stats" aria-label="Ringkasan profil">
                <div class="profile-quick-stat green">
                    <span class="profile-stat-icon">&#128197;</span>
                    <strong><?php echo e($profileMetrics['bookings']); ?></strong>
                    <small>Booking</small>
                </div>
                <div class="profile-quick-stat purple">
                    <span class="profile-stat-icon">&#9825;</span>
                    <strong><?php echo e($profileMetrics['favorites']); ?></strong>
                    <small>Favorit</small>
                </div>
                <div class="profile-quick-stat gold">
                    <span class="profile-stat-icon">&#9734;</span>
                    <strong><?php echo e($profileMetrics['rating']); ?></strong>
                    <small>Rating</small>
                </div>
                <div class="profile-quick-stat blue">
                    <span class="profile-stat-icon">&#128197;</span>
                    <strong><?php echo e($profileMetrics['reviews']); ?></strong>
                    <small>Ulasan</small>
                </div>
            </div>
        </section>

        <nav class="profile-tabs" aria-label="Bagian profil">
            <a href="#informasi" class="active">Informasi Pribadi</a>
            <a href="#aktivitas">Aktivitas</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>">Ulasan</a>
            <a href="<?php echo e(app_url('dashboard/booking')); ?>">Pembayaran</a>
        </nav>

        <section class="profile-content-grid">
            <form class="profile-panel profile-info-panel" id="informasi" method="POST" action="<?php echo e(app_url('dashboard/profil')); ?>">
                <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
                <div class="profile-panel-header">
                    <h2><span>&#9786;</span>Informasi Pribadi</h2>
                </div>
                <dl class="profile-details-list">
                    <div>
                        <dt>Nama Lengkap</dt>
                        <dd><input class="profile-detail-field" name="nama" type="text" value="<?php echo e($userName); ?>" autocomplete="name" required></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><input class="profile-detail-field" name="email" type="email" value="<?php echo e($userEmail); ?>" autocomplete="email" required></dd>
                    </div>
                    <div>
                        <dt>No. HP</dt>
                        <dd><input class="profile-detail-field" name="telepon" type="tel" value="<?php echo e($userPhone); ?>" autocomplete="tel"></dd>
                    </div>
                    <div>
                        <dt>Kota</dt>
                        <dd><input class="profile-detail-field" name="kota" type="text" value="<?php echo e($userCity); ?>" autocomplete="address-level2"></dd>
                    </div>
                </dl>
                <button type="submit" class="profile-panel-action"><span>&#10003;</span>Simpan Perubahan</button>
            </form>

            <div class="profile-side-stack">
                <article class="profile-panel">
                    <div class="profile-panel-header">
                        <h2><span>&#128200;</span>Statistik Aktivitas</h2>
                    </div>
                    <div class="profile-stat-list">
                        <div>
                            <span>Total Booking</span>
                            <strong><?php echo e($profileMetrics['bookings']); ?> Kali</strong>
                        </div>
                        <div>
                            <span>Booking Selesai</span>
                            <strong><?php echo e($profileMetrics['completed']); ?> Kali</strong>
                        </div>
                        <div>
                            <span>Total Pembayaran</span>
                            <strong><?php echo e($profileMetrics['paid']); ?></strong>
                        </div>
                        <div>
                            <span>Member Sejak</span>
                            <strong><?php echo e($userJoined); ?></strong>
                        </div>
                    </div>
                </article>

                <article class="profile-panel">
                    <div class="profile-panel-header">
                        <h2><span>&#9881;</span>Preferensi Saya</h2>
                    </div>
                    <div class="profile-preference-list">
                        <div>
                            <span><i>&#9673;</i>Olahraga Favorit</span>
                            <strong><?php echo e($favoriteSport); ?></strong>
                        </div>
                        <div>
                            <span><i>&#9201;</i>Waktu Favorit</span>
                            <strong>Sore - Malam</strong>
                        </div>
                        <div>
                            <span><i>&#9906;</i>Kota Favorit</span>
                            <strong><?php echo e($favoriteCity); ?></strong>
                        </div>
                        <div>
                            <span><i>&#9678;</i>Radius Pencarian</span>
                            <strong><?php echo e($searchRadius); ?> KM</strong>
                        </div>
                    </div>
                </article>
            </div>

            <article class="profile-panel profile-achievements-panel">
                <div class="profile-panel-header">
                    <h2><span>&#127942;</span>Pencapaian</h2>
                </div>
                <div class="profile-achievement-list">
                    <div class="profile-achievement green">
                        <span>&#9917;</span>
                        <strong>Player Aktif</strong>
                        <small>Melakukan 10 booking</small>
                        <em>10 Mei 2024</em>
                    </div>
                    <div class="profile-achievement blue">
                        <span>&#9733;</span>
                        <strong>Pelanggan Setia</strong>
                        <small>Melakukan 20 booking</small>
                        <em>22 Juni 2024</em>
                    </div>
                    <div class="profile-achievement purple">
                        <span>&#9819;</span>
                        <strong>Top Reviewer</strong>
                        <small>Memberikan 10 ulasan</small>
                        <em>15 Juli 2024</em>
                    </div>
                </div>
            </article>

            <article class="profile-panel profile-activity-panel" id="aktivitas">
                <div class="profile-panel-header row">
                    <h2><span>&#9201;</span>Aktivitas Terbaru</h2>
                    <a href="<?php echo e(app_url('dashboard/riwayat')); ?>">Lihat Semua &#8594;</a>
                </div>
                <div class="profile-activity-list">
                    <?php foreach ($profileRecentBookings as $booking): ?>
                        <a href="<?php echo e(app_url('dashboard/riwayat')); ?>">
                            <span class="profile-activity-icon">&#128197;</span>
                            <span>
                                <strong>Booking <?php echo e($booking['venue']); ?></strong>
                                <small><?php echo e($booking['date']); ?> &bull; <?php echo e($booking['time']); ?></small>
                            </span>
                            <em><?php echo e($booking['status']); ?></em>
                            <i>&#8250;</i>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($profileRecentBookings)): ?><p>Belum ada aktivitas booking.</p><?php endif; ?>
                </div>
            </article>
        </section>
    </main>
</div>
