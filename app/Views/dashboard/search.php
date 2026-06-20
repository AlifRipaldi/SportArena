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
?>

<div class="dashboard-shell profile-dashboard search-dashboard">
    <aside class="dashboard-sidebar">
        <div class="dashboard-brand">
            <div class="dashboard-logo-mark">
                <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport Logo">
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
        <section class="profile-page-head search-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions">
                <button type="button" class="profile-notification" aria-label="Notifikasi">
                    <span>&#128276;</span>
                    <sup>1</sup>
                </button>
                <div class="profile-account-menu">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=120&auto=format&fit=crop" alt="Foto profil">
                    <span>&#8964;</span>
                </div>
            </div>
        </section>

        <section class="field-search-panel" aria-label="Filter pencarian lapangan">
            <div class="field-search-primary">
                <label class="field-search-control location">
                    <span>Cari Lokasi</span>
                    <div>
                        <i>&#9906;</i>
                        <input id="fieldLocationFilter" type="search" placeholder="Contoh: Parepare, Mattirotasi, Sudirman" autocomplete="off">
                        <button type="button" aria-label="Gunakan lokasi saat ini">&#9881;</button>
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
                            <select id="fieldTimeFilter">
                                <option value="">Pilih Waktu</option>
                                <?php foreach ($filterTimes as $time): ?>
                                    <option value="<?php echo e($time); ?>"><?php echo e($time); ?></option>
                                <?php endforeach; ?>
                            </select>
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
                ?>
                <article
                    class="field-result-card"
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
                >
                    <div class="field-result-media">
                        <img src="<?php echo e($venue['image']); ?>" alt="<?php echo e($venue['name']); ?>">
                        <span>Populer</span>
                        <form method="post" action="<?php echo e(app_url('dashboard/favorit/toggle')); ?>">
                            <input type="hidden" name="id_lapangan" value="<?php echo e(isset($venue['id']) ? $venue['id'] : ''); ?>">
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
                            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Lihat Detail</a>
                            <?php if (!empty($venue['bookingUrl'])): ?>
                                <a href="<?php echo e($venue['bookingUrl']); ?>" class="primary">Pilih Jadwal</a>
                            <?php else: ?>
                                <span class="primary" aria-disabled="true">Jadwal Kosong</span>
                            <?php endif; ?>
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

        if (!toggle || !panel || !resultList) {
            return;
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
                values.push({ key: 'time', label: controls.time.value });
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

            if (selectedTime && availableTimes.indexOf(selectedTime) === -1) {
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

                if (availableDates.length > 0 && availableDates.indexOf(controls.date.value) === -1) {
                    return false;
                }

                if (availableSlots.length > 0 && selectedTime && availableSlots.indexOf(controls.date.value + '@' + selectedTime) === -1) {
                    return false;
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

        activeFilterList.addEventListener('click', function (event) {
            var chip = event.target.closest('[data-filter-key]');

            if (chip) {
                clearFilter(chip.dataset.filterKey);
            }
        });

        clearFilters.addEventListener('click', clearAllFilters);
        emptyReset.addEventListener('click', clearAllFilters);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
                setFilterPanel(false);
                toggle.focus();
            }
        });

        applyFilters();
    }());
</script>
