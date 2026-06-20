<?php
$totalLapangan = is_array($lapangan) ? count($lapangan) : 0;
$sportImages = array(
    'futsal' => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?q=80&w=1000&auto=format&fit=crop',
    'badminton' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=1000&auto=format&fit=crop',
    'mini-soccer' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1000&auto=format&fit=crop',
    'basket' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=1000&auto=format&fit=crop',
    'default' => 'https://images.unsplash.com/photo-1518605336396-d31032230006?q=80&w=1000&auto=format&fit=crop',
);
$displayLapangan = is_array($lapangan) ? array_slice($lapangan, 0, 4) : array();
$sportSlug = function ($name) {
    $slug = strtolower(trim((string) $name));
    $slug = str_replace(array(' ', '_'), '-', $slug);

    return $slug === '' ? 'default' : $slug;
};
?>

<section id="beranda" class="home-hero">
    <div class="container home-hero-inner">
        <div class="home-hero-copy">
            <h1>Booking Lapangan Jadi <span>Lebih Mudah</span></h1>
            <p>Temukan berbagai pilihan lapangan olahraga terbaik di Kota Parepare dan booking sesuai jadwalmu.</p>

            <div class="home-benefits" aria-label="Keunggulan Arena Sport">
                <div class="home-benefit">
                    <span><i class="fa-regular fa-calendar-check"></i></span>
                    <div>
                        <strong>Booking Cepat</strong>
                        <small>Pilih, pesan, beres</small>
                    </div>
                </div>
                <div class="home-benefit">
                    <span><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <strong>Aman & Terpercaya</strong>
                        <small>Transaksi aman</small>
                    </div>
                </div>
                <div class="home-benefit">
                    <span><i class="fa-solid fa-location-dot"></i></span>
                    <div>
                        <strong>Banyak Pilihan</strong>
                        <small>Lapangan terdekat</small>
                    </div>
                </div>
            </div>

            <form class="home-search" action="#lapangan" method="get" aria-label="Cari lapangan">
                <label class="home-search-field" for="sport-type">
                    <span>Jenis Olahraga</span>
                    <select id="sport-type" name="jenis">
                        <option value="">Semua</option>
                        <option value="futsal">Futsal</option>
                        <option value="badminton">Badminton</option>
                        <option value="mini-soccer">Mini Soccer</option>
                        <option value="basket">Basket</option>
                    </select>
                </label>
                <label class="home-search-field" for="field-location">
                    <span>Lokasi</span>
                    <select id="field-location" name="lokasi">
                        <option value="">Semua Lokasi</option>
                        <option value="parepare">Parepare</option>
                        <option value="ujung-pandang">Ujung Pandang</option>
                    </select>
                </label>
                <label class="home-search-field" for="booking-date">
                    <span>Tanggal</span>
                    <input id="booking-date" name="tanggal" type="date" aria-label="Pilih tanggal">
                </label>
                <button class="home-search-button" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Cari Lapangan
                </button>
            </form>
        </div>

        <div class="home-proof" aria-label="Ringkasan rating dan booking">
            <div class="home-proof-card rating-card">
                <span class="proof-icon"><i class="fa-solid fa-star"></i></span>
                <div>
                    <strong>4.8/5</strong>
                    <small>Rating Pengguna</small>
                </div>
            </div>
            <div class="home-proof-card users-card">
                <div class="avatar-stack" aria-hidden="true">
                    <span>R</span>
                    <span>A</span>
                    <span>N</span>
                    <span>S</span>
                    <span>+120</span>
                </div>
                <strong>Lebih dari 120+ pengguna</strong>
                <small>telah booking hari ini</small>
            </div>
        </div>
    </div>
</section>

<section id="lapangan" class="home-popular container">
    <div class="home-section-header">
        <div>
            <p class="section-tag">POPULAR</p>
            <h2>Lapangan Populer</h2>
            <p>Beberapa lapangan favorit dengan fasilitas terbaik di Kota Parepare.</p>
        </div>
        <a href="<?php echo e(app_url('lapangan')); ?>" class="view-all">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <?php if ($dataError): ?>
        <p class="data-alert"><?php echo e($dataError); ?></p>
    <?php endif; ?>

    <div class="home-field-grid">
        <?php foreach ($displayLapangan as $row): ?>
            <?php
            $sportKey = $sportSlug(isset($row['Jenis_olahraga']) ? $row['Jenis_olahraga'] : '');
            $coverImage = isset($sportImages[$sportKey]) ? $sportImages[$sportKey] : $sportImages['default'];
            $priceText = isset($row['Harga'])
                ? 'Rp' . number_format((int) $row['Harga'], 0, ',', '.')
                : 'Rp50.000';
            $ratingText = number_format(isset($row['Rating_avg']) ? (float) $row['Rating_avg'] : 0, 1) . ' (' . (isset($row['Review_count']) ? (int) $row['Review_count'] : 0) . ')';
            $bookingUrl = app_url('dashboard/lapangan');
            ?>
            <article class="home-field-card">
                <a class="field-card-link" href="<?php echo e($bookingUrl); ?>">
                    <div class="field-cover">
                        <img src="<?php echo e($coverImage); ?>" alt="<?php echo e($row['Nama_lapangan']); ?>" loading="lazy">
                        <span class="field-tag <?php echo e($sportKey); ?>"><?php echo e($row['Jenis_olahraga']); ?></span>
                        <span class="field-heart" aria-hidden="true"><i class="fa-regular fa-heart"></i></span>
                    </div>
                    <div class="field-body">
                        <h3><?php echo e($row['Nama_lapangan']); ?></h3>
                        <p><i class="fa-solid fa-location-dot"></i> Jl. <?php echo e($row['Lokasi']); ?></p>
                        <div class="field-meta">
                            <span><i class="fa-solid fa-star"></i> <?php echo e($ratingText); ?></span>
                            <span><small>Mulai dari</small> <?php echo e($priceText); ?> <em>/jam</em></span>
                        </div>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
        <?php if (empty($displayLapangan)): ?>
            <p class="data-alert">Belum ada lapangan aktif dengan jadwal tersedia.</p>
        <?php endif; ?>
    </div>

    <div class="home-trust-strip" aria-label="Keunggulan layanan">
        <div>
            <i class="fa-solid fa-stopwatch"></i>
            <span><strong>Mudah & Cepat</strong><small>Booking dalam hitungan menit</small></span>
        </div>
        <div>
            <i class="fa-solid fa-shield-halved"></i>
            <span><strong>Pembayaran Aman</strong><small>Data dan transaksi terjamin</small></span>
        </div>
        <div>
            <i class="fa-solid fa-headset"></i>
            <span><strong>Layanan 24/7</strong><small>Customer support selalu siap</small></span>
        </div>
        <div>
            <i class="fa-solid fa-trophy"></i>
            <span><strong>Lapangan Berkualitas</strong><small>Fasilitas terbaik di Parepare</small></span>
        </div>
    </div>
</section>

<section id="cara-kerja" class="how-it-works container">
    <div class="section-header compact">
        <div>
            <p class="section-tag">ALUR BOOKING</p>
            <h2>Cara Kerja</h2>
            <p>Tiga langkah sederhana untuk mendapatkan jadwal lapangan yang kamu butuhkan.</p>
        </div>
    </div>
    <div class="work-grid">
        <div class="work-step">
            <h3>1</h3>
            <h4>Pilih Lapangan</h4>
            <p>Cari tipe olahraga, lokasi, dan fasilitas yang paling pas.</p>
        </div>
        <div class="work-step">
            <h3>2</h3>
            <h4>Tentukan Jadwal</h4>
            <p>Pilih jam bermain yang tersedia dan isi data pemesanan.</p>
        </div>
        <div class="work-step">
            <h3>3</h3>
            <h4>Datang & Main</h4>
            <p>Selesaikan pembayaran, lalu nikmati sesi olahraga favoritmu.</p>
        </div>
    </div>
</section>

<section id="tentang-kami" class="about container">
    <div class="about-card">
        <div>
            <p class="section-tag">TENTANG ARENA SPORT</p>
            <h2>Platform booking lapangan untuk pemain dan pengelola.</h2>
        </div>
        <p>Arena Sport hadir untuk memudahkan booking lapangan olahraga di Kota Parepare. Kami menghubungkan pengguna dengan pilihan lapangan terbaik melalui pengalaman pemesanan yang cepat, jelas, dan aman.</p>
        <div class="about-highlights">
            <span>Jadwal mudah dicek</span>
            <span>Harga transparan</span>
            <span>Booking lebih rapi</span>
        </div>
    </div>
</section>

<section id="kontak" class="contact container">
    <div class="contact-card">
        <div>
            <p class="section-tag">KONTAK</p>
            <h2>Butuh bantuan booking?</h2>
            <p>Hubungi tim Arena Sport untuk informasi lapangan, jadwal, dan bantuan pemesanan.</p>
        </div>
        <div class="contact-list">
            <a href="mailto:support@arenasport.co.id">support@arenasport.co.id</a>
            <a href="tel:081234567890">0812-3456-7890</a>
        </div>
    </div>
</section>
