<?php
$photos = isset($venue['images']) && is_array($venue['images']) && !empty($venue['images'])
    ? $venue['images']
    : array($venue['image']);
$features = isset($venue['features']) && is_array($venue['features']) ? $venue['features'] : array();
$schedules = isset($venue['availableSchedules']) && is_array($venue['availableSchedules']) ? $venue['availableSchedules'] : array();
$latitude = isset($venue['latitude']) ? (float) $venue['latitude'] : 0;
$longitude = isset($venue['longitude']) ? (float) $venue['longitude'] : 0;
$openTime = '';
$closeTime = '';

foreach ($operatingHours as $hours) {
    if (!empty($hours['Tutup'])) { continue; }
    $start = !empty($hours['Jam_buka']) ? substr((string) $hours['Jam_buka'], 0, 5) : '';
    $end = !empty($hours['Jam_tutup']) ? substr((string) $hours['Jam_tutup'], 0, 5) : '';
    if ($start !== '' && ($openTime === '' || $start < $openTime)) { $openTime = $start; }
    if ($end !== '' && ($closeTime === '' || $end > $closeTime)) { $closeTime = $end; }
}

if ($openTime === '' && !empty($schedules)) {
    foreach ($schedules as $schedule) {
        $parts = preg_split('/\s*-\s*/', isset($schedule['time']) ? $schedule['time'] : '');
        if (isset($parts[0]) && ($openTime === '' || $parts[0] < $openTime)) { $openTime = $parts[0]; }
        if (isset($parts[1]) && ($closeTime === '' || $parts[1] > $closeTime)) { $closeTime = $parts[1]; }
    }
}

$hoursLabel = $openTime !== '' && $closeTime !== '' ? $openTime . ' - ' . $closeTime . ' WITA' : 'Hubungi pengelola';
$mapUrl = '';
if ($latitude !== 0.0 && $longitude !== 0.0) {
    $left = $longitude - 0.012;
    $right = $longitude + 0.012;
    $bottom = $latitude - 0.008;
    $top = $latitude + 0.008;
    $mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode($left . ',' . $bottom . ',' . $right . ',' . $top) . '&layer=mapnik&marker=' . rawurlencode($latitude . ',' . $longitude);
}
?>

<main class="customer-field-page">
    <nav class="customer-field-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo e(app_url('dashboard')); ?>">Beranda</a><span>&#8250;</span>
        <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Lapangan</a><span>&#8250;</span>
        <strong><?php echo e($venue['name']); ?></strong>
    </nav>

    <section class="customer-field-grid">
        <section class="customer-field-gallery" aria-label="Galeri <?php echo e($venue['name']); ?>">
            <div class="customer-field-photo-stage">
                <img id="customerFieldMainPhoto" src="<?php echo e($photos[0]); ?>" alt="Foto <?php echo e($venue['name']); ?>">
                <span>Populer</span>
                <?php if (count($photos) > 1): ?>
                    <button type="button" class="prev" data-gallery-direction="-1" aria-label="Foto sebelumnya">&#8249;</button>
                    <button type="button" class="next" data-gallery-direction="1" aria-label="Foto berikutnya">&#8250;</button>
                <?php endif; ?>
                <small id="customerFieldPhotoCount">1 / <?php echo count($photos); ?></small>
            </div>
            <div class="customer-field-thumbnails" aria-label="Pilih foto lapangan">
                <?php foreach ($photos as $index => $photo): ?>
                    <button type="button" class="<?php echo $index === 0 ? 'active' : ''; ?>" data-gallery-index="<?php echo $index; ?>">
                        <img src="<?php echo e($photo); ?>" alt="Foto <?php echo $index + 1; ?> <?php echo e($venue['name']); ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </section>

        <article class="customer-field-summary">
            <h1><?php echo e($venue['name']); ?></h1>
            <p class="customer-field-address"><span>&#9906;</span><?php echo e($venue['location']); ?></p>
            <div class="customer-field-tags">
                <?php foreach ($features as $feature): ?><span><?php echo e($feature); ?></span><?php endforeach; ?>
            </div>
            <p class="customer-field-rating"><b>&#9733;</b> <?php echo e($venue['rating']); ?> (<?php echo e($venue['reviews']); ?>)<i></i><span>&#9201; <?php echo e($venue['distance']); ?></span></p>

            <section>
                <h2>Deskripsi</h2>
                <p><?php echo e($venue['description']); ?></p>
            </section>
            <section>
                <h2>Fasilitas</h2>
                <div class="customer-field-facilities">
                    <?php foreach ($features as $feature): ?><span><i>&#10003;</i><?php echo e($feature); ?></span><?php endforeach; ?>
                </div>
            </section>
        </article>

        <aside class="customer-field-booking-card" id="customerFieldBooking">
            <p>Harga Mulai Dari</p>
            <strong id="customerBookingPrice"><?php echo e($venue['price']); ?></strong><span>/jam</span>
            <hr>
            <?php if (!empty($schedules)): ?>
                <form method="post" action="<?php echo e(app_url('dashboard/booking/tambah')); ?>" id="customerFieldBookingForm">
                    <input type="hidden" name="booking_token" value="<?php echo e($bookingCsrfToken); ?>">
                    <input type="hidden" name="id_jadwal" id="customerScheduleId" value="">
                    <label for="customerBookingDate">Pilih Tanggal</label>
                    <input type="date" id="customerBookingDate" min="<?php echo e($schedules[0]['date']); ?>" value="<?php echo e($schedules[0]['date']); ?>">
                    <span class="customer-booking-label">Pilih Jam</span>
                    <div class="customer-time-options" id="customerTimeOptions"></div>
                    <p class="customer-booking-hint" id="customerBookingHint">Pilih salah satu jam yang tersedia.</p>
                    <button type="submit" class="customer-book-now" id="customerBookNow" disabled>Pesan Sekarang</button>
                    <button type="button" class="customer-view-schedule" id="customerViewSchedule">Lihat Jadwal</button>
                </form>
            <?php else: ?>
                <div class="customer-booking-empty"><span>&#128197;</span><strong>Jadwal belum tersedia</strong><p>Coba periksa kembali nanti.</p></div>
                <a class="customer-view-schedule" href="<?php echo e(app_url('dashboard/lapangan')); ?>">Cari Lapangan Lain</a>
            <?php endif; ?>
        </aside>

        <section class="customer-field-info-card">
            <h2>Informasi Tambahan</h2>
            <dl>
                <div><dt>Jenis Lapangan</dt><dd><?php echo e($venue['type']); ?></dd></div>
                <div><dt>Status</dt><dd>Aktif</dd></div>
                <div><dt>Jadwal Tersedia</dt><dd><?php echo count($schedules); ?> slot</dd></div>
                <div><dt>Harga Mulai</dt><dd><?php echo e($venue['price']); ?> / jam</dd></div>
                <div><dt>Jam Operasional</dt><dd><?php echo e($hoursLabel); ?></dd></div>
            </dl>
        </section>

        <section class="customer-field-location-card">
            <h2>Lokasi</h2>
            <?php if ($mapUrl !== ''): ?>
                <iframe title="Peta lokasi <?php echo e($venue['name']); ?>" src="<?php echo e($mapUrl); ?>" loading="lazy"></iframe>
            <?php else: ?>
                <div class="customer-field-map-placeholder"><span>&#9906;</span><strong><?php echo e($venue['name']); ?></strong><small>Koordinat peta belum ditambahkan pengelola.</small></div>
            <?php endif; ?>
            <p><span>&#9906;</span><?php echo e($venue['location']); ?></p>
        </section>

        <section class="customer-field-reviews">
            <h2>Ulasan (<?php echo count($reviews); ?>)</h2>
            <?php if (empty($reviews)): ?>
                <div class="customer-review-empty"><span>&#9734;</span><strong>Belum ada ulasan</strong><p>Jadilah yang pertama memberikan ulasan untuk lapangan ini.</p></div>
            <?php else: ?>
                <div class="customer-review-grid">
                    <?php foreach ($reviews as $review): ?>
                        <article>
                            <header><strong><?php echo e($review['Nama']); ?></strong><span><?php echo str_repeat('&#9733;', max(1, min(5, (int) $review['Rating']))); ?></span></header>
                            <p><?php echo e($review['Komentar']); ?></p>
                            <small><?php echo e(date('d M Y', strtotime((string) $review['created_at']))); ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<script>
    (function () {
        var photos = <?php echo json_encode(array_values($photos), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?>;
        var schedules = <?php echo json_encode(array_values($schedules), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
        var photoIndex = 0;
        var mainPhoto = document.getElementById('customerFieldMainPhoto');
        var photoCount = document.getElementById('customerFieldPhotoCount');
        var thumbnailButtons = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-index]'));

        function showPhoto(index) {
            if (!photos.length) { return; }
            photoIndex = (index + photos.length) % photos.length;
            mainPhoto.src = photos[photoIndex];
            photoCount.textContent = (photoIndex + 1) + ' / ' + photos.length;
            thumbnailButtons.forEach(function (button) { button.classList.toggle('active', Number(button.dataset.galleryIndex) === photoIndex); });
        }
        thumbnailButtons.forEach(function (button) { button.addEventListener('click', function () { showPhoto(Number(button.dataset.galleryIndex)); }); });
        document.querySelectorAll('[data-gallery-direction]').forEach(function (button) { button.addEventListener('click', function () { showPhoto(photoIndex + Number(button.dataset.galleryDirection)); }); });

        var dateInput = document.getElementById('customerBookingDate');
        var timeOptions = document.getElementById('customerTimeOptions');
        var scheduleInput = document.getElementById('customerScheduleId');
        var bookButton = document.getElementById('customerBookNow');
        var hint = document.getElementById('customerBookingHint');
        var price = document.getElementById('customerBookingPrice');

        function renderTimes() {
            if (!dateInput || !timeOptions) { return; }
            var matches = schedules.filter(function (schedule) { return schedule.date === dateInput.value; });
            timeOptions.textContent = '';
            scheduleInput.value = '';
            bookButton.disabled = true;
            hint.textContent = matches.length ? 'Pilih salah satu jam yang tersedia.' : 'Tidak ada jadwal pada tanggal ini.';
            matches.forEach(function (schedule) {
                var button = document.createElement('button');
                button.type = 'button';
                button.textContent = schedule.time;
                button.addEventListener('click', function () {
                    timeOptions.querySelectorAll('button').forEach(function (item) { item.classList.toggle('active', item === button); });
                    scheduleInput.value = schedule.id;
                    price.textContent = schedule.price;
                    hint.textContent = schedule.dateLabel + ', ' + schedule.time;
                    bookButton.disabled = false;
                });
                timeOptions.appendChild(button);
            });
        }
        if (dateInput) { dateInput.addEventListener('change', renderTimes); renderTimes(); }
        var viewSchedule = document.getElementById('customerViewSchedule');
        if (viewSchedule) { viewSchedule.addEventListener('click', function () { dateInput.focus(); dateInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); }); }
    }());
</script>
