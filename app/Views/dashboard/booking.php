<div class="dashboard-shell profile-dashboard booking-dashboard">
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

    <main class="dashboard-main profile-main booking-main">
        <section class="profile-page-head booking-page-head">
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

        <section class="booking-toolbar" aria-label="Filter booking">
            <nav class="booking-filter-tabs" aria-label="Status booking">
                <a href="#" class="active">Mendatang</a>
                <a href="#">Selesai</a>
                <a href="#">Dibatalkan</a>
            </nav>
            <label class="booking-sort-control">
                <span>Urutkan:</span>
                <select aria-label="Urutkan booking">
                    <option>Terdekat</option>
                    <option>Terbaru</option>
                    <option>Terlama</option>
                </select>
            </label>
        </section>

        <section class="booking-info-banner">
            <span>&#128197;</span>
            <div>
                <h2>Punya rencana main?</h2>
                <p>Jangan lupa datang 15 menit sebelum jadwal booking kamu.</p>
            </div>
            <a href="#">Lihat Aturan</a>
        </section>

        <section class="booking-match-list" id="booking-list" aria-label="Daftar booking saya">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $statusClass = isset($booking['statusClass']) ? $booking['statusClass'] : 'upcoming';
                    $buttonText = isset($booking['button']) ? $booking['button'] : 'Ubah Booking';
                ?>
                <article class="booking-match-card">
                    <div class="booking-match-media">
                        <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                        <span><?php echo e($booking['type']); ?></span>
                    </div>

                    <div class="booking-match-content">
                        <h2><?php echo e($booking['venue']); ?></h2>
                        <p class="booking-location"><span>&#9906;</span><?php echo e($booking['location']); ?></p>
                        <div class="booking-match-meta">
                            <span>&#128197; <?php echo e($booking['date']); ?></span>
                            <span>&#9201; <?php echo e($booking['time']); ?></span>
                            <span>&#9711; <?php echo e($booking['duration']); ?></span>
                        </div>
                        <div class="booking-code-row">
                            <small>Kode Booking</small>
                            <strong><?php echo e($booking['code']); ?></strong>
                            <button type="button" aria-label="Salin kode booking <?php echo e($booking['code']); ?>">&#128203;</button>
                        </div>
                    </div>

                    <div class="booking-payment-panel">
                        <span class="booking-status-pill <?php echo e($statusClass); ?>"><?php echo e($booking['status']); ?></span>
                        <div>
                            <small>Total Pembayaran</small>
                            <strong><?php echo e($booking['price']); ?></strong>
                        </div>
                    </div>

                    <div class="booking-card-actions">
                        <a href="#" class="booking-detail-button">Lihat Detail</a>
                        <a href="#" class="booking-primary-button"><?php echo e($buttonText); ?></a>
                    </div>

                    <a class="booking-row-arrow" href="#" aria-label="Lihat detail <?php echo e($booking['venue']); ?>">&#8250;</a>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="booking-footnote">
            <p>Tidak menemukan booking? Lihat di <a href="<?php echo e(app_url('dashboard/riwayat')); ?>">Riwayat</a></p>
        </section>
    </main>
</div>
