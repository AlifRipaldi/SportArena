<div class="dashboard-shell profile-dashboard search-dashboard">
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
            <a href="#lapangan-populer">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main search-main">
        <section class="profile-page-head search-page-head">
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

        <section class="field-search-panel" aria-label="Filter pencarian lapangan">
            <div class="field-search-grid top">
                <label class="field-search-control location">
                    <span>Cari Lokasi</span>
                    <div>
                        <i>&#9906;</i>
                        <input type="text" placeholder="Contoh: Jakarta, Bandung, Surabaya">
                        <button type="button" aria-label="Gunakan lokasi saat ini">&#9881;</button>
                    </div>
                </label>
                <label class="field-search-control">
                    <span>Jenis Olahraga</span>
                    <select>
                        <option>Semua Olahraga</option>
                        <option>Futsal</option>
                        <option>Badminton</option>
                        <option>Mini Soccer</option>
                        <option>Basketball</option>
                    </select>
                </label>
                <label class="field-search-control">
                    <span>Tanggal</span>
                    <div>
                        <i>&#128197;</i>
                        <input type="text" placeholder="Pilih Tanggal">
                    </div>
                </label>
            </div>

            <div class="field-search-grid bottom">
                <label class="field-search-control">
                    <span>Waktu</span>
                    <div>
                        <i>&#9201;</i>
                        <select>
                            <option>Pilih Waktu</option>
                            <option>08:00 - 09:00</option>
                            <option>10:00 - 11:00</option>
                            <option>18:00 - 19:00</option>
                        </select>
                    </div>
                </label>
                <label class="field-search-control price">
                    <span>Rentang Harga</span>
                    <div class="field-price-row">
                        <input type="number" placeholder="Min">
                        <small>Rp</small>
                        <b>-</b>
                        <input type="number" placeholder="Max">
                        <small>Rp</small>
                    </div>
                </label>
                <label class="field-search-control">
                    <span>Fasilitas</span>
                    <select>
                        <option>Pilih Fasilitas</option>
                        <option>Parkir</option>
                        <option>Musholla</option>
                        <option>Toilet</option>
                        <option>Kantin</option>
                    </select>
                </label>
                <button type="button" class="field-filter-button"><span>&#9776;</span>Filter Lainnya</button>
            </div>

            <div class="field-active-filters">
                <span>Filter Aktif:</span>
                <button type="button">Futsal <i>&#215;</i></button>
                <button type="button">Jakarta Selatan <i>&#215;</i></button>
                <button type="button">22 Mei 2024 <i>&#215;</i></button>
                <button type="button">10:00 - 11:00 <i>&#215;</i></button>
                <a href="#"><span>&#128465;</span>Hapus Semua</a>
            </div>
        </section>

        <section class="field-results-head">
            <p><strong>12</strong> Lapangan Ditemukan</p>
            <label class="field-sort-control">
                <span>Urutkan:</span>
                <select>
                    <option>Terdekat</option>
                    <option>Harga Terendah</option>
                    <option>Rating Tertinggi</option>
                </select>
            </label>
        </section>

        <section id="lapangan-populer" class="field-result-list" aria-label="Daftar lapangan ditemukan">
            <?php foreach ($venues as $index => $venue): ?>
                <?php
                    $sportTypes = array('Futsal', 'Badminton', 'Mini Soccer');
                    $featureSets = array(
                        array('Futsal', 'Parkir', 'Musholla', 'Toilet', '+2'),
                        array('Badminton', 'Parkir', 'Musholla', 'Kantin'),
                        array('Mini Soccer', 'Parkir', 'Toilet', 'Kantin', '+1'),
                    );
                    $distances = array('1.2 km', '2.4 km', '2.8 km');
                    $type = isset($venue['type']) ? $venue['type'] : $sportTypes[$index % count($sportTypes)];
                    $features = isset($venue['features']) ? $venue['features'] : $featureSets[$index % count($featureSets)];
                    $distance = isset($venue['distance']) ? $venue['distance'] : $distances[$index % count($distances)];
                ?>
                <article class="field-result-card">
                    <div class="field-result-media">
                        <img src="<?php echo e($venue['image']); ?>" alt="<?php echo e($venue['name']); ?>">
                        <span>Populer</span>
                        <button type="button" aria-label="Tambah <?php echo e($venue['name']); ?> ke favorit">&#9825;</button>
                    </div>

                    <div class="field-result-content">
                        <h2><?php echo e($venue['name']); ?></h2>
                        <p class="field-result-location"><span>&#9906;</span><?php echo e($venue['location']); ?></p>
                        <div class="field-result-tags">
                            <?php foreach ($features as $feature): ?>
                                <span><?php echo e($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="field-result-rating">
                            <span class="star">&#9733;</span>
                            <span><?php echo e($venue['rating']); ?> (<?php echo e($venue['reviews']); ?>)</span>
                            <i></i>
                            <span>&#9201; <?php echo e($distance); ?></span>
                        </div>
                    </div>

                    <div class="field-result-price">
                        <small>Harga Mulai Dari</small>
                        <strong><?php echo e($venue['price']); ?> <span>/jam</span></strong>
                        <div>
                            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Lihat Detail</a>
                            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="primary">Pilih Jadwal</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</div>
