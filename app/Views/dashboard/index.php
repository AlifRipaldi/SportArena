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
            <a href="<?php echo e(app_url('dashboard')); ?>"><span>&#8962;</span>Dashboard</a>
            <a class="active" href="#"><span>&#128269;</span>Cari Lapangan</a>
            <a href="#"><span>&#128197;</span>Booking Saya</a>
            <a href="#"><span>&#9825;</span>Favorit</a>
            <a href="#"><span>&#9201;</span>Riwayat</a>
            <a href="#"><span>&#9734;</span>Ulasan Saya</a>
            <a href="#"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="#lapangan-populer">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main">
        <section class="dashboard-topbar search-topbar">
            <div>
                <p>Cari Lapangan</p>
                <h1>Temukan lapangan terbaik di sekitar kamu</h1>
            </div>
            <div class="dashboard-actions">
                <button type="button" class="icon-button" aria-label="Notifikasi">&#128276;</button>
                <div class="dashboard-user">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="search-panel">
            <div class="search-field-row">
                <label class="search-field">
                    <span>Cari Lokasi</span>
                    <div>
                        <span>📍</span>
                        <input type="text" placeholder="Contoh: Jakarta, Bandung, Surabaya">
                    </div>
                </label>
                <label class="search-field">
                    <span>Jenis Olahraga</span>
                    <select>
                        <option>Semua Olahraga</option>
                        <option>Futsal</option>
                        <option>Badminton</option>
                        <option>Mini Soccer</option>
                    </select>
                </label>
                <label class="search-field">
                    <span>Tanggal</span>
                    <input type="date">
                </label>
            </div>

            <div class="search-field-row">
                <label class="search-field">
                    <span>Waktu</span>
                    <select>
                        <option>Pilih Waktu</option>
                        <option>08:00 - 09:00</option>
                        <option>10:00 - 11:00</option>
                        <option>18:00 - 19:00</option>
                    </select>
                </label>
                <label class="search-field price-field">
                    <span>Rentang Harga</span>
                    <div class="price-range">
                        <input type="number" placeholder="Min">
                        <span>Rp</span>
                        <input type="number" placeholder="Max">
                        <span>Rp</span>
                    </div>
                </label>
                <label class="search-field">
                    <span>Fasilitas</span>
                    <select>
                        <option>Pilih Fasilitas</option>
                        <option>Parkir</option>
                        <option>Musholla</option>
                        <option>Toilet</option>
                    </select>
                </label>
                <button type="button" class="btn-filter-big">Filter Lainnya</button>
            </div>

            <div class="active-filters">
                <span class="filter-pill">Futsal <button type="button">×</button></span>
                <span class="filter-pill">Jakarta Selatan <button type="button">×</button></span>
                <span class="filter-pill">22 Mei 2024 <button type="button">×</button></span>
                <span class="filter-pill">10:00 - 11:00 <button type="button">×</button></span>
                <button type="button" class="clear-filters">Hapus Semua</button>
            </div>
        </section>

        <section class="search-results-header">
            <div>
                <strong>12</strong>
                <span>Lapangan Ditemukan</span>
            </div>
            <div class="sort-row">
                <label>Urutkan:</label>
                <select>
                    <option>Terdekat</option>
                    <option>Harga Terendah</option>
                    <option>Rating Tertinggi</option>
                </select>
            </div>
        </section>

        <section class="dashboard-section" id="lapangan-populer">
            <?php foreach ($stats as $stat): ?>
                <article class="stat-card <?php echo e($stat['accent']); ?>">
                    <div class="stat-icon"><?php echo $stat['icon']; ?></div>
                    <strong><?php echo e($stat['value']); ?></strong>
                    <p><?php echo e($stat['label']); ?></p>
                    <a href="#">Lihat detail &#8594;</a>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="lapangan-populer" class="dashboard-section">
            <div class="dashboard-section-heading">
                <h2>Lapangan Populer</h2>
                <a href="#">Lihat semua &#8594;</a>
            </div>

            <div class="venue-grid">
                <?php foreach ($venues as $venue): ?>
                    <article class="venue-card">
                        <div class="venue-image" style="background-image: url('<?php echo e($venue['image']); ?>');">
                            <span>Populer</span>
                            <button type="button" aria-label="Tambah favorit">&#9825;</button>
                        </div>
                        <div class="venue-info">
                            <h3><?php echo e($venue['name']); ?></h3>
                            <p>&#9906; <?php echo e($venue['location']); ?></p>
                            <div class="venue-rating">
                                <span>&#9733; <?php echo e($venue['rating']); ?></span>
                                <small>(<?php echo e($venue['reviews']); ?>)</small>
                            </div>
                            <strong><?php echo e($venue['price']); ?> <small>/jam</small></strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-section-heading">
                <h2>Booking Terdekat</h2>
                <a href="#">Lihat semua &#8594;</a>
            </div>

            <article class="upcoming-booking">
                <img src="<?php echo e($nextBooking['image']); ?>" alt="Lapangan booking terdekat">
                <div class="booking-detail">
                    <h3><?php echo e($nextBooking['venue']); ?></h3>
                    <p><span>&#128197;</span><?php echo e($nextBooking['date']); ?></p>
                    <p><span>&#9201;</span><?php echo e($nextBooking['time']); ?></p>
                    <p><span>&#9711;</span><?php echo e($nextBooking['duration']); ?></p>
                </div>
                <div class="booking-actions">
                    <span><?php echo e($nextBooking['status']); ?></span>
                    <a href="#">Lihat Detail</a>
                </div>
            </article>
        </section>
    </main>
</div>
