<section class="owner-profil-page">
    <div class="owner-profil-hero">
        <div>
            <h1>Profil</h1>
            <p>Kelola informasi profil dan akun Anda</p>
        </div>
    </div>

    <?php if (!empty($profileFlash)): ?>
        <div class="owner-profil-alert <?php echo e($profileFlash['type']); ?>" data-owner-profile-alert>
            <?php echo e($profileFlash['message']); ?>
        </div>
    <?php endif; ?>

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
                <details class="owner-profil-password-box">
                    <summary>
                        <i class="fa-solid fa-key"></i>
                        <span>Ubah Password</span>
                    </summary>

                    <form action="<?php echo e(app_url('pemilik/profil')); ?>" method="post" class="owner-profil-password-form">
                        <input type="hidden" name="profile_action" value="change_password">

                        <label>
                            <span>Password Lama</span>
                            <input type="password" name="current_password" autocomplete="current-password" required>
                        </label>

                        <label>
                            <span>Password Baru</span>
                            <input type="password" name="new_password" autocomplete="new-password" minlength="6" required>
                        </label>

                        <label>
                            <span>Konfirmasi Password</span>
                            <input type="password" name="confirm_password" autocomplete="new-password" minlength="6" required>
                        </label>

                        <button type="submit">
                            <i class="fa-solid fa-check"></i>
                            <span>Simpan Password</span>
                        </button>
                    </form>
                </details>
            </article>
        </aside>

        <section class="owner-profil-main">
            <article class="admin-panel owner-profil-edit-panel">
                <div class="owner-profil-panel-header">
                    <h2>Edit Profil</h2>
                </div>

                <form class="owner-profil-form" action="<?php echo e(app_url('pemilik/profil')); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="profile_action" value="update_profile">

                    <label>
                        <span>Nama Lengkap</span>
                        <input type="text" name="name" value="<?php echo e($profile['name']); ?>" autocomplete="name" required>
                    </label>

                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="<?php echo e($profile['email']); ?>" autocomplete="email" required>
                    </label>

                    <label>
                        <span>No. Telepon</span>
                        <input type="text" name="phone" value="<?php echo e($profile['phone']); ?>" autocomplete="tel">
                    </label>

                    <label>
                        <span>Lokasi</span>
                        <input type="text" name="location" value="<?php echo e($profile['location']); ?>">
                    </label>

                    <div class="owner-profil-upload owner-profil-full">
                        <span>Foto Profil</span>
                        <div class="owner-profil-upload-row">
                            <img src="<?php echo e($profile['avatar']); ?>" alt="Preview foto profil" data-owner-profile-preview>
                            <label class="owner-profil-upload-trigger">
                                <i class="fa-regular fa-camera"></i>
                                <span>
                                    <strong>Klik untuk upload foto</strong>
                                    <small>PNG, JPG maks. 2MB</small>
                                </span>
                                <input type="file" name="avatar" accept="image/png,image/jpeg" data-owner-profile-file>
                            </label>
                        </div>
                    </div>

                    <div class="owner-profil-form-actions owner-profil-full">
                        <button type="submit">
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

<script>
(function () {
    var fileInput = document.querySelector('[data-owner-profile-file]');
    var preview = document.querySelector('[data-owner-profile-preview]');
    var alert = document.querySelector('[data-owner-profile-alert]');

    if (alert) {
        alert.style.setProperty('--owner-alert-height', alert.scrollHeight + 'px');

        window.setTimeout(function () {
            alert.classList.add('is-hiding');

            window.setTimeout(function () {
                alert.setAttribute('hidden', 'hidden');
            }, 620);
        }, 4000);
    }

    if (!fileInput || !preview) {
        return;
    }

    fileInput.addEventListener('change', function () {
        var file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

        if (!file) {
            return;
        }

        preview.src = URL.createObjectURL(file);
    });
}());
</script>
