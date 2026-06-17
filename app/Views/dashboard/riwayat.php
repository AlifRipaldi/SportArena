<div class="dashboard-shell profile-dashboard history-dashboard">
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
            <a href="<?php echo e(app_url('settings')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'settings' ? 'active' : ''; ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo profile-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main history-main">
        <section class="profile-page-head history-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
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

        <section class="history-toolbar" aria-label="Filter riwayat">
            <nav class="history-filter-tabs" aria-label="Status riwayat">
                <a href="#" class="active">Semua</a>
                <a href="#">Selesai</a>
                <a href="#">Dibatalkan</a>
            </nav>
            <label class="history-sort-control">
                <span>Urutkan:</span>
                <select aria-label="Urutkan riwayat">
                    <option>Terbaru</option>
                    <option>Terlama</option>
                    <option>Pembayaran Tertinggi</option>
                    <option>Pembayaran Terendah</option>
                </select>
            </label>
        </section>

        <section class="history-info-banner">
            <span>&#128197;</span>
            <p>Berikut adalah riwayat semua booking lapangan kamu.</p>
        </section>

        <section class="history-match-list" id="booking-list" aria-label="Daftar riwayat booking">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $status = strtolower(isset($booking['status']) ? $booking['status'] : 'selesai');
                    $isCanceled = $status === 'dibatalkan';
                    $statusLabel = $isCanceled ? 'Dibatalkan' : 'Selesai';
                    $statusClass = $isCanceled ? 'canceled' : 'completed';
                ?>
                <article class="history-match-card">
                    <div class="history-match-media">
                        <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                        <span><?php echo e($booking['type']); ?></span>
                    </div>

                    <div class="history-match-content">
                        <h2><?php echo e($booking['venue']); ?></h2>
                        <p class="history-match-location"><span>&#9906;</span><?php echo e($booking['location']); ?></p>
                        <div class="history-match-meta">
                            <span>&#128197; <?php echo e($booking['date']); ?></span>
                            <span>&#9201; <?php echo e($booking['time']); ?></span>
                            <span>&#9711; <?php echo e($booking['duration']); ?></span>
                        </div>
                        <div class="history-code-row">
                            <small>Kode Booking</small>
                            <strong><?php echo e($booking['code']); ?></strong>
                            <button type="button" aria-label="Salin kode booking">&#128203;</button>
                        </div>
                    </div>

                    <div class="history-payment-panel">
                        <span class="history-status-pill <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                        <div>
                            <small>Total Pembayaran</small>
                            <strong class="<?php echo e($statusClass); ?>"><?php echo e($booking['price']); ?></strong>
                        </div>
                        <a href="#">Lihat Detail</a>
                    </div>

                    <a class="history-row-arrow" href="#" aria-label="Lihat detail <?php echo e($booking['venue']); ?>">&#8250;</a>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="history-empty-note">
            <p>Tidak menemukan booking? Coba <a href="#">ubah filter</a> atau urutan pencarian.</p>
        </section>
    </main>
</div>
