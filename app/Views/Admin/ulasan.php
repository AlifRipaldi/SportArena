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
        <div class="admin-review-toolbar" data-admin-filter="#adminReviewRows tr[data-filter-text]">
            <label class="admin-filter-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Cari ulasan atau pengguna..." class="admin-search-input" aria-label="Cari ulasan atau pengguna">
            </label>

            <select class="admin-filter-select" aria-label="Filter lapangan">
                <option value="">Semua Lapangan</option>
                <?php foreach (array_values(array_unique(array_column($reviews, 'field'))) as $reviewField): ?><option value="<?php echo e($reviewField); ?>"><?php echo e($reviewField); ?></option><?php endforeach; ?>
            </select>

            <select class="admin-filter-select" aria-label="Filter rating">
                <option value="">Semua Rating</option>
                <option value="5">5 Bintang</option><option value="4">4 Bintang</option><option value="3">3 Bintang</option><option value="2">2 Bintang</option><option value="1">1 Bintang</option>
            </select>

            <select class="admin-filter-select" aria-label="Filter status">
                <option value="">Semua Status</option>
                <option value="Ditanggapi">Ditanggapi</option>
                <option value="Belum Ditanggapi">Belum Ditanggapi</option>
            </select>

            <a class="admin-secondary-btn" href="<?php echo e(app_url('admin/export/ulasan')); ?>">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </a>
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
                <tbody id="adminReviewRows">
                    <?php foreach ($reviews as $review): ?>
                        <tr data-filter-text="<?php echo e(implode(' ', array($review['user'], $review['field'], (string) round($review['rating']), $review['comment'], $review['status']))); ?>">
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
                                    <button class="btn-icon" type="button" title="Lihat ulasan <?php echo e($review['user']); ?>" aria-label="Lihat ulasan <?php echo e($review['user']); ?>" data-dialog-open="reviewDetailDialog" data-payload="<?php echo e(json_encode(array('pengguna' => $review['user'], 'lapangan' => $review['field'], 'rating' => $review['rating'], 'komentar' => $review['comment'], 'balasan' => $review['reply']))); ?>">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button class="btn-icon" type="button" title="Tanggapi ulasan <?php echo e($review['user']); ?>" aria-label="Tanggapi ulasan <?php echo e($review['user']); ?>" data-dialog-open="reviewReplyDialog" data-payload="<?php echo e(json_encode(array('id_review' => $review['id'], 'balasan' => $review['reply']))); ?>">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>
                                    <form class="admin-inline-form" action="<?php echo e(app_url('admin/ulasan/hapus')); ?>" method="post" data-confirm="Hapus ulasan dari <?php echo e($review['user']); ?> secara permanen?"><input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_review" value="<?php echo e($review['id']); ?>"><button class="btn-icon danger" type="submit" title="Hapus ulasan <?php echo e($review['user']); ?>"><i class="fa-regular fa-trash-can"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination admin-pagination-spread">
            <span>Menampilkan <?php echo e(count($reviews)); ?> data</span>
        </div>
    </article>
</div>

<dialog class="admin-dialog" id="reviewDetailDialog">
    <div class="admin-dialog-head"><h2>Detail Ulasan</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <div class="admin-dialog-form"><label><span>Pengguna</span><input name="pengguna" readonly></label><label><span>Lapangan</span><input name="lapangan" readonly></label><label><span>Rating</span><input name="rating" readonly></label><label class="full"><span>Ulasan</span><textarea name="komentar" readonly></textarea></label><label class="full"><span>Tanggapan admin</span><textarea name="balasan" readonly></textarea></label><div class="admin-dialog-actions"><button type="button" class="btn-primary" data-dialog-close>Tutup</button></div></div>
</dialog>
<dialog class="admin-dialog" id="reviewReplyDialog">
    <div class="admin-dialog-head"><h2>Tanggapi Ulasan</h2><button class="admin-dialog-close" type="button" data-dialog-close>&times;</button></div>
    <form class="admin-dialog-form" action="<?php echo e(app_url('admin/ulasan/tanggapi')); ?>" method="post"><input type="hidden" name="admin_token" value="<?php echo e($adminToken); ?>"><input type="hidden" name="id_review"><label class="full"><span>Balasan</span><textarea name="balasan" required maxlength="2000"></textarea></label><div class="admin-dialog-actions"><button type="button" class="admin-secondary-btn" data-dialog-close>Batal</button><button type="submit" class="btn-primary">Simpan Tanggapan</button></div></form>
</dialog>

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
