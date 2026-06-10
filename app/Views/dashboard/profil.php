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
            <a href="<?php echo e(app_url('settings')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'settings' ? 'active' : ''; ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo">
            <p>Kelola informasi akun dan aktivitas kamu.</p>
            <small>Tinjau profil, statistik, dan prestasi game favoritmu.</small>
            <a href="#profil">Lihat Profil &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main">
        <section class="profile-topbar" id="profil">
            <div class="profile-hero-card">
                <div class="profile-hero-header">
                    <div class="profile-hero-avatar">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=260&auto=format&fit=crop" alt="Foto profil">
                        <button type="button" class="profile-photo-button">Ubah Foto</button>
                    </div>
                    <div class="profile-hero-meta">
                        <div class="profile-badge">Akun Terverifikasi</div>
                        <h1><?php echo e($userName); ?></h1>
                        <p><?php echo e($userEmail); ?></p>
                        <div class="profile-contact-row">
                            <span><?php echo e($userPhone); ?></span>
                            <span><?php echo e($userCity); ?></span>
                        </div>
                        <div class="profile-hero-actions">
                            <button type="button" class="btn-primary">Edit Profil</button>
                        </div>
                    </div>
                    <div class="profile-hero-stats-container">
                        <div class="profile-stats-header">
                            <h2>Aktivitas</h2>
                            <p>Ringkasan aktivitas dan riwayat booking Anda.</p>
                        </div>
                        <div class="profile-hero-stats">
                            <div class="stat-card">
                                <span>Total Booking</span>
                                <strong>28 Kali</strong>
                            </div>
                            <div class="stat-card">
                                <span>Booking Selesai</span>
                                <strong>24 Kali</strong>
                            </div>
                            <div class="stat-card">
                                <span>Total Pembayaran</span>
                                <strong>Rp2.140.000</strong>
                            </div>
                            <div class="stat-card">
                                <span>Member Sejak</span>
                                <strong>Mei 2024</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="profile-grid">
            <article class="profile-card profile-card-large">
                <div class="profile-card-header">
                    <div>
                        <h2>Informasi Pribadi</h2>
                        <p>Detail dasar profil dan kontak kamu.</p>
                    </div>
                </div>
                <div class="profile-card-body profile-data-grid">
                    <div>
                        <dt>Nama Lengkap</dt>
                        <dd><?php echo e($userName); ?></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><?php echo e($userEmail); ?></dd>
                    </div>
                    <div>
                        <dt>Nomor Handphone</dt>
                        <dd><?php echo e($userPhone); ?></dd>
                    </div>
                    <div>
                        <dt>Tanggal Lahir</dt>
                        <dd>15 Maret 1998</dd>
                    </div>
                    <div>
                        <dt>Jenis Kelamin</dt>
                        <dd>Laki-laki</dd>
                    </div>
                    <div>
                        <dt>Kota</dt>
                        <dd><?php echo e($userCity); ?></dd>
                    </div>
                    <div>
                        <dt>Alamat</dt>
                        <dd>Jl. Mattirotasi No. 12, Parepare, Sulawesi Selatan</dd>
                    </div>
                    <div>
                        <dt>Pekerjaan</dt>
                        <dd>Mahasiswa</dd>
                    </div>
                    <div>
                        <dt>Bio</dt>
                        <dd>Pecinta olahraga dan futsal. Selalu semangat bermain!</dd>
                    </div>
                </div>
            </article>

            <article class="profile-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Statistik Aktivitas</h2>
                        <p>Ringkasan performa dan riwayat booking.</p>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="stats-list">
                        <div>
                            <span>Total Booking</span>
                            <strong>28 Kali</strong>
                        </div>
                        <div>
                            <span>Booking Selesai</span>
                            <strong>24 Kali</strong>
                        </div>
                        <div>
                            <span>Total Pembayaran</span>
                            <strong>Rp2.140.000</strong>
                        </div>
                        <div>
                            <span>Member Sejak</span>
                            <strong>Mei 2024</strong>
                        </div>
                    </div>
                </div>
            </article>

            <article class="profile-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Preferensi Saya</h2>
                        <p>Pilih olahraga, waktu, dan radius favorit.</p>
                    </div>
                </div>
                <div class="profile-card-body profile-preferences">
                    <div>
                        <strong>Olahraga Favorit</strong>
                        <span>Futsal</span>
                    </div>
                    <div>
                        <strong>Waktu Favorit</strong>
                        <span>Sore - Malam</span>
                    </div>
                    <div>
                        <strong>Kota Favorit</strong>
                        <span>Parepare</span>
                    </div>
                    <div>
                        <strong>Radius Pencarian</strong>
                        <span>10 KM</span>
                    </div>
                </div>
            </article>

            <article class="profile-card social-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Media Sosial</h2>
                        <p>Hubungkan akun dan media sosial kamu.</p>
                    </div>
                </div>
                <div class="profile-card-body social-links">
                    <a href="#">Instagram <span>@ahmadfauzi_98</span></a>
                    <a href="#">Facebook <span>Ahmad Fauzi</span></a>
                    <a href="#">Twitter <span>@ahmadfauzi_98</span></a>
                </div>
            </article>

            <article class="profile-card achievement-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Pencapaian</h2>
                        <p>Prestasi loyalty dan aktivitas kamu.</p>
                    </div>
                </div>
                <div class="profile-card-body achievement-list">
                    <div class="badge-card green">
                        <strong>Player Aktif</strong>
                        <span>Melakukan 10 booking</span>
                        <small>10 Mei 2024</small>
                    </div>
                    <div class="badge-card blue">
                        <strong>Pelanggan Setia</strong>
                        <span>Melakukan 20 booking</span>
                        <small>22 Juni 2024</small>
                    </div>
                    <div class="badge-card purple">
                        <strong>Top Reviewer</strong>
                        <span>Memberikan 10 ulasan</span>
                        <small>15 Juli 2024</small>
                    </div>
                </div>
            </article>

            <article class="profile-card activity-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Aktivitas Terbaru</h2>
                        <p>Booking dan status terbaru Anda.</p>
                    </div>
                </div>
                <div class="profile-card-body activity-list">
                    <div class="activity-item">
                        <div>
                            <strong>Booking Arena Futsal Parepare</strong>
                            <span>22 Mei 2024 • 10:00 - 11:00</span>
                        </div>
                        <span class="status-complete">Selesai</span>
                    </div>
                    <div class="activity-item">
                        <div>
                            <strong>Booking Lapangan Badminton Center</strong>
                            <span>18 Mei 2024 • 08:00 - 09:00</span>
                        </div>
                        <span class="status-complete">Selesai</span>
                    </div>
                    <div class="activity-item">
                        <div>
                            <strong>Booking Mini Soccer Victory</strong>
                            <span>10 Mei 2024 • 17:00 - 18:00</span>
                        </div>
                        <span class="status-complete">Selesai</span>
                    </div>
                </div>
            </article>
        </section>
    </main>
</div>
