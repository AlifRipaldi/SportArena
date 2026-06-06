<section class="hero">
    <div class="container hero-inner">
        <div class="hero-copy">
            <span class="hero-label">Booking Lapangan</span>
            <h1>Booking Lapangan Jadi <span>Lebih Mudah</span></h1>
            <p>Temukan berbagai pilihan lapangan olahraga terbaik di Kota Parepare dan booking sesuai jadwalmu.</p>
            <div class="hero-buttons">
                <a href="#lapangan" class="btn-cta">Cari Lapangan</a>
            </div>
        </div>
        <div class="hero-info">
            <div class="info-card">
                <div>
                    <h3>4.8/5</h3>
                    <p>Rating Pengguna</p>
                </div>
            </div>
            <div class="info-card small">
                <p>Lebih dari 120+ pengguna telah booking hari ini</p>
            </div>
        </div>
    </div>
</section>

<section class="feature-row">
    <div class="container feature-grid">
        <div class="feature-item">
            <h4>Booking Cepat</h4>
            <p>Pilih, pesan, beres.</p>
        </div>
        <div class="feature-item">
            <h4>Aman & Terpercaya</h4>
            <p>Transaksi aman setiap saat.</p>
        </div>
        <div class="feature-item">
            <h4>Banyak Pilihan</h4>
            <p>Lapangan terdekat dan terbaik.</p>
        </div>
        <div class="feature-item">
            <h4>Layanan 24/7</h4>
            <p>Dukungan pelanggan selalu siap.</p>
        </div>
    </div>
</section>

<section class="search-panel container">
    <div class="search-card">
        <div class="search-field">
            <label>Jenis Olahraga</label>
            <select>
                <option>Semua</option>
                <option>Futsal</option>
                <option>Badminton</option>
                <option>Mini Soccer</option>
                <option>Basket</option>
            </select>
        </div>
        <div class="search-field">
            <label>Lokasi</label>
            <select>
                <option>Semua Lokasi</option>
                <option>Parepare</option>
                <option>Ujung Pandang</option>
            </select>
        </div>
        <div class="search-field">
            <label>Tanggal</label>
            <input type="date">
        </div>
        <div class="search-action">
            <button class="btn-cta">Cari Lapangan</button>
        </div>
    </div>
</section>

<section id="lapangan" class="container content">
    <div class="section-header">
        <div>
            <p class="section-tag">POPULAR</p>
            <h2>Lapangan Populer</h2>
            <p>Beberapa lapangan favorit dengan fasilitas terbaik di Kota Parepare.</p>
        </div>
        <a href="<?php echo e(app_url('lapangan')); ?>" class="view-all">Lihat Semua -&gt;</a>
    </div>

    <?php if ($dataError): ?>
        <p><?php echo e($dataError); ?></p>
    <?php endif; ?>

    <div class="grid-container">
        <?php foreach ($lapangan as $row): ?>
            <div class="card popular-card">
                <div class="card-detail">
                    <span class="tag"><?php echo e($row['Jenis_olahraga']); ?></span>
                    <h4><?php echo e($row['Nama_lapangan']); ?></h4>
                    <p>Jl. <?php echo e($row['Lokasi']); ?></p>
                    <div class="card-meta">
                        <span>* 4.8</span>
                        <span>
                            <?php
                            echo isset($row['Harga'])
                                ? 'Rp' . number_format((int) $row['Harga'], 0, ',', '.') . '/jam'
                                : 'Mulai dari Rp50.000/jam';
                            ?>
                        </span>
                    </div>
                    <a href="<?php echo e(app_url('public/booking.php?id=' . rawurlencode($row['ID_Lapangan']))); ?>" class="btn-book">Pilih Jadwal</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="cara-kerja" class="how-it-works container">
    <h2>Cara Kerja</h2>
    <div class="work-grid">
        <div class="work-step">
            <h3>1</h3>
            <p>Pilih tipe lapangan yang Anda inginkan.</p>
        </div>
        <div class="work-step">
            <h3>2</h3>
            <p>Pesan jadwal dengan mudah dan cepat.</p>
        </div>
        <div class="work-step">
            <h3>3</h3>
            <p>Bayar dan nikmati lapangan favorit Anda.</p>
        </div>
    </div>
</section>

<section id="tentang-kami" class="about container">
    <div class="about-card">
        <h2>Tentang Kami</h2>
        <p>Arena Sport hadir untuk memudahkan booking lapangan olahraga di Kota Parepare. Kami menyediakan pilihan lapangan terbaik dengan pengalaman booking yang cepat dan aman.</p>
    </div>
</section>

<section id="kontak" class="contact container">
    <div class="contact-card">
        <h2>Kontak</h2>
        <p>Butuh bantuan? Hubungi kami untuk informasi dan bantuan pemesanan.</p>
        <p>Email: support@arenasport.co.id</p>
        <p>Telepon: 0812-3456-7890</p>
    </div>
</section>
