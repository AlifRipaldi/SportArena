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
                <p>Riwayat</p>
                <h1>Lihat semua riwayat booking lapangan kamu</h1>
            </div>
            <div class="dashboard-actions">
                <button type="button" class="icon-button" aria-label="Notifikasi">&#128276;</button>
                <div class="dashboard-user">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="history-page" id="booking-list">
            <div class="history-topbar">
                <div class="history-title">
                    <small>Riwayat</small>
                    <h2>Lihat semua riwayat booking lapangan kamu</h2>
                    <p>Berikut adalah riwayat semua booking lapangan yang pernah kamu lakukan. Gunakan tab untuk memfilter berdasarkan status.</p>
                </div>
                <div class="history-actions">
                    <div class="history-tabs">
                        <button class="history-tab active" type="button">Semua</button>
                        <button class="history-tab" type="button">Selesai</button>
                        <button class="history-tab" type="button">Dibatalkan</button>
                    </div>
                    <div class="history-sort">
                        <label for="sort-history">Urutkan:</label>
                        <select id="sort-history" name="sort_history">
                            <option value="latest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="price_high">Harga Tertinggi</option>
                            <option value="price_low">Harga Terendah</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="history-info-bar">
                <div class="history-info-icon">&#128197;</div>
                <p>Berikut adalah riwayat semua booking lapangan kamu.</p>
            </div>

            <div class="history-grid">
                <aside class="history-sidebar">
                    <div>
                        <h3>Butuh konfirmasi?</h3>
                        <p>Periksa detail, kode booking, dan status pembayaran di setiap entri. Kamu juga dapat membuka detail untuk melihat ringkasan transaksi.</p>
                    </div>
                    <a href="#" class="btn-primary">Booking Sekarang</a>
                </aside>

                <div class="history-list">
                    <?php foreach ($bookings as $booking): ?>
                        <article class="history-card">
                            <div class="history-card-left">
                                <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                                <span class="history-tag"><?php echo e($booking['type']); ?></span>
                            </div>
                            <div class="history-card-mid">
                                <h3><?php echo e($booking['venue']); ?></h3>
                                <p class="history-location"><?php echo e($booking['location']); ?></p>

                                <div class="history-meta">
                                    <span>&#128197; <?php echo e($booking['date']); ?></span>
                                    <span>&#9201; <?php echo e($booking['time']); ?></span>
                                    <span>&#9711; <?php echo e($booking['duration']); ?></span>
                                </div>

                                <div class="history-code">Kode Booking <strong><?php echo e($booking['code']); ?></strong></div>
                            </div>
                            <div class="history-card-right">
                                <?php if(isset($booking['status']) && strtolower($booking['status']) === 'dibatalkan'): ?>
                                    <span class="history-status canceled">Dibatalkan</span>
                                <?php elseif(isset($booking['status']) && strtolower($booking['status']) === 'selesai'): ?>
                                    <span class="history-status finished">Selesai</span>
                                <?php else: ?>
                                    <span class="history-status upcoming">Selesai</span>
                                <?php endif; ?>

                                <div class="history-cost-label">Total Pembayaran</div>
                                <div class="history-cost"><?php echo e($booking['price']); ?></div>
                                <a href="#" class="btn-small">Lihat Detail</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
    .history-page { margin-top: 28px; }
    .history-topbar { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; flex-wrap:wrap; }
    .history-title { max-width: 720px; display:grid; gap:8px; }
    .history-title small { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:999px; background: rgba(139, 232, 70, 0.16); color:#8bdd4a; font-weight:800; font-size:13px; }
    .history-title h2 { color:#fff; margin:0; font-size:32px; }
    .history-title p { color: rgba(237,246,255,0.72); margin:0; max-width:620px; }
    .history-actions { display:flex; gap:16px; align-items:center; flex-wrap:wrap; }
    .history-tabs { display:flex; gap:10px; flex-wrap:wrap; }
    .history-tab { min-width:fit-content; border-radius:999px; padding:12px 20px; background: rgba(255,255,255,0.03); color: rgba(237,246,255,0.8); font-weight:700; border:1px solid rgba(255,255,255,0.06); cursor:pointer; }
    .history-tab.active { background: linear-gradient(135deg,#8bdd4a,#43b940); color:#07121f; border-color:transparent; }
    .history-sort { display:flex; align-items:center; gap:10px; }
    .history-sort label { color: rgba(237,246,255,0.72); font-size:13px; }
    .history-sort select { min-width:180px; padding:10px 14px; border-radius:14px; border:1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.05); color:#fff; }

    .history-info-bar { display:flex; align-items:center; gap:14px; padding:18px 22px; border-radius:18px; border:1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); margin:18px 0 22px; }
    .history-info-icon { width:42px; height:42px; display:grid; place-items:center; border-radius:16px; background: rgba(139, 232, 70, 0.16); color: #8bdd4a; font-size:18px; }
    .history-info-bar p { margin:0; color: rgba(237,246,255,0.72); }

    .history-grid { display:grid; grid-template-columns: 1.5fr 0.45fr; gap:22px; }
    .history-sidebar { padding:24px; border-radius:22px; border:1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); display:flex; flex-direction:column; justify-content:space-between; min-height:380px; }
    .history-sidebar h3 { color:#fff; margin-bottom:14px; font-size:22px; }
    .history-sidebar p { color: rgba(237,246,255,0.72); line-height:1.75; }
    .history-sidebar .btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:14px; background: linear-gradient(135deg, #8bdd4a, #43b940); color: #07121f; font-weight:900; text-decoration:none; }

    .history-list { display:grid; gap:18px; }
    .history-card { display:grid; grid-template-columns: 220px minmax(0,1fr) 220px; gap:18px; align-items:center; padding:22px; border-radius:18px; border:1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); }
    .history-card-left { position:relative; overflow:hidden; border-radius:18px; }
    .history-card-left img { width:100%; height:170px; object-fit:cover; display:block; }
    .history-tag { position:absolute; top:16px; left:16px; display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:999px; background: rgba(72,196,72,0.18); color: #8bdd4a; font-weight:800; font-size:12px; }

    .history-card-mid { display:grid; gap:12px; }
    .history-card-mid h3 { margin:0; color:#fff; font-size:22px; }
    .history-location { margin:0; color: rgba(237,246,255,0.7); font-size:14px; }
    .history-meta { display:flex; flex-wrap:wrap; gap:10px; color: rgba(237,246,255,0.68); }
    .history-meta span { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:14px; background: rgba(255,255,255,0.04); font-size:13px; }
    .history-code { margin-top:8px; color:#8bdd4a; font-weight:700; font-size:13px; }

    .history-card-right { display:grid; gap:10px; justify-items:end; }
    .history-status { padding:10px 16px; border-radius:14px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; }
    .history-status.finished { background: rgba(72,196,72,0.14); color:#8bdd4a; }
    .history-status.canceled { background: rgba(255,90,90,0.12); color:#ff7b7b; }
    .history-cost-label { color: rgba(237,246,255,0.66); font-size:13px; }
    .history-cost { color:#8bdd4a; font-size:22px; font-weight:900; }
    .btn-small { text-decoration:none; padding:12px 18px; border-radius:14px; border:1px solid rgba(255,255,255,0.12); color:#fff; display:inline-flex; }

    @media (max-width:1040px) {
        .history-grid { grid-template-columns: 1fr; }
        .history-card { grid-template-columns: 1fr; }
        .history-card-right { justify-items:stretch; }
        .history-actions { width:100%; justify-content:space-between; }
    }

    @media (max-width:720px) {
        .history-topbar { flex-direction:column; }
        .history-actions { width:100%; justify-content:flex-start; }
        .history-sort { width:100%; }
        .history-sort select { width:100%; }
    }
</style>
