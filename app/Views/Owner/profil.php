<section class="owner-profil-page">
    <div class="owner-profil-hero">
        <div>
            <h1>Profil</h1>
            <p>Kelola informasi profil dan akun Anda</p>
        </div>
    </div>

    <section class="owner-profil-layout">
        <aside class="owner-profil-side">
            <article class="admin-panel owner-profil-card">
                <div class="owner-profil-avatar-wrap">
                    <img src="<?php echo e($profile['avatar']); ?>" alt="Foto profil <?php echo e($profile['name']); ?>">
                    <h2><?php echo e($profile['name']); ?></h2>
                    <span>Pemilik Lapangan</span>
                </div>

                <div class="owner-profil-contact-list">
                    <div class="owner-profil-contact-item">
                        <i class="fa-regular fa-envelope"></i>
                        <p>
                            <strong><?php echo e($profile['email']); ?></strong>
                            <small>Email</small>
                        </p>
                    </div>
                    <div class="owner-profil-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <p>
                            <strong><?php echo e($profile['phone']); ?></strong>
                            <small>No. Telepon</small>
                        </p>
                    </div>
                    <div class="owner-profil-contact-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <p>
                            <strong><?php echo e($profile['location']); ?></strong>
                            <small>Lokasi</small>
                        </p>
                    </div>
                    <div class="owner-profil-contact-item">
                        <i class="fa-regular fa-calendar-days"></i>
                        <p>
                            <strong>Bergabung sejak</strong>
                            <small><?php echo e($profile['joined']); ?></small>
                        </p>
                    </div>
                    <div class="owner-profil-contact-item">
                        <i class="fa-regular fa-credit-card"></i>
                        <p>
                            <strong>Total Lapangan</strong>
                            <small><?php echo e($profile['totalFields']); ?></small>
                        </p>
                    </div>
                </div>
            </article>

            <article class="admin-panel owner-profil-security-card">
                <h2><i class="fa-solid fa-lock"></i> Keamanan Akun</h2>
                <p>Terakhir login<br><strong><?php echo e($profile['lastLogin']); ?></strong></p>
                <button type="button">
                    <i class="fa-solid fa-key"></i>
                    <span>Ubah Password</span>
                </button>
            </article>
        </aside>

        <section class="owner-profil-main">
            <article class="admin-panel owner-profil-edit-panel">
                <div class="owner-profil-panel-header">
                    <h2>Edit Profil</h2>
                </div>

                <form class="owner-profil-form" action="#" method="post">
                    <label>
                        <span>Nama Lengkap</span>
                        <input type="text" value="<?php echo e($profile['name']); ?>">
                    </label>

                    <label>
                        <span>Email</span>
                        <input type="email" value="<?php echo e($profile['email']); ?>">
                    </label>

                    <label>
                        <span>No. Telepon</span>
                        <input type="text" value="<?php echo e($profile['phone']); ?>">
                    </label>

                    <label>
                        <span>Lokasi</span>
                        <input type="text" value="<?php echo e($profile['location']); ?>">
                    </label>

                    <label class="owner-profil-full">
                        <span>Tentang Saya</span>
                        <textarea rows="4"><?php echo e($profile['bio']); ?></textarea>
                    </label>

                    <div class="owner-profil-upload owner-profil-full">
                        <span>Foto Profil</span>
                        <div class="owner-profil-upload-row">
                            <img src="<?php echo e($profile['avatar']); ?>" alt="Preview foto profil">
                            <button type="button">
                                <i class="fa-regular fa-camera"></i>
                                <span>
                                    <strong>Klik untuk upload foto</strong>
                                    <small>PNG, JPG maks. 2MB</small>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="owner-profil-form-actions owner-profil-full">
                        <button type="button">
                            <i class="fa-regular fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </article>

            <article class="admin-panel owner-profil-fields-panel">
                <div class="owner-profil-table-header">
                    <h2>Lapangan yang Dikelola</h2>
                    <a href="<?php echo e(app_url('pemilik/lapangan')); ?>">
                        <span>Lihat Semua Lapangan</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="admin-table-responsive">
                    <table class="admin-table owner-profil-fields-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nama Lapangan</th>
                                <th>Jenis</th>
                                <th>Lokasi</th>
                                <th>Harga / Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($managedFields as $field): ?>
                                <tr>
                                    <td><img src="<?php echo e($field['image']); ?>" alt="<?php echo e($field['name']); ?>"></td>
                                    <td><?php echo e($field['name']); ?></td>
                                    <td><?php echo e($field['type']); ?></td>
                                    <td><?php echo e($field['location']); ?></td>
                                    <td><?php echo e($field['price']); ?></td>
                                    <td><span class="owner-profil-status"><?php echo e($field['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </section>
</section>
