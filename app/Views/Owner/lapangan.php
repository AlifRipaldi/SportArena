<?php
// Owner - Lapangan Management
?>

<section class="admin-hero">
    <div>
        <h1>Kelola Lapangan</h1>
        <p>Atur dan kelola semua lapangan Anda</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Lapangan</button>
    </div>
</section>

<div class="admin-content-section">
    <div class="admin-filter-bar">
        <input type="search" placeholder="Cari lapangan..." class="admin-search-input">
        <select class="admin-filter-select">
            <option>Status: Semua</option>
            <option>Aktif</option>
            <option>Maintenance</option>
            <option>Nonaktif</option>
        </select>
    </div>

    <div class="admin-lapangan-grid">
        <?php foreach ($lapangan as $field): ?>
            <article class="admin-lapangan-card">
                <div class="admin-lapangan-header">
                    <h3><?php echo e($field['name']); ?></h3>
                    <span class="admin-badge <?php echo $field['status'] === 'Aktif' ? 'success' : 'warning'; ?>"><?php echo e($field['status']); ?></span>
                </div>
                <div class="admin-lapangan-info">
                    <p><strong>Jenis:</strong> <?php echo e($field['type']); ?></p>
                    <p><strong>Lokasi:</strong> <?php echo e($field['location']); ?></p>
                    <p><strong>Harga:</strong> <?php echo e($field['price']); ?>/jam</p>
                </div>
                <div class="admin-lapangan-actions">
                    <button class="btn-small"><i class="fa-solid fa-pen"></i> Edit</button>
                    <button class="btn-small"><i class="fa-solid fa-calendar"></i> Jadwal</button>
                    <button class="btn-small danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
