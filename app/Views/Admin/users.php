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
        <button class="btn-primary" type="button" data-dialog-open="userCreateDialog"><i class="fa-solid fa-plus"></i> Tambah Pengguna</button>
        <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/users')); ?>"><i class="fa-solid fa-download"></i> Export</a>
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
    <div class="admin-filter-bar" data-admin-filter="#adminUserRows tr[data-filter-text]">
        <label class="admin-filter-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari customer..." class="admin-search-input" aria-label="Cari customer">
        </label>
        <select class="admin-filter-select" aria-label="Filter role customer">
            <option value="">Semua Role</option>
            <option value="Pemilik">Pemilik</option>
            <option value="Customer">Customer</option>
        </select>
        <select class="admin-filter-select" aria-label="Filter status user">
            <option value="">Semua Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Nonaktif</option>
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
                <tbody id="adminUserRows">
                    <?php if (empty($users)): ?>
                        <tr data-filter-text="<?php echo e(implode(' ', array($user['name'], $user['email'], $user['phone'], $user['role'], $user['status']))); ?>">
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
                                    <button class="btn-icon" type="button" title="Edit <?php echo e($user['name']); ?>" aria-label="Edit <?php echo e($user['name']); ?>" data-dialog-open="userEditDialog" data-payload="<?php echo e(json_encode(array('id_user' => $user['id'], 'nama' => $user['name'], 'email' => $user['email'], 'telepon' => $user['phone'] === '-' ? '' : $user['phone'], 'role' => strtolower($user['role']), 'status' => $user['status']))); ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <?php if ($user['status'] === 'Aktif'): ?><form class="admin-inline-form" action="<?php echo e(app_url('admin/users/hapus')); ?>" method="post" data-confirm="Nonaktifkan <?php echo e($user['name']); ?>? Riwayat pengguna tetap disimpan."><input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_user" value="<?php echo e($user['id']); ?>"><button class="btn-icon danger" type="submit" title="Nonaktifkan <?php echo e($user['name']); ?>"><i class="fa-solid fa-user-slash"></i></button></form><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>Menampilkan <?php echo e(count($users)); ?> data</span>
        </div>
    </article>
</div>

<dialog class="admin-dialog" id="userCreateDialog">
    <div class="admin-dialog-head"><h2>Tambah Pengguna</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/users/tambah')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>">
        <label><span>Nama</span><input name="nama" required maxlength="120"></label><label><span>Email</span><input type="email" name="email" required maxlength="160"></label>
        <label><span>Telepon</span><input name="telepon" required maxlength="50"></label><label><span>Password awal</span><input type="password" name="password" required minlength="8"></label>
        <label><span>Role</span><select name="role" required><option value="customer">Customer</option><option value="pemilik">Pemilik</option></select></label><label><span>Nama usaha (untuk pemilik)</span><input name="nama_usaha" maxlength="255"></label>
        <label class="full"><span>Alamat usaha</span><textarea name="alamat"></textarea></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Tambah</button></div>
    </form>
</dialog>

<dialog class="admin-dialog" id="userEditDialog">
    <div class="admin-dialog-head"><h2>Edit Pengguna</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/users/update')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_user">
        <label><span>Nama</span><input name="nama" required maxlength="120"></label><label><span>Email</span><input type="email" name="email" required maxlength="160"></label>
        <label><span>Telepon</span><input name="telepon" required maxlength="50"></label><label><span>Role</span><select name="role"><option value="customer">Customer</option><option value="pemilik">Pemilik</option></select></label>
        <label class="full"><span>Status</span><select name="status"><option>Aktif</option><option>Nonaktif</option></select></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
    </form>
</dialog>
