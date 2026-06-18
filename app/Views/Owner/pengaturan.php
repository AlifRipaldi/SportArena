<section class="owner-settings-page">
    <div class="owner-settings-hero">
        <div>
            <h1>Pengaturan</h1>
            <p>Kelola preferensi akun, aplikasi, dan bisnis Anda</p>
        </div>
    </div>

    <section class="owner-settings-layout">
        <aside class="owner-settings-side">
            <article class="admin-panel owner-settings-account-card">
                <h2>Akun Saya</h2>

                <div class="owner-settings-account-head">
                    <img src="<?php echo e($profile['avatar']); ?>" alt="Foto profil <?php echo e($profile['name']); ?>">
                    <div>
                        <strong><?php echo e($profile['name']); ?></strong>
                        <span>Pemilik Lapangan</span>
                    </div>
                </div>

                <div class="owner-settings-account-contact">
                    <p><?php echo e($profile['email']); ?></p>
                    <p><?php echo e($profile['phone']); ?></p>
                </div>

                <a class="owner-settings-action-link" href="<?php echo e(app_url('pemilik/profil')); ?>">
                    <i class="fa-regular fa-id-card"></i>
                    <span>Ubah Profil</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </article>

            <article class="admin-panel owner-settings-help-card">
                <h2>Pusat Bantuan</h2>

                <div class="owner-settings-help-list">
                    <?php foreach ($helpItems as $item): ?>
                        <a href="<?php echo e($item['url']); ?>">
                            <span class="owner-settings-help-icon">
                                <i class="fa-solid <?php echo e($item['icon']); ?>"></i>
                            </span>
                            <strong><?php echo e($item['label']); ?></strong>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="owner-settings-support-box">
                    <strong>Butuh bantuan?</strong>
                    <p>Tim support kami siap membantu Anda</p>
                    <button type="button">
                        <i class="fa-solid fa-headset"></i>
                        <span>Hubungi Support</span>
                    </button>
                </div>
            </article>
        </aside>

        <section class="owner-settings-main">
            <?php foreach ($settingsGroups as $group): ?>
                <article class="admin-panel owner-settings-panel">
                    <h2><?php echo e($group['title']); ?></h2>

                    <div class="owner-settings-list">
                        <?php foreach ($group['items'] as $item): ?>
                            <a class="owner-settings-item" href="<?php echo e($item['url']); ?>">
                                <span class="owner-settings-item-icon">
                                    <i class="fa-solid <?php echo e($item['icon']); ?>"></i>
                                </span>
                                <span class="owner-settings-item-copy">
                                    <strong><?php echo e($item['label']); ?></strong>
                                    <small><?php echo e($item['description']); ?></small>
                                </span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </section>
</section>
