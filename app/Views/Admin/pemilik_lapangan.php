<?php
$ownerStats = array(
    array('label' => 'Total Pemilik', 'value' => '48', 'trend' => '6', 'note' => 'dari bulan lalu', 'icon' => 'fa-users-gear', 'accent' => 'lime'),
    array('label' => 'Total Lapangan', 'value' => '76', 'trend' => '8', 'note' => 'dari bulan lalu', 'icon' => 'fa-shop', 'accent' => 'blue'),
    array('label' => 'Total Booking', 'value' => '245', 'trend' => '15', 'note' => 'dari bulan lalu', 'icon' => 'fa-calendar-check', 'accent' => 'purple'),
    array('label' => 'Rating Rata-rata', 'value' => '4.6', 'trend' => '0.2', 'note' => 'dari bulan lalu', 'icon' => 'fa-star', 'accent' => 'gold'),
);

$owners = array(
    array(
        'name' => 'Andi Rahman',
        'email' => 'andi@arena.com',
        'phone' => '081234567890',
        'city' => 'Parepare',
        'fields' => 5,
        'bookings' => 32,
        'rating' => '4.8',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '18 Feb 2024',
        'accent' => 'lime',
    ),
    array(
        'name' => 'Siti Raodah',
        'email' => 'siti@futsal.com',
        'phone' => '082345678901',
        'city' => 'Makassar',
        'fields' => 8,
        'bookings' => 45,
        'rating' => '4.6',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '12 Jan 2024',
        'accent' => 'blue',
    ),
    array(
        'name' => 'Budi Wijaya',
        'email' => 'budiwijaya@gmail.com',
        'phone' => '083456789012',
        'city' => 'Pinrang',
        'fields' => 3,
        'bookings' => 18,
        'rating' => '4.4',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '05 Mar 2024',
        'accent' => 'purple',
    ),
    array(
        'name' => 'Muh. Nasrullah',
        'email' => 'nasrullah@lapangan.com',
        'phone' => '084567890123',
        'city' => 'Barru',
        'fields' => 4,
        'bookings' => 27,
        'rating' => '4.7',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '22 Apr 2024',
        'accent' => 'gold',
    ),
    array(
        'name' => 'Fajar Maulana',
        'email' => 'fajar.maulana@arena.com',
        'phone' => '085678901234',
        'city' => 'Sidrap',
        'fields' => 6,
        'bookings' => 38,
        'rating' => '4.5',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '30 Apr 2024',
        'accent' => 'green',
    ),
    array(
        'name' => 'Nurfadillah',
        'email' => 'nurfadillah@gmail.com',
        'phone' => '086789012345',
        'city' => 'Parepare',
        'fields' => 2,
        'bookings' => 12,
        'rating' => '4.2',
        'status' => 'Nonaktif',
        'statusClass' => 'warning',
        'joined' => '11 Mei 2024',
        'accent' => 'pink',
    ),
    array(
        'name' => 'Hasanuddin',
        'email' => 'hasanuddin@lapangan.com',
        'phone' => '087890123456',
        'city' => 'Makassar',
        'fields' => 7,
        'bookings' => 41,
        'rating' => '4.9',
        'status' => 'Aktif',
        'statusClass' => 'success',
        'joined' => '09 Jan 2024',
        'accent' => 'teal',
    ),
);
?>

<section class="admin-hero">
    <div>
        <h1>Kelola Pemilik Lapangan</h1>
        <p>Kelola semua pemilik lapangan yang terdaftar di Arena Sport</p>
    </div>
    <div class="admin-hero-actions">
        <button class="btn-primary" type="button"><i class="fa-solid fa-plus"></i> Tambah Pemilik</button>
    </div>
</section>

<section class="admin-stat-grid" aria-label="Ringkasan pemilik lapangan">
    <?php foreach ($ownerStats as $stat): ?>
        <article class="admin-stat-card">
            <span class="admin-stat-icon <?php echo e($stat['accent']); ?>">
                <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
            </span>
            <div class="admin-stat-details">
                <p><?php echo e($stat['label']); ?></p>
                <strong><?php echo e($stat['value']); ?></strong>
                <small><i class="fa-solid fa-arrow-up"></i> <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span></small>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<div class="admin-content-section">
    <article class="admin-panel admin-full-width">
        <div class="admin-owner-toolbar">
            <label class="admin-filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Cari pemilik..." class="admin-search-input" aria-label="Cari pemilik lapangan">
            </label>

            <select class="admin-filter-select" aria-label="Filter status pemilik">
                <option>Status: Semua</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>

            <select class="admin-filter-select" aria-label="Filter kota pemilik">
                <option>Kota: Semua</option>
                <option>Makassar</option>
                <option>Parepare</option>
                <option>Pinrang</option>
                <option>Barru</option>
                <option>Sidrap</option>
            </select>

            <button class="admin-secondary-btn" type="button">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </button>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table admin-owner-table">
                <thead>
                    <tr>
                        <th>Pemilik</th>
                        <th>Kontak</th>
                        <th>Kota</th>
                        <th>Total Lapangan</th>
                        <th>Total Booking</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($owners as $owner): ?>
                        <tr>
                            <td>
                                <div class="admin-customer">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($owner['name']); ?>&background=20314a&color=ffffff" alt="">
                                    <span><?php echo e($owner['name']); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="admin-owner-contact">
                                    <span><?php echo e($owner['email']); ?></span>
                                    <small><?php echo e($owner['phone']); ?></small>
                                </div>
                            </td>
                            <td><?php echo e($owner['city']); ?></td>
                            <td><?php echo e($owner['fields']); ?></td>
                            <td><?php echo e($owner['bookings']); ?></td>
                            <td>
                                <span class="admin-rating"><?php echo e($owner['rating']); ?> <i class="fa-solid fa-star"></i></span>
                            </td>
                            <td><span class="admin-badge <?php echo e($owner['statusClass']); ?>"><?php echo e($owner['status']); ?></span></td>
                            <td><?php echo e($owner['joined']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-icon" type="button" title="Edit <?php echo e($owner['name']); ?>" aria-label="Edit <?php echo e($owner['name']); ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn-icon danger" type="button" title="Hapus <?php echo e($owner['name']); ?>" aria-label="Hapus <?php echo e($owner['name']); ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination admin-pagination-spread">
            <span>Menampilkan 1 - 7 dari 48 data</span>
            <div class="admin-pagination-pages">
                <button class="admin-pagination-btn" type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="admin-page-number active" type="button">1</button>
                <button class="admin-page-number" type="button">2</button>
                <button class="admin-page-number" type="button">3</button>
                <span>...</span>
                <button class="admin-page-number" type="button">7</button>
                <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </article>
</div>
