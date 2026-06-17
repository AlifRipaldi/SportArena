<?php
// Owner - Lapangan Saya
?>

<section class="owner-lapangan-page">
    <div class="owner-lapangan-hero">
        <div>
            <h1>Lapangan Saya</h1>
            <p>Kelola semua lapangan yang Anda miliki</p>
        </div>
        <button class="btn-primary owner-add-field-btn" type="button">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Lapangan</span>
        </button>
    </div>

    <div class="owner-lapangan-grid" aria-label="Kartu lapangan saya">
        <?php foreach ($lapangan as $field): ?>
            <?php
            $cardStatus = isset($field['cardStatus']) ? $field['cardStatus'] : $field['status'];
            $cardStatusClass = strtolower($cardStatus) === 'aktif' ? 'success' : 'warning';
            ?>
            <article class="owner-lapangan-card">
                <div class="owner-lapangan-visual <?php echo e($field['visual']); ?>">
                    <span class="admin-badge <?php echo e($cardStatusClass); ?>"><?php echo e($cardStatus); ?></span>
                    <button class="btn-icon owner-field-like" type="button" aria-label="Favorit <?php echo e($field['name']); ?>">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="owner-lapangan-body">
                    <h2><?php echo e($field['name']); ?></h2>
                    <p class="owner-lapangan-location">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?php echo e($field['location']); ?></span>
                    </p>
                    <p class="owner-lapangan-rating">
                        <i class="fa-solid fa-star"></i>
                        <strong><?php echo e($field['rating']); ?></strong>
                        <span>(<?php echo e($field['reviews']); ?> ulasan)</span>
                    </p>
                    <p class="owner-lapangan-price">
                        <strong><?php echo e($field['price']); ?></strong>
                        <span>/jam</span>
                    </p>

                    <div class="owner-lapangan-actions">
                        <button class="owner-lapangan-btn" type="button">Edit</button>
                        <button class="owner-lapangan-btn primary" type="button">Detail</button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <article class="admin-panel owner-lapangan-table-panel">
        <div class="admin-panel-header owner-lapangan-table-header">
            <h2>Daftar Lapangan</h2>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table owner-lapangan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lapangan</th>
                        <th>Jenis</th>
                        <th>Harga/Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lapangan as $index => $field): ?>
                        <?php $statusClass = strtolower($field['status']) === 'aktif' ? 'success' : 'warning'; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($field['name']); ?></td>
                            <td><?php echo e($field['type']); ?></td>
                            <td><?php echo e($field['price']); ?></td>
                            <td><span class="admin-badge <?php echo e($statusClass); ?>"><?php echo e($field['status']); ?></span></td>
                            <td>
                                <div class="owner-table-actions">
                                    <button class="btn-icon owner-table-edit" type="button" aria-label="Edit <?php echo e($field['name']); ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-icon owner-table-delete" type="button" aria-label="Hapus <?php echo e($field['name']); ?>">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
