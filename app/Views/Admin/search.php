<section class="admin-hero"><div><h1>Hasil Pencarian</h1><p>Hasil untuk “<?php echo e($query); ?>” di pengguna, lapangan, dan booking.</p></div></section>
<article class="admin-panel admin-full-width">
    <?php if ($query === ''): ?><p>Masukkan kata kunci pada kolom pencarian di bagian atas.</p><?php elseif (empty($results)): ?><p>Tidak ada data yang cocok.</p><?php else: ?>
    <div class="admin-latest-review-list"><?php foreach ($results as $result): ?><a class="admin-latest-review-item" href="<?php echo e($result['url']); ?>"><span class="admin-review-avatar blue"><i class="fa-solid fa-magnifying-glass"></i></span><div><strong><?php echo e($result['title']); ?></strong><p><?php echo e($result['detail']); ?></p><small><?php echo e($result['type']); ?></small></div></a><?php endforeach; ?></div>
    <?php endif; ?>
</article>
