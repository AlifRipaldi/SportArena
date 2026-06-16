<?php
$users = array(
    array(
        'name' => 'Ahmad Fauzi',
        'email' => 'ahmad@email.com',
        'phone' => '081234567890',
        'role' => 'User',
        'roleClass' => 'success',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'registered' => '15 Mei 2024',
    ),
    array(
        'name' => 'Siti Aminah',
        'email' => 'siti@email.com',
        'phone' => '082345678901',
        'role' => 'Pemilik',
        'roleClass' => 'info',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'registered' => '10 April 2024',
    ),
    array(
        'name' => 'Budi Santoso',
        'email' => 'budi@email.com',
        'phone' => '083456789012',
        'role' => 'User',
        'roleClass' => 'success',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'registered' => '05 Mei 2024',
    ),
    array(
        'name' => 'Dinda Putri',
        'email' => 'dinda@email.com',
        'phone' => '084567890123',
        'role' => 'User',
        'roleClass' => 'success',
        'status' => 'Nonaktif',
        'statusClass' => 'warning',
        'registered' => '20 Maret 2024',
    ),
);

$activeUsers = 0;
$ownerUsers = 0;
$inactiveUsers = 0;

foreach ($users as $user) {
    if ($user['status'] === 'Aktif') {
        $activeUsers++;
    }

    if ($user['role'] === 'Pemilik') {
        $ownerUsers++;
    }

    if ($user['status'] === 'Nonaktif') {
        $inactiveUsers++;
    }
}
?>

<section class="admin-hero">
    <div>
        <h1>Manajemen User</h1>
        <p>Kelola semua pengguna dalam sistem Arena Sport</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary" type="button"><i class="fa-solid fa-plus"></i> Tambah User</button>
    </div>
</section>

<section class="admin-summary-grid" aria-label="Ringkasan user">
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon lime"><i class="fa-solid fa-users"></i></span>
        <div>
            <p>Total User</p>
            <strong><?php echo count($users); ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon green"><i class="fa-solid fa-user-check"></i></span>
        <div>
            <p>User Aktif</p>
            <strong><?php echo $activeUsers; ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon blue"><i class="fa-solid fa-building-user"></i></span>
        <div>
            <p>Pemilik</p>
            <strong><?php echo $ownerUsers; ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon gold"><i class="fa-solid fa-user-slash"></i></span>
        <div>
            <p>Nonaktif</p>
            <strong><?php echo $inactiveUsers; ?></strong>
        </div>
    </article>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <label class="admin-filter-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari user..." class="admin-search-input" aria-label="Cari user">
        </label>
        <select class="admin-filter-select" aria-label="Filter role user">
            <option>Role: Semua</option>
            <option>Admin</option>
            <option>Pemilik</option>
            <option>User</option>
        </select>
        <select class="admin-filter-select" aria-label="Filter status user">
            <option>Status: Semua</option>
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
            <span>Halaman 1 dari 10</span>
            <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </article>
</div>
