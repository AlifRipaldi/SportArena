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
                    <p>Jangan lupa datang 15 menit sebelum jadwal booking. Temukan detail booking, status, dan pilihan ubah langsung dari halaman ini.</p>
                </div>
                <div class="booking-actions">
                    <div class="booking-tabs">
                        <button class="booking-tab active" type="button">Mendatang</button>
                        <button class="booking-tab" type="button">Selesai</button>
                        <button class="booking-tab" type="button">Dibatalkan</button>
                    </div>
                    <div class="booking-sort">
                        <label for="sort-booking">Urutkan:</label>
                        <select id="sort-booking" name="sort_booking">
                            <option value="nearest">Terdekat</option>
                            <option value="latest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="booking-info-bar">
                <div class="booking-info-icon">&#128197;</div>
                <div>
                    <p><strong>Punya rencana main?</strong></p>
                    <p>Jangan lupa datang 15 menit sebelum jadwal booking kamu.</p>
                </div>
                <a href="#" class="btn-secondary booking-info-btn">Lihat Aturan</a>
            </div>

            <div class="booking-list">
                <?php foreach ($bookings as $booking): ?>
                    <article class="booking-card">
                        <div class="booking-card-left">
                            <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                            <span class="booking-tag"><?php echo e($booking['type']); ?></span>
                        </div>
                        <div class="booking-card-info">
                            <h3><?php echo e($booking['venue']); ?></h3>
                            <p class="booking-location"><?php echo e($booking['location']); ?></p>
                            <div class="booking-meta">
                                <span>&#128197; <?php echo e($booking['date']); ?></span>
                                <span>&#9201; <?php echo e($booking['time']); ?></span>
                                <span>&#9711; <?php echo e($booking['duration']); ?></span>
                            </div>
                            <div class="booking-code">Kode Booking <strong><?php echo e($booking['code']); ?></strong></div>
                        </div>
                        <div class="booking-card-actions">
                            <span class="booking-status <?php echo e($booking['statusClass']); ?>"><?php echo e($booking['status']); ?></span>
                            <div class="booking-cost-label">Total Pembayaran</div>
                            <div class="booking-cost"><?php echo e($booking['price']); ?></div>
                            <div class="booking-buttons">
                                <a href="#" class="btn-secondary">Lihat Detail</a>
                                <a href="#" class="btn-primary"><?php echo e($booking['button']); ?></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
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
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .booking-title {
        display: grid;
        gap: 8px;
        max-width: 680px;
    }

    .booking-title small {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
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
        color: rgba(237, 246, 255, 0.72);
        margin: 0;
        max-width: 620px;
    }

    .booking-actions {
        display: flex;
        gap: 18px;
        align-items: center;
        flex-wrap: wrap;
    }

    .booking-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-tab {
        min-width: fit-content;
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

    .booking-sort {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .booking-sort label {
        color: rgba(237, 246, 255, 0.72);
        font-size: 13px;
    }

    .booking-sort select {
        min-width: 180px;
        padding: 10px 14px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .booking-info-bar {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 18px;
        align-items: center;
        padding: 22px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
    }

    .booking-info-icon {
        width: 52px;
        height: 52px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: rgba(139, 232, 70, 0.16);
        color: #8bdd4a;
        font-size: 22px;
    }

    .booking-info-bar p {
        margin: 0;
        color: rgba(237, 246, 255, 0.72);
        line-height: 1.5;
    }

    .booking-info-bar p strong {
        color: #fff;
    }

    .booking-info-btn {
        padding: 12px 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
    }

    .booking-list {
        display: grid;
        gap: 20px;
    }

    .booking-card {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr) 220px;
        gap: 18px;
        align-items: center;
        padding: 24px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.04);
    }

    .booking-card-left {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
    }

    .booking-card-left img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
    }

    .booking-tag {
        position: absolute;
        top: 18px;
        left: 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(72, 196, 72, 0.18);
        color: #8bdd4a;
        font-weight: 800;
        font-size: 12px;
    }

    .booking-card-info {
        display: grid;
        gap: 10px;
    }

    .booking-card-info h3 {
        margin: 0;
        font-size: 22px;
        color: #ffffff;
    }

    .booking-location {
        margin: 0;
        color: rgba(237, 246, 255, 0.64);
        font-size: 14px;
    }

    .booking-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        color: rgba(237, 246, 255, 0.68);
    }

    .booking-meta span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.04);
        font-size: 13px;
    }

    .booking-code {
        color: #8bdd4a;
        font-size: 13px;
        font-weight: 700;
        margin-top: 10px;
    }

    .booking-card-actions {
        display: grid;
        gap: 10px;
        justify-items: end;
    }

    .booking-status {
        padding: 12px 18px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .booking-status.upcoming { background: rgba(72, 196, 72, 0.14); color: #8bdd4a; }
    .booking-status.completed { background: rgba(109, 134, 233, 0.16); color: #b1bdfb; }
    .booking-status.pending { background: rgba(255, 191, 0, 0.16); color: #ffe27d; }

    .booking-cost-label {
        color: rgba(237, 246, 255, 0.66);
        font-size: 13px;
    }

    .booking-cost {
        color: #8bdd4a;
        font-size: 22px;
        font-weight: 900;
    }

    .booking-buttons {
        display: grid;
        gap: 10px;
        width: 100%;
    }

    .booking-buttons a {
        display: inline-flex;
        padding: 12px 18px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 800;
        font-size: 13px;
        text-align: center;
        min-width: 100%;
        justify-content: center;
    }

    .booking-buttons .btn-secondary {
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
        background: transparent;
    }

    .booking-buttons .btn-primary {
        background: linear-gradient(135deg, #8bdd4a, #43b940);
        color: #07121f;
    }

    @media (max-width: 1040px) {
        .booking-card {
            grid-template-columns: 1fr;
        }

        .booking-card-actions {
            justify-items: stretch;
        }

        .booking-info-bar {
            grid-template-columns: 1fr;
            text-align: left;
        }

        .booking-info-btn {
            width: fit-content;
        }
    }

    @media (max-width: 720px) {
        .booking-topbar,
        .booking-actions,
        .booking-info-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .booking-sort,
        .booking-info-btn {
            width: 100%;
        }

        .booking-sort select {
            width: 100%;
        }
    }
</style>
