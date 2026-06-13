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

        <?php
            $totalBookings = count($bookings);
            $completedBookings = 0;
            $canceledBookings = 0;
            foreach ($bookings as $booking) {
                $status = isset($booking['status']) ? strtolower($booking['status']) : 'selesai';
                if ($status === 'selesai') {
                    $completedBookings++;
                } elseif ($status === 'dibatalkan') {
                    $canceledBookings++;
                }
            }
            $activeBookings = $totalBookings - $completedBookings - $canceledBookings;
        ?>

        <section class="history-page" id="booking-list">
            <div class="history-topbar">
                <div class="history-title">
                    <p>Telusuri riwayat booking, status, dan detail transaksi dengan tampilan yang lebih segar dan mudah dibaca.</p>
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

            <div class="history-summary-cards">
                <article class="history-summary-card summary-total">
                    <span>Total Booking</span>
                    <strong><?php echo e($totalBookings); ?></strong>
                    <p>Jumlah seluruh booking yang pernah kamu lakukan.</p>
                </article>
                <article class="history-summary-card summary-completed">
                    <span>Selesai</span>
                    <strong><?php echo e($completedBookings); ?></strong>
                    <p>Booking berhasil yang sudah selesai dimainkan.</p>
                </article>
                <article class="history-summary-card summary-canceled">
                    <span>Dibatalkan</span>
                    <strong><?php echo e($canceledBookings); ?></strong>
                    <p>Booking yang dibatalkan atau batal.</p>
                </article>
            </div>

            <div class="history-grid">
                <aside class="history-sidebar">
                    <div>
                        <h3>Ringkas & Interaktif</h3>
                        <p>Setiap entri menampilkan status, tanggal, lokasi, dan total pembayaran agar kamu bisa langsung menemukan transaksi penting.</p>
                    </div>
                    <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="btn-primary">Cari Lapangan Baru</a>
                </aside>

                <div class="history-list">
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                            $status = isset($booking['status']) ? strtolower($booking['status']) : 'selesai';
                            $statusLabel = $status === 'dibatalkan' ? 'Dibatalkan' : 'Selesai';
                            $statusClass = $status === 'dibatalkan' ? 'canceled' : 'finished';
                        ?>
                        <article class="history-card">
                            <div class="history-card-top">
                                <div class="history-card-badge">
                                    <span class="history-status <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
                                </div>
                                <div class="history-card-price"><?php echo e($booking['price']); ?></div>
                            </div>

                            <div class="history-card-body">
                                <div class="history-card-media">
                                    <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                                    <span class="history-tag"><?php echo e($booking['type']); ?></span>
                                </div>

                                <div class="history-card-details">
                                    <h3><?php echo e($booking['venue']); ?></h3>
                                    <p class="history-location"><?php echo e($booking['location']); ?></p>

                                    <div class="history-meta">
                                        <span>&#128197; <?php echo e($booking['date']); ?></span>
                                        <span>&#9201; <?php echo e($booking['time']); ?></span>
                                        <span>&#9711; <?php echo e($booking['duration']); ?></span>
                                    </div>

                                    <div class="history-code">Kode Booking <strong><?php echo e($booking['code']); ?></strong></div>
                                </div>

                                <div class="history-card-actions">
                                    <div class="history-action-note">Detail transaksi lengkap tersedia di halaman booking.</div>
                                    <a href="#" class="btn-small">Lihat Detail</a>
                                </div>
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
    .history-title { max-width: 720px; display:grid; gap:10px; }
    .history-title small { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:999px; background: rgba(139, 232, 70, 0.16); color:#8bdd4a; font-weight:800; font-size:13px; letter-spacing:0.3px; }
    .history-title h2 { color:#fff; margin:0; font-size:34px; line-height:1.1; }
    .history-title p { color: rgba(237,246,255,0.76); margin:0; max-width:620px; font-size:15px; }
    .history-actions { display:flex; gap:16px; align-items:center; flex-wrap:wrap; }
    .history-tabs { display:flex; gap:10px; flex-wrap:wrap; }
    .history-tab { min-width:fit-content; border-radius:999px; padding:12px 22px; background: rgba(255,255,255,0.03); color: rgba(237,246,255,0.82); font-weight:700; border:1px solid rgba(255,255,255,0.08); cursor:pointer; transition: transform 0.18s ease, background 0.2s ease, color 0.2s ease; }
    .history-tab:hover { transform: translateY(-1px); }
    .history-tab.active { background: linear-gradient(135deg,#8bdd4a,#43b940); color:#07121f; border-color:transparent; }
    .history-sort { display:flex; align-items:center; gap:10px; }
    .history-sort label { color: rgba(237,246,255,0.72); font-size:13px; }
    .history-sort select { min-width:180px; padding:12px 16px; border-radius:16px; border:1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.06); color:#fff; outline:none; }
    .history-sort select option { background: #07101e; color:#fff; }

    .history-summary-cards { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:18px; margin:24px 0 14px; }
    .history-summary-card { position:relative; overflow:hidden; padding:26px 24px; border-radius:24px; background: rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); min-height:150px; display:grid; gap:12px; }
    .history-summary-card::after { content: ''; position:absolute; top:0; right:-40px; width:160px; height:160px; background: rgba(139,232,74,0.15); border-radius:50%; filter: blur(40px); }
    .history-summary-card span { color: rgba(237,246,255,0.72); text-transform: uppercase; letter-spacing:0.06em; font-size:12px; font-weight:700; }
    .history-summary-card strong { font-size:40px; color:#fff; line-height:1; }
    .history-summary-card p { color: rgba(237,246,255,0.72); font-size:14px; line-height:1.7; }
    .summary-completed::after { background: rgba(72,196,72,0.18); }
    .summary-canceled::after { background: rgba(255,90,90,0.18); }

    .history-grid { display:grid; grid-template-columns: 1.5fr 0.45fr; gap:22px; }
    .history-sidebar { padding:28px 24px; border-radius:24px; border:1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); display:flex; flex-direction:column; justify-content:space-between; min-height:420px; }
    .history-sidebar h3 { color:#fff; margin-bottom:16px; font-size:24px; }
    .history-sidebar p { color: rgba(237,246,255,0.72); line-height:1.85; font-size:15px; }
    .history-sidebar .btn-primary { display:inline-flex; align-items:center; justify-content:center; padding:15px 22px; border-radius:16px; background: linear-gradient(135deg, #8bdd4a, #43b940); color: #07121f; font-weight:900; text-decoration:none; margin-top:18px; }

    .history-list { display:grid; gap:20px; }
    .history-card { padding:26px; border-radius:24px; background: linear-gradient(160deg, rgba(8,18,32,0.98), rgba(12,28,52,0.96)); border:1px solid rgba(123,229,125,0.14); box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18); }
    .history-card-top { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:22px; }
    .history-card-badge { display:flex; align-items:center; gap:10px; }
    .history-card-price { color:#8bdd4a; font-size:20px; font-weight:900; }
    .history-card-body { display:grid; grid-template-columns: 220px 1fr; gap:18px; align-items:center; }
    .history-card-media { position:relative; overflow:hidden; border-radius:22px; min-height:190px; }
    .history-card-media img { width:100%; height:100%; object-fit:cover; display:block; }
    .history-tag { position:absolute; top:16px; left:16px; display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:999px; background: rgba(72,196,72,0.2); color: #8bdd4a; font-weight:800; font-size:12px; }

    .history-card-details { display:grid; gap:12px; }
    .history-card-details h3 { margin:0; color:#fff; font-size:24px; }
    .history-location { margin:0; color: rgba(237,246,255,0.74); font-size:14px; }
    .history-meta { display:flex; flex-wrap:wrap; gap:10px; margin-top:4px; }
    .history-meta span { display:inline-flex; align-items:center; gap:10px; padding:12px 16px; border-radius:16px; background: rgba(255,255,255,0.04); color: rgba(237,246,255,0.72); font-size:13px; }
    .history-code { margin-top:6px; color:#8bdd4a; font-weight:700; font-size:14px; }

    .history-card-actions { display:grid; gap:12px; justify-items:end; }
    .history-action-note { color: rgba(237,246,255,0.68); font-size:13px; text-align:right; }
    .history-status { padding:12px 18px; border-radius:18px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; font-size:13px; }
    .history-status.finished { background: rgba(72,196,72,0.18); color:#8bdd4a; }
    .history-status.canceled { background: rgba(255,90,90,0.16); color:#ff7b7b; }
    .history-cost-label { color: rgba(237,246,255,0.66); font-size:13px; }
    .history-cost { color:#8bdd4a; font-size:22px; font-weight:900; }
    .btn-small { text-decoration:none; padding:12px 20px; border-radius:16px; border:1px solid rgba(255,255,255,0.14); background: rgba(255,255,255,0.04); color:#fff; display:inline-flex; transition: transform 0.15s ease, background 0.2s ease; }
    .btn-small:hover { transform: translateY(-1px); background: rgba(255,255,255,0.08); }

    @media (max-width:1040px) {
        .history-grid { grid-template-columns: 1fr; }
        .history-card-body { grid-template-columns: 1fr; }
        .history-card-actions { justify-items:start; }
        .history-actions { width:100%; justify-content:space-between; }
    }

    @media (max-width:720px) {
        .history-topbar { flex-direction:column; }
        .history-actions { width:100%; justify-content:flex-start; }
        .history-sort { width:100%; }
        .history-sort select { width:100%; }
        .history-summary-cards { grid-template-columns: 1fr; }
        .history-card-body { grid-template-columns: 1fr; }
        .history-card-top { flex-direction:column; align-items:flex-start; }
        .history-card-actions { justify-items:flex-start; }
    }
</style>
