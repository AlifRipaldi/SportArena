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
            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'booking' ? 'active' : ''; ?>"><span>&#128197;</span>Booking Saya</a>
            <a href="<?php echo e(app_url('dashboard/favorit')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'favorit' ? 'active' : ''; ?>"><span>&#9825;</span>Favorit</a>
            <a href="<?php echo e(app_url('dashboard/riwayat')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'riwayat' ? 'active' : ''; ?>"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'profil' ? 'active' : ''; ?>"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="#booking-list">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-topbar search-topbar">
            <div>
                <p><?php echo e($pageHeading); ?></p>
                <h1><?php echo e($pageSubheading); ?></h1>
            </div>
            <div class="dashboard-actions">
                <button type="button" class="icon-button" aria-label="Notifikasi">&#128276;</button>
                <div class="dashboard-user">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="booking-page" id="booking-list">
            <div class="booking-topbar">
                <div class="booking-title">
                    <small>Booking Saya</small>
                    <h2>Kelola semua booking lapangan kamu</h2>
                    <p>Jangan lupa datang 15 menit sebelum jadwal booking. Temukan detail booking, status, dan pilihan ubah langsung dari halaman ini.</p>
                </div>
                <div class="booking-tabs">
                    <button class="booking-tab active" type="button">Mendatang</button>
                    <button class="booking-tab" type="button">Selesai</button>
                    <button class="booking-tab" type="button">Dibatalkan</button>
                </div>
            </div>

            <div class="booking-grid">
                <aside class="booking-sidebar">
                    <div>
                        <h3>Punya rencana main?</h3>
                        <p>Booking kamu dikelola dalam satu tampilan. Pastikan semua informasi sudah benar dan lihat aturan penggunaan sebelum datang ke lapangan.</p>
                    </div>
                    <a href="#" class="btn-primary">Lihat Aturan</a>
                </aside>

                <div class="booking-list">
                    <?php foreach ($bookings as $booking): ?>
                        <article class="booking-card">
                            <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                            <div class="booking-card-info">
                                <span class="booking-label"><?php echo e($booking['type']); ?></span>
                                <h3><?php echo e($booking['venue']); ?></h3>
                                <p class="booking-location"><?php echo e($booking['location']); ?></p>
                                <div class="booking-meta">
                                    <span>&#128197; <?php echo e($booking['date']); ?></span>
                                    <span>&#9201; <?php echo e($booking['time']); ?></span>
                                    <span>&#9711; <?php echo e($booking['duration']); ?></span>
                                </div>
                                <div class="booking-code">Kode Booking <?php echo e($booking['code']); ?></div>
                            </div>
                            <div class="booking-card-actions">
                                <span class="booking-status <?php echo e($booking['statusClass']); ?>"><?php echo e($booking['status']); ?></span>
                                <div class="booking-cost"><?php echo e($booking['price']); ?></div>
                                <a href="#" class="btn-secondary">Lihat Detail</a>
                                <a href="#" class="btn-primary"><?php echo e($booking['button']); ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
    .booking-page {
        margin-top: 28px;
        display: grid;
        gap: 24px;
    }

    .booking-topbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
    }

    .booking-title {
        display: grid;
        gap: 8px;
    }

    .booking-title small {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(139, 232, 70, 0.16);
        color: #8bdd4a;
        font-weight: 800;
        font-size: 13px;
    }

    .booking-title h2 {
        color: #ffffff;
        margin: 0;
        font-size: 32px;
    }

    .booking-title p {
        color: rgba(237, 246, 255, 0.7);
        margin: 0;
        max-width: 620px;
    }

    .booking-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-tab {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.03);
        color: rgba(237, 246, 255, 0.8);
        padding: 12px 20px;
        cursor: pointer;
        font-weight: 700;
    }

    .booking-tab.active {
        background: linear-gradient(135deg, #8bdd4a, #43b940);
        color: #07121f;
        border-color: transparent;
    }

    .booking-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 22px;
    }

    .booking-sidebar {
        padding: 24px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 360px;
    }

    .booking-sidebar h3 {
        color: #ffffff;
        margin-bottom: 12px;
        font-size: 20px;
    }

    .booking-sidebar p {
        color: rgba(237, 246, 255, 0.72);
        line-height: 1.75;
        margin-bottom: 22px;
    }

    .booking-sidebar .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 20px;
        border-radius: 14px;
        background: linear-gradient(135deg, #8bdd4a, #43b940);
        color: #07121f;
        font-weight: 900;
        text-decoration: none;
    }

    .booking-list {
        display: grid;
        gap: 20px;
    }

    .booking-card {
        display: grid;
        grid-template-columns: 160px 1fr 240px;
        gap: 18px;
        align-items: center;
        padding: 22px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.045);
    }

    .booking-card img {
        width: 100%;
        height: 156px;
        object-fit: cover;
        border-radius: 18px;
    }

    .booking-card-info {
        display: grid;
        gap: 10px;
    }

    .booking-label {
        display: inline-flex;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(72, 196, 72, 0.14);
        color: #8bdd4a;
        font-size: 12px;
        font-weight: 800;
        width: fit-content;
    }

    .booking-card-info h3 {
        margin: 0;
        font-size: 20px;
        color: #ffffff;
    }

    .booking-card-info .booking-location {
        color: rgba(237, 246, 255, 0.64);
        margin: 0;
        font-size: 13px;
    }

    .booking-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: rgba(237, 246, 255, 0.68);
        font-size: 13px;
    }

    .booking-meta span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .booking-code {
        color: #8bdd4a;
        font-size: 13px;
        font-weight: 700;
        margin-top: 10px;
    }

    .booking-card-actions {
        display: grid;
        gap: 12px;
        justify-items: end;
    }

    .booking-status {
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .booking-status.upcoming { background: rgba(72, 196, 72, 0.14); color: #8bdd4a; }
    .booking-status.completed { background: rgba(109, 134, 233, 0.16); color: #b1bdfb; }
    .booking-status.pending { background: rgba(255, 191, 0, 0.16); color: #ffe27d; }

    .booking-cost {
        color: #8bdd4a;
        font-size: 18px;
        font-weight: 900;
    }

    .booking-card-actions a {
        display: inline-flex;
        padding: 12px 16px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 13px;
        text-align: center;
        min-width: 140px;
    }

    .booking-card-actions .btn-secondary {
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
        background: transparent;
    }

    .booking-card-actions .btn-primary {
        background: linear-gradient(135deg, #8bdd4a, #43b940);
        color: #07121f;
    }

    @media (max-width: 1040px) {
        .booking-grid {
            grid-template-columns: 1fr;
        }

        .booking-card {
            grid-template-columns: 1fr;
        }

        .booking-card-actions {
            justify-items: stretch;
        }
    }
</style>
