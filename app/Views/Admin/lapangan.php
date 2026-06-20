<?php
if (!isset($fields) || !is_array($fields)) {
$fields = array(
    array(
        'name' => 'Futsal A',
        'type' => 'Futsal',
        'location' => 'Area 1',
        'price' => 'Rp80.000/jam',
        'bookings' => 8,
        'rating' => '4.8',
        'reviews' => 120,
        'status' => 'Aktif',
        'badge' => 'success',
        'progress' => 82,
        'icon' => 'fa-futbol',
        'accent' => 'lime',
    ),
    array(
        'name' => 'Badminton B',
        'type' => 'Badminton',
        'location' => 'Area 2',
        'price' => 'Rp60.000/jam',
        'bookings' => 6,
        'rating' => '4.6',
        'reviews' => 85,
        'status' => 'Aktif',
        'badge' => 'success',
        'progress' => 68,
        'icon' => 'fa-table-tennis-paddle-ball',
        'accent' => 'blue',
    ),
    array(
        'name' => 'Mini Soccer',
        'type' => 'Mini Soccer',
        'location' => 'Area 3',
        'price' => 'Rp100.000/jam',
        'bookings' => 5,
        'rating' => '4.7',
        'reviews' => 98,
        'status' => 'Aktif',
        'badge' => 'success',
        'progress' => 74,
        'icon' => 'fa-person-running',
        'accent' => 'green',
    ),
    array(
        'name' => 'Basketball A',
        'type' => 'Basketball',
        'location' => 'Area 4',
        'price' => 'Rp70.000/jam',
        'bookings' => 0,
        'rating' => '4.4',
        'reviews' => 45,
        'status' => 'Maintenance',
        'badge' => 'warning',
        'progress' => 0,
        'icon' => 'fa-basketball',
        'accent' => 'gold',
    ),
);
}

$activeFields = 0;
$maintenanceFields = 0;
$totalBookings = 0;

foreach ($fields as $field) {
    if ($field['status'] === 'Aktif') {
        $activeFields++;
    }

    if ($field['status'] === 'Maintenance') {
        $maintenanceFields++;
    }

    $totalBookings += $field['bookings'];
}
?>

<section class="admin-hero admin-page-hero">
    <div>
        <h1>Manajemen Lapangan</h1>
        <p>Kelola semua lapangan olahraga yang tersedia</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary" type="button" data-dialog-open="fieldCreateDialog"><i class="fa-solid fa-plus"></i> Tambah Lapangan</button>
        <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/lapangan')); ?>"><i class="fa-solid fa-download"></i> Export</a>
    </div>
</section>

<section class="admin-lapangan-summary" aria-label="Ringkasan lapangan">
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon lime"><i class="fa-solid fa-layer-group"></i></span>
        <div>
            <p>Total Lapangan</p>
            <strong><?php echo count($fields); ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon green"><i class="fa-solid fa-circle-check"></i></span>
        <div>
            <p>Lapangan Aktif</p>
            <strong><?php echo $activeFields; ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon gold"><i class="fa-solid fa-screwdriver-wrench"></i></span>
        <div>
            <p>Maintenance</p>
            <strong><?php echo $maintenanceFields; ?></strong>
        </div>
    </article>
    <article class="admin-mini-stat">
        <span class="admin-mini-stat-icon blue"><i class="fa-solid fa-calendar-day"></i></span>
        <div>
            <p>Booking Hari Ini</p>
            <strong><?php echo $totalBookings; ?></strong>
        </div>
    </article>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar" data-admin-filter="#adminFieldCards .admin-lapangan-card">
        <label class="admin-filter-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari lapangan..." class="admin-search-input" aria-label="Cari lapangan">
        </label>
        <select class="admin-filter-select" aria-label="Filter jenis lapangan">
            <option value="">Jenis: Semua</option>
            <?php foreach (array_values(array_unique(array_column($fields, 'type'))) as $fieldType): ?><option value="<?php echo e($fieldType); ?>"><?php echo e($fieldType); ?></option><?php endforeach; ?>
        </select>
        <select class="admin-filter-select" aria-label="Filter status lapangan">
            <option value="">Status: Semua</option>
            <option value="Aktif">Aktif</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Nonaktif">Nonaktif</option>
        </select>
    </div>

    <div class="admin-lapangan-grid" id="adminFieldCards">
        <?php foreach ($fields as $field): ?>
            <article class="admin-lapangan-card" data-filter-text="<?php echo e(implode(' ', array($field['name'], $field['type'], $field['location'], $field['status']))); ?>">
                <div class="admin-lapangan-header">
                    <div class="admin-field-title">
                        <span class="admin-field-icon <?php echo e($field['accent']); ?>">
                            <i class="fa-solid <?php echo e($field['icon']); ?>"></i>
                        </span>
                        <div>
                            <h3><?php echo e($field['name']); ?></h3>
                            <p><?php echo e($field['location']); ?></p>
                        </div>
                    </div>
                    <span class="admin-badge <?php echo e($field['badge']); ?>"><?php echo e($field['status']); ?></span>
                </div>

                <div class="admin-lapangan-metrics">
                    <div>
                        <span>Harga</span>
                        <strong><?php echo e($field['price']); ?></strong>
                    </div>
                    <div>
                        <span>Booking</span>
                        <strong><?php echo e($field['bookings']); ?> hari ini</strong>
                    </div>
                </div>

                <div class="admin-lapangan-meta">
                    <span><i class="fa-solid fa-tag"></i> <?php echo e($field['type']); ?></span>
                    <span><i class="fa-solid fa-star"></i> <?php echo e($field['rating']); ?> (<?php echo e($field['reviews']); ?> review)</span>
                </div>

                <div class="admin-occupancy">
                    <div>
                        <span>Tingkat booking</span>
                        <strong><?php echo (int) $field['progress']; ?>%</strong>
                    </div>
                    <div class="admin-progress">
                        <span style="width: <?php echo (int) $field['progress']; ?>%;"></span>
                    </div>
                </div>

                <div class="admin-lapangan-actions">
                    <button class="admin-action-btn" type="button" title="Edit <?php echo e($field['name']); ?>" data-dialog-open="fieldEditDialog" data-payload="<?php echo e(json_encode(array('id_lapangan' => $field['id'], 'id_pemilik' => $field['ownerId'], 'nama' => $field['name'], 'lokasi' => $field['location'], 'jenis' => $field['type'], 'fasilitas' => $field['facilities'], 'harga' => $field['priceValue'], 'status' => $field['status'], 'deskripsi' => $field['description']))); ?>">
                        <i class="fa-solid fa-pen"></i>
                        <span>Edit</span>
                    </button>
                    <a class="admin-action-btn" href="<?php echo e(app_url('admin/jadwal?field=' . rawurlencode($field['id']))); ?>" title="Jadwal <?php echo e($field['name']); ?>">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Jadwal</span>
                    </a>
                    <form class="admin-inline-form" action="<?php echo e(app_url('admin/lapangan/hapus')); ?>" method="post" data-confirm="Nonaktifkan dan sembunyikan <?php echo e($field['name']); ?>?"><input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_lapangan" value="<?php echo e($field['id']); ?>"><button class="admin-action-btn danger" type="submit" title="Hapus <?php echo e($field['name']); ?>"><i class="fa-solid fa-trash"></i><span>Hapus</span></button></form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<dialog class="admin-dialog" id="fieldCreateDialog">
    <div class="admin-dialog-head"><h2>Tambah Lapangan</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/lapangan/tambah')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>">
        <label class="full"><span>Pemilik</span><select name="id_pemilik" required><option value="">Pilih pemilik</option><?php foreach ($fieldOwners as $owner): ?><option value="<?php echo e($owner['id']); ?>"><?php echo e($owner['name']); ?></option><?php endforeach; ?></select></label>
        <label><span>Nama lapangan</span><input name="nama" required maxlength="160"></label><label><span>Jenis olahraga</span><input name="jenis" required maxlength="80"></label>
        <label><span>Lokasi</span><input name="lokasi" required maxlength="180"></label><label><span>Harga per jam</span><input type="number" name="harga" required min="1"></label>
        <label class="full"><span>Fasilitas</span><textarea name="fasilitas"></textarea></label><label class="full"><span>Deskripsi</span><textarea name="deskripsi"></textarea></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Tambah</button></div>
    </form>
</dialog>

<dialog class="admin-dialog" id="fieldEditDialog">
    <div class="admin-dialog-head"><h2>Edit Lapangan</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/lapangan/update')); ?>" method="post">
        <input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_lapangan">
        <label class="full"><span>Pemilik</span><select name="id_pemilik" required><?php foreach ($fieldOwners as $owner): ?><option value="<?php echo e($owner['id']); ?>"><?php echo e($owner['name']); ?></option><?php endforeach; ?></select></label>
        <label><span>Nama lapangan</span><input name="nama" required></label><label><span>Jenis olahraga</span><input name="jenis" required></label>
        <label><span>Lokasi</span><input name="lokasi" required></label><label><span>Harga per jam</span><input type="number" name="harga" required min="1"></label>
        <label><span>Status</span><select name="status"><option>Aktif</option><option>Maintenance</option><option>Nonaktif</option></select></label><label><span>Fasilitas</span><input name="fasilitas"></label>
        <label class="full"><span>Deskripsi</span><textarea name="deskripsi"></textarea></label>
        <div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
    </form>
</dialog>
