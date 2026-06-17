<?php
$settingMeta = array(
    'umum' => array('title' => 'Pengaturan Umum', 'description' => 'Kelola konfigurasi dasar sistem Arena Sport.'),
    'notifikasi' => array('title' => 'Pengaturan Notifikasi', 'description' => 'Kelola notifikasi sistem dan preferensi pemberitahuan.'),
    'pembayaran' => array('title' => 'Pengaturan Pembayaran', 'description' => 'Kelola metode dan konfigurasi pembayaran.'),
    'keamanan' => array('title' => 'Pengaturan Keamanan', 'description' => 'Kelola keamanan sistem dan aktivitas akun.'),
    'akun' => array('title' => 'Akun Administrator', 'description' => 'Kelola informasi akun administrator dan keamanan login.'),
);
$currentSetting = isset($settingMeta[$activeSettingTab]) ? $settingMeta[$activeSettingTab] : $settingMeta['umum'];
?>

<section class="admin-settings-hero">
    <div>
        <nav class="admin-settings-breadcrumb" aria-label="Breadcrumb pengaturan">
            <span>Pengaturan</span>
            <i class="fa-solid fa-chevron-right"></i>
            <strong><?php echo e($activeSettingTab === 'akun' ? 'Akun' : $currentSetting['title']); ?></strong>
        </nav>
        <h1><?php echo e($currentSetting['title']); ?></h1>
        <p><?php echo e($currentSetting['description']); ?></p>
    </div>
    <button class="admin-settings-help" type="button">
        <i class="fa-regular fa-circle-question"></i>
        <span>Bantuan</span>
    </button>
</section>

<nav class="admin-settings-tabs" aria-label="Kategori pengaturan">
    <?php foreach ($settingTabs as $tab): ?>
        <a class="<?php echo $activeSettingTab === $tab['key'] ? 'active' : ''; ?>" href="<?php echo e(app_url('admin/pengaturan?tab=' . $tab['key'])); ?>">
            <i class="fa-solid <?php echo e($tab['icon']); ?>"></i>
            <?php echo e($tab['label']); ?>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($activeSettingTab === 'akun'): ?>
    <section class="admin-account-grid">
        <article class="admin-panel admin-account-card admin-account-profile-card">
            <div class="admin-account-card-title">
                <i class="fa-regular fa-user"></i>
                <h2>Informasi Profil</h2>
            </div>

            <div class="admin-account-profile-content">
                <div class="admin-account-avatar-editor">
                    <div class="admin-account-avatar">
                        <span><?php echo e($adminAccountProfile['initials']); ?></span>
                        <button type="button" aria-label="Upload foto baru">
                            <i class="fa-regular fa-camera"></i>
                        </button>
                    </div>
                    <strong>Upload foto baru</strong>
                    <small>PNG, JPG maks. 2MB</small>
                    <button class="admin-account-delete-photo" type="button">
                        <i class="fa-regular fa-trash-can"></i>
                        <span>Hapus Foto</span>
                    </button>
                </div>

                <form class="admin-account-profile-form" action="#" method="post">
                    <label>
                        <span>Nama Lengkap</span>
                        <input type="text" value="<?php echo e($adminAccountProfile['name']); ?>">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" value="<?php echo e($adminAccountProfile['email']); ?>">
                    </label>
                    <label>
                        <span>Nomor Telepon</span>
                        <input type="text" value="<?php echo e($adminAccountProfile['phone']); ?>">
                    </label>
                    <label>
                        <span>Username</span>
                        <input type="text" value="<?php echo e($adminAccountProfile['username']); ?>">
                    </label>
                    <label>
                        <span>Role</span>
                        <input type="text" value="<?php echo e($adminAccountProfile['role']); ?>" readonly>
                    </label>
                    <div class="admin-account-form-action">
                        <button type="button">
                            <i class="fa-regular fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </article>

        <article class="admin-panel admin-account-card">
            <div class="admin-account-card-title">
                <i class="fa-solid fa-shield-halved"></i>
                <h2>Keamanan Akun</h2>
            </div>

            <form class="admin-account-password-form" action="#" method="post">
                <label>
                    <span>Password Saat Ini</span>
                    <span class="admin-account-password-input">
                        <input type="password" value="passwordlama">
                        <button type="button" aria-label="Tampilkan password saat ini"><i class="fa-regular fa-eye"></i></button>
                    </span>
                </label>
                <label>
                    <span>Password Baru</span>
                    <span class="admin-account-password-input">
                        <input type="password" value="passwordbaru">
                        <button type="button" aria-label="Tampilkan password baru"><i class="fa-regular fa-eye"></i></button>
                    </span>
                </label>
                <label>
                    <span>Konfirmasi Password Baru</span>
                    <span class="admin-account-password-input">
                        <input type="password" value="passwordbaru">
                        <button type="button" aria-label="Tampilkan konfirmasi password"><i class="fa-regular fa-eye"></i></button>
                    </span>
                </label>

                <div class="admin-account-centered-action">
                    <button type="button">
                        <i class="fa-solid fa-lock"></i>
                        <span>Ganti Password</span>
                    </button>
                </div>
            </form>
        </article>

        <article class="admin-panel admin-account-card">
            <div class="admin-account-card-title">
                <i class="fa-solid fa-lock"></i>
                <h2>Pengaturan Login</h2>
            </div>

            <div class="admin-account-login-settings">
                <?php foreach ($adminLoginSettings as $setting): ?>
                    <div class="admin-account-login-setting">
                        <div>
                            <strong><?php echo e($setting['label']); ?></strong>
                            <small><?php echo e($setting['description']); ?></small>
                        </div>
                        <label class="admin-account-switch" aria-label="<?php echo e($setting['label']); ?>">
                            <input type="checkbox" <?php echo $setting['enabled'] ? 'checked' : ''; ?>>
                            <span data-on="ON" data-off="OFF"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="admin-panel admin-account-card">
            <div class="admin-account-card-title">
                <i class="fa-regular fa-clock"></i>
                <h2>Aktivitas Login Terakhir</h2>
            </div>

            <div class="admin-account-activity-list">
                <?php foreach ($adminLoginActivity as $activity): ?>
                    <div class="admin-account-activity-row">
                        <span><?php echo e($activity['label']); ?></span>
                        <strong><?php echo e($activity['value']); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-account-centered-action">
                <button type="button">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Lihat Riwayat Login</span>
                </button>
            </div>
        </article>

        <article class="admin-panel admin-account-card">
            <div class="admin-account-card-title">
                <i class="fa-solid fa-shield-halved"></i>
                <h2>Hak Akses Administrator</h2>
            </div>

            <div class="admin-account-access-grid">
                <?php foreach ($adminAccessRights as $right): ?>
                    <span><i class="fa-regular fa-circle-check"></i> <?php echo e($right); ?></span>
                <?php endforeach; ?>
            </div>

            <div class="admin-account-centered-action">
                <button type="button">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Lihat Detail Hak Akses</span>
                </button>
            </div>
        </article>

        <article class="admin-panel admin-account-card">
            <div class="admin-account-card-title admin-account-device-title">
                <span><i class="fa-solid fa-desktop"></i> Perangkat Aktif</span>
                <em>2 Aktif</em>
            </div>

            <div class="admin-account-device-list">
                <?php foreach ($adminActiveDevices as $device): ?>
                    <article class="admin-account-device-item">
                        <span class="admin-account-device-icon">
                            <i class="fa-solid <?php echo e($device['icon']); ?>"></i>
                        </span>
                        <div>
                            <strong>
                                <?php echo e($device['device']); ?>
                                <?php if ($device['current']): ?>
                                    <em>Perangkat Saat Ini</em>
                                <?php endif; ?>
                            </strong>
                            <small><?php echo e($device['ip']); ?><br><?php echo e($device['location']); ?></small>
                        </div>
                        <time><?php echo $device['time']; ?></time>
                        <button type="button" aria-label="Menu perangkat"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="admin-account-logout-devices">
                <button type="button">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Logout Semua Perangkat</span>
                </button>
            </div>
        </article>
    </section>
<?php elseif ($activeSettingTab === 'notifikasi'): ?>
    <section class="admin-settings-grid admin-notification-settings-grid">
        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Pengaturan Notifikasi</h2>
                <p>Kelola preferensi notifikasi yang ingin Anda terima.</p>
            </div>

            <div class="admin-notification-section-title">
                <h3>Saluran Notifikasi</h3>
                <p>Pilih saluran untuk menerima notifikasi.</p>
            </div>

            <div class="admin-notification-channel-list">
                <?php foreach ($notificationChannels as $channel): ?>
                    <div class="admin-notification-channel">
                        <span class="admin-notification-icon <?php echo e($channel['accent']); ?>">
                            <i class="fa-solid <?php echo e($channel['icon']); ?>"></i>
                        </span>
                        <div>
                            <strong><?php echo e($channel['label']); ?></strong>
                            <small><?php echo e($channel['description']); ?></small>
                        </div>
                        <label class="admin-switch" aria-label="<?php echo e($channel['label']); ?>">
                            <input type="checkbox" <?php echo $channel['enabled'] ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-notification-section-title has-spacing">
                <h3>Pengaturan Jenis Notifikasi</h3>
                <p>Pilih jenis notifikasi yang ingin Anda terima.</p>
            </div>

            <div class="admin-notification-type-list">
                <?php foreach ($notificationTypes as $type): ?>
                    <label class="admin-notification-type">
                        <span>
                            <strong><?php echo e($type['label']); ?></strong>
                            <small><?php echo e($type['description']); ?></small>
                        </span>
                        <input type="checkbox" <?php echo $type['enabled'] ? 'checked' : ''; ?>>
                        <i class="fa-solid fa-check"></i>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="admin-settings-save">
                <button class="btn-primary" type="button">Simpan Preferensi</button>
            </div>
        </article>

        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Contoh Notifikasi</h2>
                <p>Preview tampilan notifikasi yang akan Anda terima.</p>
            </div>

            <div class="admin-notification-preview-list">
                <?php foreach ($notificationPreviews as $preview): ?>
                    <article class="admin-notification-preview">
                        <span class="admin-notification-icon <?php echo e($preview['accent']); ?>">
                            <i class="fa-solid <?php echo e($preview['icon']); ?>"></i>
                        </span>
                        <div>
                            <strong><?php echo e($preview['title']); ?></strong>
                            <p><?php echo e($preview['description']); ?></p>
                        </div>
                        <time><?php echo e($preview['time']); ?></time>
                    </article>
                <?php endforeach; ?>
            </div>
        </article>
    </section>
<?php elseif ($activeSettingTab === 'pembayaran'): ?>
    <section class="admin-settings-grid admin-payment-settings-grid">
        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Metode Pembayaran</h2>
                <p>Kelola metode pembayaran yang tersedia untuk user.</p>
            </div>

            <div class="admin-admin-payment-list">
                <?php foreach ($adminPaymentMethods as $method): ?>
                    <div class="admin-admin-payment-method">
                        <span class="admin-admin-payment-logo <?php echo e($method['accent']); ?>"><?php echo e($method['mark']); ?></span>
                        <div>
                            <strong><?php echo e($method['name']); ?> <em>Aktif</em></strong>
                            <small><?php echo e($method['description']); ?></small>
                        </div>
                        <label class="admin-switch" aria-label="<?php echo e($method['name']); ?>">
                            <input type="checkbox" <?php echo $method['enabled'] ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-settings-card-head admin-payment-config-head">
                <h2>Pengaturan Pembayaran</h2>
                <p>Atur konfigurasi umum terkait pembayaran.</p>
            </div>

            <div class="admin-payment-config-list">
                <?php foreach ($adminPaymentSettings as $setting): ?>
                    <div class="admin-payment-config-item">
                        <div>
                            <strong><?php echo e($setting['label']); ?></strong>
                            <small><?php echo e($setting['description']); ?></small>
                        </div>

                        <?php if ($setting['type'] === 'select'): ?>
                            <select aria-label="<?php echo e($setting['label']); ?>">
                                <?php foreach ($setting['options'] as $option): ?>
                                    <option <?php echo $option === $setting['value'] ? 'selected' : ''; ?>><?php echo e($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" value="<?php echo e($setting['value']); ?>" aria-label="<?php echo e($setting['label']); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-settings-save">
                <button class="btn-primary" type="button">Simpan Perubahan</button>
            </div>
        </article>

        <article class="admin-panel admin-settings-card admin-bank-panel">
            <div class="admin-payment-panel-head">
                <div>
                    <h2>Daftar Rekening Bank</h2>
                    <p>Kelola daftar rekening bank untuk metode pembayaran transfer.</p>
                </div>
                <button class="btn-primary" type="button"><i class="fa-solid fa-plus"></i> Tambah Rekening</button>
            </div>

            <div class="admin-table-responsive">
                <table class="admin-table admin-bank-table">
                    <thead>
                        <tr>
                            <th>Nama Bank</th>
                            <th>No. Rekening</th>
                            <th>Atas Nama</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminBankAccounts as $account): ?>
                            <tr>
                                <td>
                                    <span class="admin-bank-name">
                                        <span class="admin-bank-logo <?php echo e($account['accent']); ?>"><?php echo e(substr($account['bank'], 0, 3)); ?></span>
                                        <?php echo e($account['bank']); ?>
                                    </span>
                                </td>
                                <td><?php echo e($account['account']); ?></td>
                                <td><?php echo e($account['owner']); ?></td>
                                <td><span class="admin-badge <?php echo e($account['statusClass']); ?>"><?php echo e($account['status']); ?></span></td>
                                <td>
                                    <div class="admin-actions">
                                        <button class="btn-icon" type="button" title="Edit rekening <?php echo e($account['bank']); ?>" aria-label="Edit rekening <?php echo e($account['bank']); ?>">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn-icon danger" type="button" title="Hapus rekening <?php echo e($account['bank']); ?>" aria-label="Hapus rekening <?php echo e($account['bank']); ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-bank-footer">
                <div class="admin-bank-length">
                    <span>Tampilkan</span>
                    <select aria-label="Jumlah data rekening">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span>data</span>
                </div>
                <div class="admin-pagination-pages">
                    <button class="admin-pagination-btn" type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="admin-page-number active" type="button">1</button>
                    <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </article>
    </section>
<?php elseif ($activeSettingTab === 'keamanan'): ?>
    <section class="admin-settings-grid admin-security-settings-grid">
        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Pengaturan Keamanan</h2>
                <p>Kelola keamanan akun dan sistem Anda.</p>
            </div>

            <div class="admin-security-setting-list">
                <?php foreach ($securitySettings as $setting): ?>
                    <div class="admin-security-setting-item">
                        <span class="admin-notification-icon <?php echo e($setting['accent']); ?>">
                            <i class="fa-solid <?php echo e($setting['icon']); ?>"></i>
                        </span>
                        <div>
                            <strong>
                                <?php echo e($setting['label']); ?>
                                <?php if (!empty($setting['status'])): ?>
                                    <em><?php echo e($setting['status']); ?></em>
                                <?php endif; ?>
                            </strong>
                            <small><?php echo e($setting['description']); ?></small>
                        </div>

                        <?php if ($setting['type'] === 'toggle'): ?>
                            <label class="admin-switch" aria-label="<?php echo e($setting['label']); ?>">
                                <input type="checkbox" <?php echo !empty($setting['enabled']) ? 'checked' : ''; ?>>
                                <span></span>
                            </label>
                        <?php elseif ($setting['type'] === 'button'): ?>
                            <button class="admin-security-action" type="button"><?php echo e($setting['button']); ?></button>
                        <?php else: ?>
                            <div class="admin-security-verified">
                                <span><?php echo e($setting['email']); ?></span>
                                <i class="fa-solid fa-check"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Aktivitas Keamanan Terbaru</h2>
                <p>Riwayat aktivitas keamanan pada akun Anda.</p>
            </div>

            <div class="admin-security-activity-list">
                <?php foreach ($securityActivities as $activity): ?>
                    <article class="admin-security-activity-item">
                        <span class="admin-notification-icon <?php echo e($activity['accent']); ?>">
                            <i class="fa-solid <?php echo e($activity['icon']); ?>"></i>
                        </span>
                        <div>
                            <strong><?php echo e($activity['title']); ?></strong>
                            <small><?php echo e($activity['description']); ?></small>
                        </div>
                        <time>
                            <?php echo e($activity['date']); ?><br>
                            <?php echo e($activity['time']); ?>
                        </time>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="admin-security-view-all">
                <button type="button">Lihat Semua Aktivitas</button>
            </div>
        </article>
    </section>

    <section class="admin-panel admin-active-session-panel">
        <div class="admin-settings-card-head">
            <h2>Pengaturan Sesi Aktif</h2>
            <p>Perangkat yang saat ini sedang login ke akun Anda.</p>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table admin-session-table">
                <thead>
                    <tr>
                        <th>Perangkat</th>
                        <th>Browser</th>
                        <th>Lokasi / IP</th>
                        <th>Terakhir Aktif</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeSessions as $session): ?>
                        <tr>
                            <td>
                                <div class="admin-session-device">
                                    <span class="admin-session-icon <?php echo e($session['accent']); ?>">
                                        <i class="fa-solid <?php echo e($session['icon']); ?>"></i>
                                    </span>
                                    <div>
                                        <strong>
                                            <?php echo e($session['device']); ?>
                                            <?php if ($session['current']): ?>
                                                <em>Perangkat Saat Ini</em>
                                            <?php endif; ?>
                                        </strong>
                                        <?php if ($session['type'] !== ''): ?>
                                            <small><?php echo e($session['type']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($session['browser']); ?></td>
                            <td><?php echo e($session['location']); ?><br><?php echo e($session['ip']); ?></td>
                            <td><?php echo $session['lastActive']; ?></td>
                            <td><span class="admin-badge success"><?php echo e($session['status']); ?></span></td>
                            <td>
                                <?php if ($session['current']): ?>
                                    <span class="admin-session-empty-action">-</span>
                                <?php else: ?>
                                    <button class="admin-session-delete" type="button">Hapus</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php else: ?>
    <section class="admin-settings-grid">
        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Informasi Sistem</h2>
                <p>Informasi dasar mengenai sistem dan kontak admin.</p>
            </div>

            <form class="admin-settings-form" action="#" method="post">
                <label>
                    <span>Nama Aplikasi</span>
                    <input type="text" value="Arena Sport" aria-label="Nama aplikasi">
                </label>

                <label>
                    <span>Deskripsi</span>
                    <input type="text" value="Platform booking lapangan olahraga online." aria-label="Deskripsi aplikasi">
                </label>

                <label>
                    <span>Email Admin</span>
                    <input type="email" value="admin@arenasport.com" aria-label="Email admin">
                </label>

                <label>
                    <span>Nomor Kontak Admin</span>
                    <input type="text" value="0812-3456-7890" aria-label="Nomor kontak admin">
                </label>

                <label>
                    <span>Alamat</span>
                    <input type="text" value="Jl. Olahraga No. 123, Parepare, Sulawesi Selatan" aria-label="Alamat admin">
                </label>

                <div class="admin-settings-save">
                    <button class="btn-primary" type="button">Simpan Perubahan</button>
                </div>
            </form>
        </article>

        <article class="admin-panel admin-settings-card">
            <div class="admin-settings-card-head">
                <h2>Pengaturan Umum</h2>
                <p>Kelola berbagai pengaturan sistem.</p>
            </div>

            <div class="admin-settings-toggle-list">
                <?php foreach ($generalSettings as $setting): ?>
                    <div class="admin-settings-toggle-item">
                        <div>
                            <strong><?php echo e($setting['label']); ?></strong>
                            <span><?php echo e($setting['description']); ?></span>
                        </div>
                        <label class="admin-switch" aria-label="<?php echo e($setting['label']); ?>">
                            <input type="checkbox" <?php echo $setting['enabled'] ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>
<?php endif; ?>

<?php if ($activeSettingTab !== 'akun'): ?>
    <section class="admin-panel admin-system-info-panel">
        <div class="admin-settings-card-head">
            <h2>Informasi Sistem</h2>
        </div>

        <div class="admin-system-info-grid">
            <?php foreach ($systemInfo as $info): ?>
                <article class="admin-system-info-card">
                    <span class="admin-system-info-icon <?php echo e($info['accent']); ?>">
                        <i class="fa-solid <?php echo e($info['icon']); ?>"></i>
                    </span>
                    <div>
                        <p><?php echo e($info['label']); ?></p>
                        <strong><?php echo $info['value']; ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
