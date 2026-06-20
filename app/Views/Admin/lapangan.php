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
        <button class="btn-primary" type="button"><i class="fa-solid fa-plus"></i> Tambah Lapangan</button>
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
    <div class="admin-filter-bar">
        <label class="admin-filter-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari lapangan..." class="admin-search-input" aria-label="Cari lapangan">
        </label>
        <select class="admin-filter-select" aria-label="Filter jenis lapangan">
            <option>Jenis: Semua</option>
            <option>Futsal</option>
            <option>Badminton</option>
            <option>Basketball</option>
            <option>Mini Soccer</option>
        </select>
        <select class="admin-filter-select" aria-label="Filter status lapangan">
            <option>Status: Semua</option>
            <option>Aktif</option>
            <option>Maintenance</option>
            <option>Nonaktif</option>
        </select>
    </div>

    <div class="admin-lapangan-grid">
        <?php foreach ($fields as $field): ?>
            <article class="admin-lapangan-card">
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
                    <button class="admin-action-btn" type="button" title="Edit <?php echo e($field['name']); ?>">
                        <i class="fa-solid fa-pen"></i>
                        <span>Edit</span>
                    </button>
                    <button class="admin-action-btn" type="button" title="Jadwal <?php echo e($field['name']); ?>">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Jadwal</span>
                    </button>
                    <button class="admin-action-btn danger" type="button" title="Hapus <?php echo e($field['name']); ?>">
                        <i class="fa-solid fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
