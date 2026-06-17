<?php
$renderStars = function ($rating) {
    $rating = (float) $rating;
    $fullStars = (int) floor($rating);
    $hasHalf = ($rating - $fullStars) >= 0.5;
    $emptyStars = max(0, 5 - $fullStars - ($hasHalf ? 1 : 0));
    $html = '';

    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fa-solid fa-star"></i>';
    }

    if ($hasHalf) {
        $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
    }

    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="fa-regular fa-star"></i>';
    }

    return $html;
};
?>

<section class="admin-hero">
    <div>
        <h1>Ulasan & Rating</h1>
        <p>Kelola semua ulasan dan rating dari pengguna.</p>
    </div>
</section>

<section class="admin-review-stat-grid" aria-label="Ringkasan ulasan dan rating">
    <?php foreach ($reviewStats as $stat): ?>
        <article class="admin-stat-card">
            <span class="admin-stat-icon <?php echo e($stat['accent']); ?>">
                <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
            </span>
            <div class="admin-stat-details">
                <p><?php echo e($stat['label']); ?></p>
                <strong><?php echo e($stat['value']); ?></strong>
                <small class="<?php echo $stat['direction'] === 'down' ? 'is-down' : ''; ?>">
                    <i class="fa-solid fa-arrow-<?php echo $stat['direction'] === 'down' ? 'down' : 'up'; ?>"></i>
                    <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span>
                </small>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<div class="admin-content-section">
    <article class="admin-panel admin-full-width admin-review-table-panel">
        <div class="admin-review-toolbar">
            <label class="admin-filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Cari ulasan atau pengguna..." class="admin-search-input" aria-label="Cari ulasan atau pengguna">
            </label>

            <select class="admin-filter-select" aria-label="Filter lapangan">
                <option>Semua Lapangan</option>
                <option>Arena Futsal Parepare</option>
                <option>Mini Soccer Victory</option>
                <option>Lapangan Badminton Center</option>
                <option>Basket Ball Center</option>
            </select>

            <select class="admin-filter-select" aria-label="Filter rating">
                <option>Semua Rating</option>
                <option>5 Bintang</option>
                <option>4 Bintang</option>
                <option>3 Bintang</option>
                <option>2 Bintang</option>
                <option>1 Bintang</option>
            </select>

            <select class="admin-filter-select" aria-label="Filter status">
                <option>Semua Status</option>
                <option>Ditanggapi</option>
                <option>Belum Ditanggapi</option>
            </select>

            <button class="admin-secondary-btn" type="button">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </button>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table admin-review-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Lapangan</th>
                        <th>Rating</th>
                        <th>Ulasan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>
                                <div class="admin-review-user">
                                    <span class="admin-review-avatar <?php echo e($review['accent']); ?>"><?php echo e($review['initials']); ?></span>
                                    <span><?php echo e($review['user']); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($review['field']); ?></td>
                            <td>
                                <span class="admin-review-stars" aria-label="Rating <?php echo e(number_format((float) $review['rating'], 1)); ?>">
                                    <?php echo $renderStars($review['rating']); ?>
                                </span>
                            </td>
                            <td><p class="admin-review-copy"><?php echo e($review['comment']); ?></p></td>
                            <td><?php echo e($review['date']); ?></td>
                            <td><span class="admin-badge <?php echo e($review['statusClass']); ?>"><?php echo e($review['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-icon" type="button" title="Lihat ulasan <?php echo e($review['user']); ?>" aria-label="Lihat ulasan <?php echo e($review['user']); ?>">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button class="btn-icon" type="button" title="Tanggapi ulasan <?php echo e($review['user']); ?>" aria-label="Tanggapi ulasan <?php echo e($review['user']); ?>">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>
                                    <button class="btn-icon danger" type="button" title="Hapus ulasan <?php echo e($review['user']); ?>" aria-label="Hapus ulasan <?php echo e($review['user']); ?>">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination admin-pagination-spread">
            <span>Menampilkan 1 - 5 dari 128 data</span>
            <div class="admin-pagination-pages">
                <button class="admin-pagination-btn" type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="admin-page-number active" type="button">1</button>
                <button class="admin-page-number" type="button">2</button>
                <button class="admin-page-number" type="button">3</button>
                <span>...</span>
                <button class="admin-page-number" type="button">26</button>
                <button class="admin-pagination-btn" type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </article>
</div>

<section class="admin-review-insights" aria-label="Insight ulasan">
    <article class="admin-panel">
        <div class="admin-panel-header">
            <h2>Distribusi Rating</h2>
        </div>

        <div class="admin-rating-distribution">
            <?php foreach ($ratingDistribution as $item): ?>
                <div class="admin-rating-row">
                    <span><?php echo e($item['label']); ?></span>
                    <div class="admin-rating-bar">
                        <span class="<?php echo e($item['color']); ?>" style="width: <?php echo (int) $item['percent']; ?>%;"></span>
                    </div>
                    <strong><?php echo (int) $item['percent']; ?>% (<?php echo (int) $item['count']; ?>)</strong>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="admin-panel">
        <div class="admin-panel-header">
            <h2>Rating per Lapangan</h2>
            <a href="<?php echo e(app_url('admin/lapangan')); ?>">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="admin-field-rating-list">
            <?php foreach ($fieldRatings as $field): ?>
                <div class="admin-field-rating-item">
                    <img src="<?php echo e($field['image']); ?>" alt="<?php echo e($field['name']); ?>">
                    <strong><?php echo e($field['name']); ?></strong>
                    <span class="admin-field-score"><i class="fa-solid fa-star"></i> <?php echo e($field['rating']); ?></span>
                    <em>(<?php echo (int) $field['reviews']; ?>)</em>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="admin-panel">
        <div class="admin-panel-header">
            <h2>Ulasan Terbaru</h2>
            <a href="<?php echo e(app_url('admin/ulasan')); ?>">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="admin-latest-review-list">
            <?php foreach ($latestReviews as $review): ?>
                <div class="admin-latest-review-item">
                    <span class="admin-review-avatar <?php echo e($review['accent']); ?>"><?php echo e($review['initials']); ?></span>
                    <div>
                        <div class="admin-latest-review-head">
                            <div>
                                <strong><?php echo e($review['user']); ?></strong>
                                <small><?php echo e($review['field']); ?></small>
                            </div>
                            <div class="admin-latest-rating">
                                <span class="admin-review-stars" aria-label="Rating <?php echo e(number_format((float) $review['rating'], 1)); ?>">
                                    <?php echo $renderStars($review['rating']); ?>
                                </span>
                                <small><?php echo e($review['date']); ?></small>
                            </div>
                        </div>
                        <p><?php echo e($review['comment']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
