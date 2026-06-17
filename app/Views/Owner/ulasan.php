<?php
if (!function_exists('owner_review_stars')) {
function owner_review_stars($rating)
{
    $rating = (float) $rating;

    for ($index = 1; $index <= 5; $index++) {
        if ($rating >= $index) {
            echo '<i class="fa-solid fa-star"></i>';
            continue;
        }

        if ($rating >= $index - 0.5) {
            echo '<i class="fa-solid fa-star-half-stroke"></i>';
            continue;
        }

        echo '<i class="fa-regular fa-star"></i>';
    }
}
}
?>

<section class="owner-ulasan-page">
    <div class="owner-ulasan-hero">
        <div>
            <h1>Ulasan & Rating</h1>
            <p>Lihat ulasan dan rating dari pelanggan Anda</p>
        </div>
    </div>

    <section class="owner-ulasan-stat-grid" aria-label="Ringkasan ulasan dan rating">
        <?php foreach ($reviewStats as $stat): ?>
            <article class="owner-ulasan-stat-card">
                <span class="owner-ulasan-stat-icon <?php echo e($stat['accent']); ?>">
                    <i class="fa-solid <?php echo e($stat['icon']); ?>"></i>
                </span>
                <div>
                    <p><?php echo e($stat['label']); ?></p>
                    <strong><?php echo e($stat['value']); ?></strong>

                    <?php if (isset($stat['rating'])): ?>
                        <div class="owner-ulasan-stars" aria-label="Rating <?php echo e($stat['rating']); ?> dari 5">
                            <?php owner_review_stars($stat['rating']); ?>
                        </div>
                        <small class="muted"><?php echo e($stat['note']); ?></small>
                    <?php else: ?>
                        <small class="<?php echo $stat['accent'] === 'orange' ? 'negative' : ''; ?>">
                            <?php if ($stat['accent'] !== 'orange'): ?>
                                <i class="fa-solid fa-arrow-up"></i>
                            <?php endif; ?>
                            <?php echo e($stat['trend']); ?> <span><?php echo e($stat['note']); ?></span>
                        </small>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="owner-ulasan-insight-grid">
        <article class="admin-panel owner-ulasan-distribution-panel">
            <div class="owner-ulasan-panel-header">
                <h2>Distribusi Rating</h2>
            </div>

            <div class="owner-ulasan-rating-bars">
                <?php foreach ($ratingDistribution as $rating): ?>
                    <div class="owner-ulasan-rating-row">
                        <span class="owner-ulasan-rating-label"><?php echo e($rating['stars']); ?> <i class="fa-solid fa-star"></i></span>
                        <span class="owner-ulasan-rating-track">
                            <span style="width: <?php echo e($rating['percent']); ?>%;"></span>
                        </span>
                        <strong><?php echo e($rating['count']); ?> <em>(<?php echo e(number_format($rating['percent'], 1)); ?>%)</em></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="admin-panel owner-ulasan-fields-panel">
            <div class="owner-ulasan-panel-header">
                <h2>Rating per Lapangan</h2>
            </div>

            <div class="owner-ulasan-field-list">
                <?php foreach ($fieldRatings as $field): ?>
                    <article class="owner-ulasan-field-item">
                        <img src="<?php echo e($field['image']); ?>" alt="<?php echo e($field['name']); ?>">
                        <div>
                            <div class="owner-ulasan-field-head">
                                <strong><?php echo e($field['name']); ?></strong>
                                <span><i class="fa-solid fa-star"></i> <?php echo e($field['rating']); ?> <em>(<?php echo e($field['reviews']); ?> ulasan)</em></span>
                            </div>
                            <span class="owner-ulasan-field-progress">
                                <span style="width: <?php echo e($field['percent']); ?>%;"></span>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </article>
    </section>

    <article class="admin-panel owner-ulasan-table-panel">
        <div class="owner-ulasan-table-header">
            <h2>Ulasan Terbaru</h2>
            <button type="button">
                <i class="fa-solid fa-filter"></i>
                <span>Semua Lapangan</span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table owner-ulasan-table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Lapangan</th>
                        <th>Rating</th>
                        <th>Ulasan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>
                                <div class="owner-ulasan-customer">
                                    <img src="<?php echo e($review['avatar']); ?>" alt="<?php echo e($review['name']); ?>">
                                    <span>
                                        <strong><?php echo e($review['name']); ?></strong>
                                        <small><?php echo e($review['username']); ?></small>
                                    </span>
                                </div>
                            </td>
                            <td><?php echo e($review['field']); ?></td>
                            <td>
                                <span class="owner-ulasan-table-stars" aria-label="Rating <?php echo e($review['rating']); ?> dari 5">
                                    <?php owner_review_stars($review['rating']); ?>
                                </span>
                            </td>
                            <td class="owner-ulasan-review-copy"><?php echo e($review['review']); ?></td>
                            <td>
                                <span class="owner-ulasan-date">
                                    <?php echo e($review['date']); ?>
                                    <small><?php echo e($review['time']); ?></small>
                                </span>
                            </td>
                            <td>
                                <button class="btn-icon owner-ulasan-view" type="button" aria-label="Lihat ulasan <?php echo e($review['name']); ?>">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="owner-ulasan-table-footer">
            <p>Menampilkan 1 - 5 dari 156 ulasan</p>
            <nav class="owner-ulasan-pagination" aria-label="Paginasi ulasan">
                <button type="button" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="active" type="button" aria-current="page">1</button>
                <button type="button">2</button>
                <button type="button">3</button>
                <span>...</span>
                <button type="button">32</button>
                <button type="button" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></button>
            </nav>
        </div>
    </article>
</section>
