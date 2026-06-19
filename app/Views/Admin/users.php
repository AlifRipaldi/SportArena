<?php
$users = isset($users) && is_array($users) ? $users : array();
$defaultUserStats = array(
    'total' => count($users),
    'active' => 0,
    'owners' => 0,
    'inactive' => 0,
);
$userStats = isset($userStats) && is_array($userStats) ? array_merge($defaultUserStats, $userStats) : $defaultUserStats;
?>

<section class="admin-hero">
    <div>
        <h1>Kelola Customer</h1>
        <p>Kelola semua customer dan pemilik lapangan dalam sistem Arena Sport</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary" type="button"><i class="fa-solid fa-plus"></i> Tambah Customer</button>
    </div>
</section>

<section class="admin-summary-grid" aria-label="Ringkasan user">
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon lime"><i class="fa-solid fa-users"></i></span>
        <div>
            <p>Total Customer</p>
            <strong><?php echo e($userStats['total']); ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon green"><i class="fa-solid fa-user-check"></i></span>
        <div>
            <p>Customer Aktif</p>
            <strong><?php echo e($userStats['active']); ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon blue"><i class="fa-solid fa-building-user"></i></span>
        <div>
            <p>Pemilik</p>
            <strong><?php echo e($userStats['owners']); ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon gold"><i class="fa-solid fa-user-slash"></i></span>
        <div>
            <p>Nonaktif</p>
            <strong><?php echo e($userStats['inactive']); ?></strong>
        </div>
    </article>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <label class="admin-filter-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari customer..." class="admin-search-input" aria-label="Cari customer">
        </label>
        <select class="admin-filter-select" aria-label="Filter role customer">
            <option>Semua</option>
            <option>Pemilik</option>
            <option>Customer</option>
        </select>
        <select class="admin-filter-select" aria-label="Filter status user">
            <option></option>
            <option>Aktif</option>
            <option>Nonaktif</option>
        </select>
    </div>

    <article class="admin-panel admin-full-width">
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7">Belum ada data customer di database.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($user['name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($user['email']); ?></td>
                            <td><?php echo e($user['phone']); ?></td>
                            <td><span class="admin-badge <?php echo e($user['roleClass']); ?>"><?php echo e($user['role']); ?></span></td>
                            <td><span class="admin-badge <?php echo e($user['statusClass']); ?>"><?php echo e($user['status']); ?></span></td>
                            <td><?php echo e($user['registered']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-icon" type="button" title="Edit <?php echo e($user['name']); ?>" aria-label="Edit <?php echo e($user['name']); ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn-icon danger" type="button" title="Hapus <?php echo e($user['name']); ?>" aria-label="Hapus <?php echo e($user['name']); ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <button class="admin-pagination-btn" type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
            <span>Menampilkan <?php echo e(count($users)); ?> data</span>
            <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </article>
</div>
