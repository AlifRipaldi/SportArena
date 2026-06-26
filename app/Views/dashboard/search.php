<?php
$filterSports = array();
$filterFacilities = array();
$filterTimes = array();

foreach ($venues as $venue) {
    $venueType = isset($venue['type']) ? trim((string) $venue['type']) : '';
    $venueFeatures = isset($venue['features']) && is_array($venue['features']) ? $venue['features'] : array();
    $venueTimes = isset($venue['availableTimes']) && is_array($venue['availableTimes']) ? $venue['availableTimes'] : array();

    if ($venueType !== '' && !in_array($venueType, $filterSports, true)) {
        $filterSports[] = $venueType;
    }

    foreach ($venueFeatures as $feature) {
        $feature = trim((string) $feature);

        if ($feature !== '' && $feature !== $venueType && strpos($feature, '+') !== 0 && !in_array($feature, $filterFacilities, true)) {
            $filterFacilities[] = $feature;
        }
    }

    foreach ($venueTimes as $time) {
        $time = trim((string) $time);

        if ($time !== '' && !in_array($time, $filterTimes, true)) {
            $filterTimes[] = $time;
        }
    }
}

sort($filterSports);
sort($filterFacilities);
sort($filterTimes);

$venueDetails = array();
foreach ($venues as $venue) {
    $venueId = isset($venue['id']) ? (string) $venue['id'] : '';
    if ($venueId === '') {
        continue;
    }

    $venueDetails[$venueId] = array(
        'id' => $venueId,
        'name' => isset($venue['name']) ? $venue['name'] : 'Lapangan Olahraga',
        'type' => isset($venue['type']) ? $venue['type'] : '',
        'location' => isset($venue['location']) ? $venue['location'] : '',
        'description' => isset($venue['description']) ? $venue['description'] : '',
        'features' => isset($venue['features']) && is_array($venue['features']) ? $venue['features'] : array(),
        'rating' => isset($venue['rating']) ? $venue['rating'] : '0.0',
        'reviews' => isset($venue['reviews']) ? $venue['reviews'] : '0 ulasan',
        'distance' => isset($venue['distance']) ? $venue['distance'] : '-',
        'price' => isset($venue['price']) ? $venue['price'] : 'Rp0',
        'image' => isset($venue['image']) ? $venue['image'] : '',
        'schedules' => isset($venue['availableSchedules']) && is_array($venue['availableSchedules']) ? $venue['availableSchedules'] : array(),
    );
}
?>

<div class="dashboard-shell profile-dashboard search-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo-mark.png')); ?>" alt="Arena Sport Logo">
            </div>
            <div>
                <strong>Arena</strong>
                <span>Sport</span>
            </div>
        </div>

        <nav class="dashboard-menu" aria-label="Menu dashboard">
            <a href="<?php echo e(app_url('dashboard')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : ''; ?>"><span>&#8962;</span>Dashboard</a>
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'lapangan' ? 'active' : ''; ?>"><span>&#128269;</span>Cari Lapangan</a>
            <a href="<?php echo e(app_url('dashboard/booking')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'booking' ? 'active' : ''; ?>"><span>&#128197;</span>Booking Saya</a>
            <a href="<?php echo e(app_url('dashboard/favorit')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'favorit' ? 'active' : ''; ?>"><span>&#9825;</span>Favorit</a>
            <a href="<?php echo e(app_url('dashboard/riwayat')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'riwayat' ? 'active' : ''; ?>"><span>&#9201;</span>Riwayat</a>
            <a href="<?php echo e(app_url('dashboard/ulasan')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'ulasan' ? 'active' : ''; ?>"><span>&#9734;</span>Ulasan Saya</a>
            <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'profil' ? 'active' : ''; ?>"><span>&#9786;</span>Profil</a>
            <a href="<?php echo e(app_url('settings')); ?>" class="<?php echo isset($activeMenu) && $activeMenu === 'settings' ? 'active' : ''; ?>"><span>&#9881;</span>Pengaturan</a>
        </nav>

        <div class="dashboard-promo profile-promo">
            <p>Mainkan Game Terbaikmu</p>
            <small>Pesan lapangan favoritmu sekarang!</small>
            <a href="#lapangan-populer">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main search-main">
        <?php if (!empty($bookingMessage)): ?><section class="settings-alert success-message" role="status"><?php echo e($bookingMessage); ?></section><?php endif; ?>
        <?php if (!empty($bookingError)): ?><section class="settings-alert error-message" role="alert"><?php echo e($bookingError); ?></section><?php endif; ?>
        <section class="profile-page-head search-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions">
                <?php require __DIR__ . '/partials/customer_notifications.php'; ?>
                <a href="<?php echo e(app_url('dashboard/profil')); ?>" class="profile-account-menu" aria-label="Buka profil">
                    <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                    <span>&#8964;</span>
                </a>
            </div>
        </section>

        <section class="field-search-panel" aria-label="Filter pencarian lapangan">
            <div class="field-search-primary">
                <label class="field-search-control location">
                    <span>Cari Lokasi</span>
                    <div>
                        <i>&#9906;</i>
                        <input id="fieldLocationFilter" type="search" placeholder="Contoh: Parepare, Mattirotasi, Sudirman" autocomplete="off">
                    </div>
                </label>

                <button
                    type="button"
                    class="field-filter-button"
                    id="fieldFilterToggle"
                    aria-expanded="false"
                    aria-controls="fieldAdvancedFilters"
                >
                    <span aria-hidden="true">&#9776;</span>
                    <span>Filter Lainnya</span>
                    <i class="field-filter-chevron" aria-hidden="true">&#8964;</i>
                </button>
            </div>

            <div class="field-advanced-filters" id="fieldAdvancedFilters" hidden>
                <div class="field-advanced-head">
                    <div>
                        <strong>Filter Lanjutan</strong>
                        <small>Sesuaikan pencarian lapangan dengan kebutuhanmu.</small>
                    </div>
                    <button type="button" class="field-filter-close" aria-label="Tutup filter lanjutan">&#215;</button>
                </div>

                <div class="field-search-grid advanced">
                    <label class="field-search-control">
                        <span>Jenis Olahraga</span>
                        <select id="fieldSportFilter">
                            <option value="">Semua Olahraga</option>
                            <?php foreach ($filterSports as $sport): ?>
                                <option value="<?php echo e($sport); ?>"><?php echo e($sport); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="field-search-control">
                        <span>Tanggal</span>
                        <div>
                            <i>&#128197;</i>
                            <input id="fieldDateFilter" type="date" aria-label="Pilih tanggal bermain">
                        </div>
                    </label>
                    <label class="field-search-control">
                        <span>Waktu</span>
                        <div>
                            <i>&#9201;</i>
                            <input id="fieldTimeFilter" type="time" aria-label="Masukkan waktu bermain">
                        </div>
                    </label>
                    <label class="field-search-control price">
                        <span>Rentang Harga</span>
                        <div class="field-price-row">
                            <input id="fieldMinPriceFilter" type="number" min="0" step="1000" placeholder="Min" aria-label="Harga minimum">
                            <small>Rp</small>
                            <b>-</b>
                            <input id="fieldMaxPriceFilter" type="number" min="0" step="1000" placeholder="Max" aria-label="Harga maksimum">
                            <small>Rp</small>
                        </div>
                    </label>
                    <label class="field-search-control">
                        <span>Fasilitas</span>
                        <select id="fieldFacilityFilter">
                            <option value="">Pilih Fasilitas</option>
                            <?php foreach ($filterFacilities as $facility): ?>
                                <option value="<?php echo e($facility); ?>"><?php echo e($facility); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
            </div>

            <div class="field-active-filters" id="fieldActiveFilters" hidden>
                <span>Filter Aktif:</span>
                <div class="field-active-filter-list" id="fieldActiveFilterList"></div>
                <button type="button" class="field-clear-filters" id="fieldClearFilters"><span>&#128465;</span>Hapus Semua</button>
            </div>
        </section>

        <section class="field-results-head">
            <p aria-live="polite"><strong id="fieldResultCount"><?php echo count($venues); ?></strong> Lapangan Ditemukan</p>
            <label class="field-sort-control">
                <span>Urutkan:</span>
                <select id="fieldSortControl">
                    <option value="distance">Terdekat</option>
                    <option value="price">Harga Terendah</option>
                    <option value="rating">Rating Tertinggi</option>
                </select>
            </label>
        </section>

        <section id="lapangan-populer" class="field-result-list" aria-label="Daftar lapangan ditemukan">
            <?php foreach ($venues as $index => $venue): ?>
                <?php
                    $sportTypes = array('Futsal', 'Badminton', 'Mini Soccer');
                    $featureSets = array(
                        array('Futsal', 'Parkir', 'Musholla', 'Toilet', '+2'),
                        array('Badminton', 'Parkir', 'Musholla', 'Kantin'),
                        array('Mini Soccer', 'Parkir', 'Toilet', 'Kantin', '+1'),
                    );
                    $distances = array('1.2 km', '2.4 km', '2.8 km');
                    $type = isset($venue['type']) ? $venue['type'] : $sportTypes[$index % count($sportTypes)];
                    $features = isset($venue['features']) ? $venue['features'] : $featureSets[$index % count($featureSets)];
                    $distance = isset($venue['distance']) ? $venue['distance'] : $distances[$index % count($distances)];
                    $priceNumber = (int) preg_replace('/[^0-9]/', '', $venue['price']);
                    $distanceNumber = (float) preg_replace('/[^0-9.]/', '', $distance);
                    $availableDays = isset($venue['availableDays']) ? $venue['availableDays'] : array(0, 1, 2, 3, 4, 5, 6);
                    $availableDates = isset($venue['availableDates']) ? $venue['availableDates'] : array();
                    $availableTimes = isset($venue['availableTimes']) ? $venue['availableTimes'] : array('08:00 - 09:00', '10:00 - 11:00', '18:00 - 19:00');
                    $availableSlots = isset($venue['availableSlots']) ? $venue['availableSlots'] : array();
                    $availableSchedules = isset($venue['availableSchedules']) && is_array($venue['availableSchedules']) ? $venue['availableSchedules'] : array();
                ?>
                <article
                    class="field-result-card"
                    id="lapangan-<?php echo e(isset($venue['id']) ? $venue['id'] : $index); ?>"
                    data-field-id="<?php echo e(isset($venue['id']) ? $venue['id'] : ''); ?>"
                    data-detail-url="<?php echo e(app_url('dashboard/lapangan/' . rawurlencode(isset($venue['id']) ? $venue['id'] : ''))); ?>"
                    data-name="<?php echo e($venue['name']); ?>"
                    data-location="<?php echo e($venue['location']); ?>"
                    data-sport="<?php echo e($type); ?>"
                    data-features="<?php echo e(implode('|', $features)); ?>"
                    data-price="<?php echo e($priceNumber); ?>"
                    data-distance="<?php echo e($distanceNumber); ?>"
                    data-rating="<?php echo e($venue['rating']); ?>"
                    data-days="<?php echo e(implode(',', $availableDays)); ?>"
                    data-dates="<?php echo e(implode('|', $availableDates)); ?>"
                    data-times="<?php echo e(implode('|', $availableTimes)); ?>"
                    data-slots="<?php echo e(implode('|', $availableSlots)); ?>"
                    role="link"
                    tabindex="0"
                    aria-label="Buka halaman detail dan jadwal <?php echo e($venue['name']); ?>"
                >
                    <div class="field-result-media">
                        <img src="<?php echo e($venue['image']); ?>" alt="<?php echo e($venue['name']); ?>">
                        <span>Populer</span>
                        <form method="post" action="<?php echo e(app_url('dashboard/favorit/toggle')); ?>">
                            <input type="hidden" name="id_lapangan" value="<?php echo e(isset($venue['id']) ? $venue['id'] : ''); ?>">
                            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
                            <button type="submit" aria-label="Tambah <?php echo e($venue['name']); ?> ke favorit">&#9825;</button>
                        </form>
                    </div>

                    <div class="field-result-content">
                        <h2><?php echo e($venue['name']); ?></h2>
                        <p class="field-result-location"><span>&#9906;</span><?php echo e($venue['location']); ?></p>
                        <div class="field-result-tags">
                            <?php foreach ($features as $feature): ?>
                                <span><?php echo e($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="field-result-rating">
                            <span class="star">&#9733;</span>
                            <span><?php echo e($venue['rating']); ?> (<?php echo e($venue['reviews']); ?>)</span>
                            <i></i>
                            <span>&#9201; <?php echo e($distance); ?></span>
                        </div>
                    </div>

                    <div class="field-result-price">
                        <small>Harga Mulai Dari</small>
                        <strong><?php echo e($venue['price']); ?> <span>/jam</span></strong>
                        <div>
                            <a class="field-detail-button" data-field-open href="<?php echo e(app_url('dashboard/lapangan/' . rawurlencode(isset($venue['id']) ? $venue['id'] : ''))); ?>">Lihat Detail</a>
                            <a class="field-book-button" data-field-open data-focus-schedules href="<?php echo e(app_url('dashboard/lapangan/' . rawurlencode(isset($venue['id']) ? $venue['id'] : '') . '#customerFieldBooking')); ?>">Pilih Jadwal</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <div class="field-results-empty" id="fieldResultsEmpty" hidden>
                <span aria-hidden="true">&#128269;</span>
                <strong>Lapangan tidak ditemukan</strong>
                <p>Coba ubah atau hapus beberapa filter pencarian.</p>
                <button type="button" id="fieldEmptyReset">Hapus Semua Filter</button>
            </div>
        </section>
    </main>
</div>

<div class="field-detail-modal" id="fieldDetailModal" hidden>
    <div class="field-detail-backdrop" data-field-close></div>
    <section class="field-detail-dialog" role="dialog" aria-modal="true" aria-labelledby="fieldDetailTitle">
        <button type="button" class="field-detail-close" data-field-close aria-label="Tutup detail lapangan">&#215;</button>
        <div class="field-detail-hero">
            <img id="fieldDetailImage" src="" alt="">
            <span id="fieldDetailType">Olahraga</span>
        </div>
        <div class="field-detail-body">
            <div class="field-detail-heading">
                <div>
                    <h2 id="fieldDetailTitle">Detail Lapangan</h2>
                    <p id="fieldDetailLocation"></p>
                </div>
                <div class="field-detail-price">
                    <small>Harga mulai</small>
                    <strong id="fieldDetailPrice">Rp0</strong>
                    <span>/jam</span>
                </div>
            </div>
            <p class="field-detail-description" id="fieldDetailDescription"></p>
            <div class="field-detail-rating" id="fieldDetailRating"></div>
            <div class="field-detail-facilities" id="fieldDetailFacilities" aria-label="Fasilitas lapangan"></div>
            <section class="field-detail-schedules" id="fieldDetailSchedules" tabindex="-1">
                <header>
                    <div>
                        <h3>Jadwal Tersedia</h3>
                        <p>Pilih waktu bermain, lalu booking langsung.</p>
                    </div>
                    <span id="fieldScheduleCount">0 slot</span>
                </header>
                <div class="field-detail-schedule-list" id="fieldDetailScheduleList"></div>
            </section>
        </div>
    </section>
</div>

<script>
    (function () {
        var toggle = document.getElementById('fieldFilterToggle');
        var panel = document.getElementById('fieldAdvancedFilters');
        var closeButton = panel ? panel.querySelector('.field-filter-close') : null;
        var controls = {
            location: document.getElementById('fieldLocationFilter'),
            sport: document.getElementById('fieldSportFilter'),
            date: document.getElementById('fieldDateFilter'),
            time: document.getElementById('fieldTimeFilter'),
            minPrice: document.getElementById('fieldMinPriceFilter'),
            maxPrice: document.getElementById('fieldMaxPriceFilter'),
            facility: document.getElementById('fieldFacilityFilter')
        };
        var sortControl = document.getElementById('fieldSortControl');
        var resultList = document.getElementById('lapangan-populer');
        var cards = resultList ? Array.prototype.slice.call(resultList.querySelectorAll('.field-result-card')) : [];
        var resultCount = document.getElementById('fieldResultCount');
        var emptyState = document.getElementById('fieldResultsEmpty');
        var emptyReset = document.getElementById('fieldEmptyReset');
        var activeFilters = document.getElementById('fieldActiveFilters');
        var activeFilterList = document.getElementById('fieldActiveFilterList');
        var clearFilters = document.getElementById('fieldClearFilters');
        var detailModal = document.getElementById('fieldDetailModal');
        var detailScheduleList = document.getElementById('fieldDetailScheduleList');
        var detailSchedules = document.getElementById('fieldDetailSchedules');
        var venueDetails = <?php echo json_encode($venueDetails, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
        var bookingCreateUrl = <?php echo json_encode(app_url('dashboard/booking/tambah'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?>;
        var bookingToken = <?php echo json_encode(isset($bookingCsrfToken) ? $bookingCsrfToken : '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var lastFieldTrigger = null;

        if (!toggle || !panel || !resultList || !detailModal) {
            return;
        }

        function appendSchedule(detail, schedule) {
            var form = document.createElement('form');
            var scheduleInput = document.createElement('input');
            var tokenInput = document.createElement('input');
            var button = document.createElement('button');
            var info = document.createElement('span');
            var date = document.createElement('strong');
            var time = document.createElement('small');
            var price = document.createElement('b');
            var action = document.createElement('em');

            form.method = 'post';
            form.action = bookingCreateUrl;
            scheduleInput.type = 'hidden';
            scheduleInput.name = 'id_jadwal';
            scheduleInput.value = schedule.id;
            tokenInput.type = 'hidden';
            tokenInput.name = 'booking_token';
            tokenInput.value = bookingToken;
            button.type = 'submit';
            button.setAttribute('aria-label', 'Booking ' + detail.name + ', ' + schedule.dateLabel + ' ' + schedule.time);
            date.textContent = schedule.dateLabel;
            time.textContent = schedule.time;
            price.textContent = schedule.price;
            action.textContent = 'Booking';

            info.appendChild(date);
            info.appendChild(time);
            button.appendChild(info);
            button.appendChild(price);
            button.appendChild(action);
            form.appendChild(scheduleInput);
            form.appendChild(tokenInput);
            form.appendChild(button);
            detailScheduleList.appendChild(form);
        }

        function openFieldDetail(card, focusSchedules) {
            if (card && card.dataset.detailUrl) {
                window.location.href = card.dataset.detailUrl + (focusSchedules ? '#customerFieldBooking' : '');
                return;
            }

            var detail = card ? venueDetails[card.dataset.fieldId] : null;
            if (!detail) { return; }

            lastFieldTrigger = document.activeElement;
            document.getElementById('fieldDetailTitle').textContent = detail.name;
            document.getElementById('fieldDetailLocation').textContent = '⌖ ' + detail.location;
            document.getElementById('fieldDetailType').textContent = detail.type || 'Olahraga';
            document.getElementById('fieldDetailPrice').textContent = detail.price;
            document.getElementById('fieldDetailDescription').textContent = detail.description || 'Lapangan siap digunakan untuk jadwal bermain Anda.';
            document.getElementById('fieldDetailRating').textContent = '★ ' + detail.rating + ' (' + detail.reviews + ')  •  ' + detail.distance;

            var image = document.getElementById('fieldDetailImage');
            image.src = detail.image;
            image.alt = 'Foto ' + detail.name;

            var facilities = document.getElementById('fieldDetailFacilities');
            facilities.textContent = '';
            (detail.features || []).forEach(function (feature) {
                var item = document.createElement('span');
                item.textContent = feature;
                facilities.appendChild(item);
            });

            detailScheduleList.textContent = '';
            var schedules = detail.schedules || [];
            document.getElementById('fieldScheduleCount').textContent = schedules.length + ' slot';
            if (schedules.length) {
                schedules.forEach(function (schedule) { appendSchedule(detail, schedule); });
            } else {
                var empty = document.createElement('div');
                empty.className = 'field-detail-schedule-empty';
                empty.innerHTML = '<span aria-hidden="true">&#128197;</span><strong>Belum ada jadwal tersedia</strong><p>Silakan cek kembali setelah pemilik lapangan menambahkan jadwal.</p>';
                detailScheduleList.appendChild(empty);
            }

            detailModal.hidden = false;
            document.body.classList.add('field-detail-modal-open');
            window.setTimeout(function () {
                if (focusSchedules) {
                    detailSchedules.focus();
                } else {
                    detailModal.querySelector('.field-detail-close').focus();
                }
            }, 0);
        }

        function closeFieldDetail() {
            detailModal.hidden = true;
            document.body.classList.remove('field-detail-modal-open');
            if (lastFieldTrigger && typeof lastFieldTrigger.focus === 'function') { lastFieldTrigger.focus(); }
        }

        function normalize(value) {
            return String(value || '').trim().toLocaleLowerCase('id-ID');
        }

        function numberValue(control) {
            return control && control.value !== '' ? Number(control.value) : null;
        }

        function formatCurrency(value) {
            return 'Rp' + Number(value || 0).toLocaleString('id-ID');
        }

        function formatDate(value) {
            if (!value) {
                return '';
            }

            var selectedDate = new Date(value + 'T00:00:00');

            if (isNaN(selectedDate.getTime())) {
                return value;
            }

            return selectedDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function timeMatchesSlot(selectedTime, slot) {
            var selected = String(selectedTime || '').trim();
            var slotValue = String(slot || '').trim();

            if (!selected || !slotValue) {
                return false;
            }

            var range = slotValue.match(/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/);

            if (!range) {
                return slotValue === selected;
            }

            return selected >= range[1] && selected < range[2];
        }

        function priceLabel() {
            var minimum = numberValue(controls.minPrice);
            var maximum = numberValue(controls.maxPrice);

            if (minimum !== null && maximum !== null) {
                return formatCurrency(minimum) + ' - ' + formatCurrency(maximum);
            }

            if (minimum !== null) {
                return 'Min. ' + formatCurrency(minimum);
            }

            if (maximum !== null) {
                return 'Maks. ' + formatCurrency(maximum);
            }

            return '';
        }

        function activeFilterValues() {
            var values = [];
            var location = controls.location.value.trim();

            if (location) {
                values.push({ key: 'location', label: 'Lokasi: ' + location });
            }

            if (controls.sport.value) {
                values.push({ key: 'sport', label: controls.sport.value });
            }

            if (controls.date.value) {
                values.push({ key: 'date', label: formatDate(controls.date.value) });
            }

            if (controls.time.value) {
                values.push({ key: 'time', label: 'Waktu: ' + controls.time.value });
            }

            if (priceLabel()) {
                values.push({ key: 'price', label: priceLabel() });
            }

            if (controls.facility.value) {
                values.push({ key: 'facility', label: 'Fasilitas: ' + controls.facility.value });
            }

            return values;
        }

        function renderActiveFilters() {
            var values = activeFilterValues();

            activeFilterList.innerHTML = '';

            values.forEach(function (filter) {
                var chip = document.createElement('button');
                var closeIcon = document.createElement('i');

                chip.type = 'button';
                chip.dataset.filterKey = filter.key;
                chip.setAttribute('aria-label', 'Hapus filter ' + filter.label);
                chip.appendChild(document.createTextNode(filter.label));
                closeIcon.setAttribute('aria-hidden', 'true');
                closeIcon.innerHTML = '&#215;';
                chip.appendChild(closeIcon);
                activeFilterList.appendChild(chip);
            });

            activeFilters.hidden = values.length === 0;
        }

        function cardMatchesFilters(card) {
            var locationQuery = normalize(controls.location.value);
            var sport = normalize(controls.sport.value);
            var facility = normalize(controls.facility.value);
            var selectedTime = controls.time.value;
            var minimum = numberValue(controls.minPrice);
            var maximum = numberValue(controls.maxPrice);
            var cardPrice = Number(card.dataset.price || 0);
            var searchableLocation = normalize((card.dataset.name || '') + ' ' + (card.dataset.location || ''));
            var cardFacilities = String(card.dataset.features || '').split('|').map(normalize);
            var availableTimes = String(card.dataset.times || '').split('|');

            if (locationQuery && searchableLocation.indexOf(locationQuery) === -1) {
                return false;
            }

            if (sport && normalize(card.dataset.sport) !== sport) {
                return false;
            }

            if (facility && cardFacilities.indexOf(facility) === -1) {
                return false;
            }

            if (minimum !== null && cardPrice < minimum) {
                return false;
            }

            if (maximum !== null && cardPrice > maximum) {
                return false;
            }

            if (selectedTime && !availableTimes.some(function (time) { return timeMatchesSlot(selectedTime, time); })) {
                return false;
            }

            if (controls.date.value) {
                var selectedDate = new Date(controls.date.value + 'T00:00:00');
                var availableDates = String(card.dataset.dates || '').split('|').filter(Boolean);
                var availableDays = String(card.dataset.days || '').split(',');
                var availableSlots = String(card.dataset.slots || '').split('|').filter(Boolean);

                if (isNaN(selectedDate.getTime())) {
                    return false;
                }

                if (availableSlots.length > 0 && selectedTime) {
                    var hasMatchingSlot = availableSlots.some(function (slot) {
                        var separator = slot.indexOf('@');

                        return separator !== -1
                            && slot.slice(0, separator) === controls.date.value
                            && timeMatchesSlot(selectedTime, slot.slice(separator + 1));
                    });

                    if (!hasMatchingSlot && availableDates.indexOf(controls.date.value) !== -1) {
                        return false;
                    }
                }

                if (availableDates.length === 0 && availableDays.indexOf(String(selectedDate.getDay())) === -1) {
                    return false;
                }
            }

            return true;
        }

        function sortCards() {
            var sortBy = sortControl.value;

            cards.sort(function (firstCard, secondCard) {
                if (sortBy === 'price') {
                    return Number(firstCard.dataset.price) - Number(secondCard.dataset.price);
                }

                if (sortBy === 'rating') {
                    return Number(secondCard.dataset.rating) - Number(firstCard.dataset.rating);
                }

                return Number(firstCard.dataset.distance) - Number(secondCard.dataset.distance);
            });

            cards.forEach(function (card) {
                resultList.appendChild(card);
            });

            resultList.appendChild(emptyState);
        }

        function applyFilters() {
            var visibleCount = 0;

            sortCards();

            cards.forEach(function (card) {
                var visible = cardMatchesFilters(card);

                card.hidden = !visible;
                if (visible) {
                    visibleCount += 1;
                }
            });

            resultCount.textContent = visibleCount;
            emptyState.hidden = visibleCount !== 0;
            renderActiveFilters();
        }

        function clearFilter(key) {
            if (key === 'price') {
                controls.minPrice.value = '';
                controls.maxPrice.value = '';
            } else if (controls[key]) {
                controls[key].value = '';
            }

            applyFilters();
        }

        function clearAllFilters() {
            Object.keys(controls).forEach(function (key) {
                controls[key].value = '';
            });

            applyFilters();
        }

        function setFilterPanel(open) {
            panel.hidden = !open;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.classList.toggle('active', open);
        }

        toggle.addEventListener('click', function () {
            setFilterPanel(toggle.getAttribute('aria-expanded') !== 'true');
        });

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                setFilterPanel(false);
                toggle.focus();
            });
        }

        Object.keys(controls).forEach(function (key) {
            var eventName = controls[key].tagName === 'SELECT' || controls[key].type === 'date' ? 'change' : 'input';
            controls[key].addEventListener(eventName, applyFilters);
        });

        sortControl.addEventListener('change', applyFilters);

        resultList.addEventListener('click', function (event) {
            var card = event.target.closest('.field-result-card');
            var opener = event.target.closest('[data-field-open]');
            if (!card) { return; }
            if (event.target.closest('form') && !opener) { return; }
            if (event.target.closest('a, button, input, select, textarea, summary') && !opener) { return; }
            openFieldDetail(card, Boolean(opener && opener.hasAttribute('data-focus-schedules')));
        });

        cards.forEach(function (card) {
            card.addEventListener('keydown', function (event) {
                if ((event.key === 'Enter' || event.key === ' ') && event.target === card) {
                    event.preventDefault();
                    openFieldDetail(card, false);
                }
            });
        });

        detailModal.querySelectorAll('[data-field-close]').forEach(function (button) {
            button.addEventListener('click', closeFieldDetail);
        });

        activeFilterList.addEventListener('click', function (event) {
            var chip = event.target.closest('[data-filter-key]');

            if (chip) {
                clearFilter(chip.dataset.filterKey);
            }
        });

        clearFilters.addEventListener('click', clearAllFilters);
        emptyReset.addEventListener('click', clearAllFilters);

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') { return; }
            if (!detailModal.hidden) {
                closeFieldDetail();
            } else if (toggle.getAttribute('aria-expanded') === 'true') {
                setFilterPanel(false);
                toggle.focus();
            }
        });

        var query = new URLSearchParams(window.location.search);
        var initialSearch = query.get('q');
        if (initialSearch) { controls.location.value = initialSearch; }
        var requestedField = query.get('lapangan');
        var requestedFieldCard = requestedField ? cards.find(function (card) { return card.dataset.fieldId === requestedField; }) : null;
        if (requestedFieldCard) {
            openFieldDetail(requestedFieldCard, false);
        }
        applyFilters();
    }());
</script>
