<?php
$bookingCounts = array('upcoming' => 0, 'completed' => 0, 'cancelled' => 0);
$rescheduleSchedules = array();

foreach (isset($venues) ? $venues : array() as $venue) {
    if (!empty($venue['id'])) {
        $rescheduleSchedules[$venue['id']] = isset($venue['availableSchedules']) ? $venue['availableSchedules'] : array();
    }
}

foreach ($bookings as $bookingItem) {
    $bookingCategory = isset($bookingItem['category']) ? $bookingItem['category'] : 'upcoming';

    if ($bookingCategory === 'pending') {
        $bookingCategory = 'upcoming';
    }

    if (isset($bookingCounts[$bookingCategory])) {
        $bookingCounts[$bookingCategory]++;
    }
}
?>

<div
    class="dashboard-shell profile-dashboard booking-dashboard"
    id="bookingDashboard"
    data-review-url="<?php echo e(app_url('dashboard/ulasan')); ?>"
    data-rebook-url="<?php echo e(app_url('dashboard/lapangan')); ?>"
>
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
            <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Booking Sekarang &#8594;</a>
        </div>

        <a class="dashboard-logout" href="<?php echo e(app_url('public/logout.php')); ?>"><span>&#8634;</span>Keluar</a>
    </aside>

    <main class="dashboard-main profile-main booking-main">
        <?php if (!empty($bookingMessage)): ?><section class="settings-alert success-message" role="status"><?php echo e($bookingMessage); ?></section><?php endif; ?>
        <?php if (!empty($bookingError)): ?><section class="settings-alert error-message" role="alert"><?php echo e($bookingError); ?></section><?php endif; ?>
        <section class="profile-page-head booking-page-head">
            <div>
                <h1><?php echo e($pageHeading); ?></h1>
                <p><?php echo e($pageSubheading); ?></p>
            </div>
            <div class="profile-head-actions booking-head-actions">
                <div class="booking-head-menu">
                    <button type="button" class="profile-notification" id="bookingNotificationToggle" aria-label="Notifikasi" aria-expanded="false" aria-controls="bookingNotificationMenu">
                        <span>&#128276;</span>
                        <sup>1</sup>
                    </button>
                    <div class="booking-head-popover notification" id="bookingNotificationMenu" hidden>
                        <strong>Pengingat Booking</strong>
                        <p>Datang 15 menit lebih awal dan tunjukkan kode booking di lokasi.</p>
                    </div>
                </div>
                <div class="booking-head-menu">
                    <button type="button" class="profile-account-menu" id="bookingAccountToggle" aria-label="Buka menu akun" aria-expanded="false" aria-controls="bookingAccountMenu">
                        <img src="<?php echo e($userAvatar); ?>" alt="Foto profil">
                        <span>&#8964;</span>
                    </button>
                    <div class="booking-head-popover account" id="bookingAccountMenu" hidden>
                        <a href="<?php echo e(app_url('dashboard/profil')); ?>">Profil Saya</a>
                        <a href="<?php echo e(app_url('settings')); ?>">Pengaturan</a>
                        <a href="<?php echo e(app_url('public/logout.php')); ?>">Keluar</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="booking-toolbar" aria-label="Filter booking">
            <nav class="booking-filter-tabs" aria-label="Status booking" role="tablist">
                <button type="button" class="active" role="tab" aria-selected="true" aria-controls="booking-list" data-booking-filter="upcoming">Mendatang <small><?php echo e($bookingCounts['upcoming']); ?></small></button>
                <button type="button" role="tab" aria-selected="false" aria-controls="booking-list" data-booking-filter="completed">Selesai <small><?php echo e($bookingCounts['completed']); ?></small></button>
                <button type="button" role="tab" aria-selected="false" aria-controls="booking-list" data-booking-filter="cancelled">Dibatalkan <small><?php echo e($bookingCounts['cancelled']); ?></small></button>
            </nav>
            <label class="booking-sort-control">
                <span>Urutkan:</span>
                <select id="bookingSortControl" aria-label="Urutkan booking">
                    <option value="nearest">Terdekat</option>
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                </select>
            </label>
        </section>

        <section class="booking-info-banner">
            <span>&#128197;</span>
            <div>
                <h2>Punya rencana main?</h2>
                <p>Jangan lupa datang 15 menit sebelum jadwal booking kamu.</p>
            </div>
            <button type="button" id="bookingRulesButton">Lihat Aturan</button>
        </section>

        <section class="booking-match-list" id="booking-list" aria-label="Daftar booking saya">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $statusClass = isset($booking['statusClass']) ? $booking['statusClass'] : 'upcoming';
                    $buttonText = isset($booking['button']) ? $booking['button'] : 'Ubah Booking';
                    $category = isset($booking['category']) ? $booking['category'] : 'upcoming';
                    $category = $category === 'pending' ? 'upcoming' : $category;
                    $action = isset($booking['action']) ? $booking['action'] : 'edit';
                    $dateValue = isset($booking['dateValue']) ? $booking['dateValue'] : '';
                    $priceNumber = (int) preg_replace('/[^0-9]/', '', $booking['price']);
                ?>
                <article
                    class="booking-match-card"
                    data-booking-code="<?php echo e($booking['code']); ?>"
                    data-field-id="<?php echo e(isset($booking['fieldId']) ? $booking['fieldId'] : ''); ?>"
                    data-category="<?php echo e($category); ?>"
                    data-action="<?php echo e($action); ?>"
                    data-venue="<?php echo e($booking['venue']); ?>"
                    data-type="<?php echo e($booking['type']); ?>"
                    data-location="<?php echo e($booking['location']); ?>"
                    data-date="<?php echo e($dateValue); ?>"
                    data-date-label="<?php echo e($booking['date']); ?>"
                    data-time="<?php echo e($booking['time']); ?>"
                    data-duration="<?php echo e($booking['duration']); ?>"
                    data-price="<?php echo e($priceNumber); ?>"
                    data-price-label="<?php echo e($booking['price']); ?>"
                    data-status="<?php echo e($booking['status']); ?>"
                    data-status-class="<?php echo e($statusClass); ?>"
                >
                    <div class="booking-match-media">
                        <img src="<?php echo e($booking['image']); ?>" alt="<?php echo e($booking['venue']); ?>">
                        <span><?php echo e($booking['type']); ?></span>
                    </div>

                    <div class="booking-match-content">
                        <h2><?php echo e($booking['venue']); ?></h2>
                        <p class="booking-location"><span>&#9906;</span><?php echo e($booking['location']); ?></p>
                        <div class="booking-match-meta">
                            <span class="booking-card-date">&#128197; <?php echo e($booking['date']); ?></span>
                            <span class="booking-card-time">&#9201; <?php echo e($booking['time']); ?></span>
                            <span>&#9711; <?php echo e($booking['duration']); ?></span>
                        </div>
                        <div class="booking-code-row">
                            <small>Kode Booking</small>
                            <strong><?php echo e($booking['code']); ?></strong>
                            <button type="button" class="booking-copy-code" data-copy-code="<?php echo e($booking['code']); ?>" aria-label="Salin kode booking <?php echo e($booking['code']); ?>">&#128203;</button>
                        </div>
                    </div>

                    <div class="booking-payment-panel">
                        <span class="booking-status-pill <?php echo e($statusClass); ?>"><?php echo e($booking['status']); ?></span>
                        <div>
                            <small>Total Pembayaran</small>
                            <strong><?php echo e($booking['price']); ?></strong>
                        </div>
                    </div>

                    <div class="booking-card-actions">
                        <button type="button" class="booking-detail-button">Lihat Detail</button>
                        <button type="button" class="booking-primary-button" data-booking-action="<?php echo e($action); ?>"><?php echo e($buttonText); ?></button>
                    </div>

                    <button type="button" class="booking-row-arrow" aria-label="Lihat detail <?php echo e($booking['venue']); ?>">&#8250;</button>
                </article>
            <?php endforeach; ?>

            <div class="booking-empty-state" id="bookingEmptyState" hidden>
                <span aria-hidden="true">&#128197;</span>
                <strong>Belum ada booking pada status ini</strong>
                <p>Pilih tab lain atau cari lapangan untuk membuat booking baru.</p>
                <a href="<?php echo e(app_url('dashboard/lapangan')); ?>">Cari Lapangan</a>
            </div>
        </section>

        <section class="booking-footnote">
            <p>Tidak menemukan booking? Lihat di <a href="<?php echo e(app_url('dashboard/riwayat')); ?>">Riwayat</a></p>
        </section>
    </main>
</div>

<div class="booking-modal" id="bookingRulesModal" hidden>
    <section class="booking-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="bookingRulesTitle">
        <header class="booking-modal-head">
            <div>
                <span class="booking-modal-icon" aria-hidden="true">&#128203;</span>
                <div>
                    <h2 id="bookingRulesTitle">Aturan Booking</h2>
                    <p>Supaya kegiatan bermain berjalan nyaman dan tertib.</p>
                </div>
            </div>
            <button type="button" data-booking-modal-close aria-label="Tutup aturan booking">&#215;</button>
        </header>
        <ol class="booking-rules-list">
            <li>Datang paling lambat 15 menit sebelum jadwal dimulai.</li>
            <li>Tunjukkan kode booking kepada petugas lapangan.</li>
            <li>Perubahan jadwal dilakukan sebelum waktu bermain dimulai.</li>
            <li>Jaga kebersihan dan patuhi peraturan yang berlaku di lokasi.</li>
            <li>Pembatalan dapat mengikuti ketentuan pengembalian dana pengelola.</li>
        </ol>
        <div class="booking-modal-actions single">
            <button type="button" class="booking-modal-primary" data-booking-modal-close>Saya Mengerti</button>
        </div>
    </section>
</div>

<div class="booking-modal" id="bookingDetailModal" hidden>
    <section class="booking-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="bookingDetailTitle">
        <header class="booking-modal-head">
            <div>
                <span class="booking-modal-icon" aria-hidden="true">&#128197;</span>
                <div>
                    <h2 id="bookingDetailTitle">Detail Booking</h2>
                    <p id="bookingDetailCode">-</p>
                </div>
            </div>
            <button type="button" data-booking-modal-close aria-label="Tutup detail booking">&#215;</button>
        </header>
        <dl class="booking-detail-list">
            <div><dt>Lapangan</dt><dd id="bookingDetailVenue">-</dd></div>
            <div><dt>Lokasi</dt><dd id="bookingDetailLocation">-</dd></div>
            <div><dt>Olahraga</dt><dd id="bookingDetailType">-</dd></div>
            <div><dt>Tanggal</dt><dd id="bookingDetailDate">-</dd></div>
            <div><dt>Waktu</dt><dd id="bookingDetailTime">-</dd></div>
            <div><dt>Durasi</dt><dd id="bookingDetailDuration">-</dd></div>
            <div><dt>Total</dt><dd id="bookingDetailPrice">-</dd></div>
            <div><dt>Status</dt><dd><span class="booking-status-pill" id="bookingDetailStatus">-</span></dd></div>
        </dl>
        <div class="booking-modal-actions single">
            <button type="button" class="booking-modal-primary" data-booking-modal-close>Tutup</button>
        </div>
    </section>
</div>

<div class="booking-modal" id="bookingEditModal" hidden>
    <section class="booking-modal-dialog compact" role="dialog" aria-modal="true" aria-labelledby="bookingEditTitle">
        <header class="booking-modal-head">
            <div>
                <span class="booking-modal-icon" aria-hidden="true">&#9201;</span>
                <div>
                    <h2 id="bookingEditTitle">Ubah Jadwal Booking</h2>
                    <p id="bookingEditVenue">-</p>
                </div>
            </div>
            <button type="button" data-booking-modal-close aria-label="Tutup ubah booking">&#215;</button>
        </header>
        <form id="bookingEditForm" class="booking-modal-form" method="post" action="<?php echo e(app_url('dashboard/booking/update')); ?>">
            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
            <input type="hidden" id="bookingEditCode" name="id_booking" value="">
            <input type="hidden" id="bookingEditAction" name="booking_action" value="reschedule">
            <label>
                <span>Jadwal Baru</span>
                <select id="bookingEditSlot" name="booking_slot" required>
                    <option value="">Pilih jadwal tersedia</option>
                </select>
            </label>
            <p class="booking-modal-note">Perubahan hanya berhasil jika slot jadwal tersedia di database.</p>
            <div class="booking-modal-actions split">
                <button type="button" class="booking-modal-danger" id="bookingCancelButton">Batalkan Booking</button>
                <button type="submit" class="booking-modal-primary">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</div>

<div class="booking-modal" id="bookingPaymentModal" hidden>
    <section class="booking-modal-dialog compact" role="dialog" aria-modal="true" aria-labelledby="bookingPaymentTitle">
        <header class="booking-modal-head">
            <div>
                <span class="booking-modal-icon" aria-hidden="true">&#128179;</span>
                <div>
                    <h2 id="bookingPaymentTitle">Pembayaran Booking</h2>
                    <p id="bookingPaymentCode">-</p>
                </div>
            </div>
            <button type="button" data-booking-modal-close aria-label="Tutup pembayaran">&#215;</button>
        </header>
        <form id="bookingPaymentForm" class="booking-modal-form" method="post" action="<?php echo e(app_url('dashboard/booking/bayar')); ?>">
            <input type="hidden" name="booking_token" value="<?php echo e(isset($bookingCsrfToken) ? $bookingCsrfToken : ''); ?>">
            <input type="hidden" id="bookingPaymentBookingId" name="id_booking" value="">
            <div class="booking-payment-summary">
                <span>Total yang harus dibayar</span>
                <strong id="bookingPaymentTotal">-</strong>
            </div>
            <label>
                <span>Metode Pembayaran</span>
                <select id="bookingPaymentMethod" name="payment_method" required>
                    <option value="">Pilih metode pembayaran</option>
                    <?php foreach (isset($paymentMethods) ? $paymentMethods : array() as $method): ?>
                        <option value="<?php echo e($method['ID_Metode']); ?>"><?php echo e($method['Nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <p class="booking-modal-note">Pembayaran yang dikonfirmasi akan tersimpan pada riwayat transaksi.</p>
            <div class="booking-modal-actions split">
                <button type="button" class="booking-modal-danger" id="bookingPendingCancelButton">Batalkan Booking</button>
                <button type="submit" class="booking-modal-primary">Konfirmasi Pembayaran</button>
            </div>
        </form>
    </section>
</div>

<div class="booking-toast" id="bookingToast" role="status" aria-live="polite" hidden></div>

<script>
    (function () {
        var dashboard = document.getElementById('bookingDashboard');
        var list = document.getElementById('booking-list');
        var cards = list ? Array.prototype.slice.call(list.querySelectorAll('.booking-match-card')) : [];
        var filterButtons = Array.prototype.slice.call(document.querySelectorAll('[data-booking-filter]'));
        var sortControl = document.getElementById('bookingSortControl');
        var emptyState = document.getElementById('bookingEmptyState');
        var rulesButton = document.getElementById('bookingRulesButton');
        var rulesModal = document.getElementById('bookingRulesModal');
        var detailModal = document.getElementById('bookingDetailModal');
        var editModal = document.getElementById('bookingEditModal');
        var paymentModal = document.getElementById('bookingPaymentModal');
        var editForm = document.getElementById('bookingEditForm');
        var paymentForm = document.getElementById('bookingPaymentForm');
        var cancelButton = document.getElementById('bookingCancelButton');
        var pendingCancelButton = document.getElementById('bookingPendingCancelButton');
        var toast = document.getElementById('bookingToast');
        var activeFilter = 'upcoming';
        var selectedCard = null;
        var lastFocusedElement = null;
        var toastTimer = null;
        var storageKey = 'arenaSportBookingStateV1';
        var rescheduleSchedules = <?php echo json_encode($rescheduleSchedules, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        if (!dashboard || !list) {
            return;
        }

        function formatDate(value) {
            var date = new Date(value + 'T00:00:00');

            if (!value || isNaN(date.getTime())) {
                return value || '-';
            }

            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function showToast(message) {
            window.clearTimeout(toastTimer);
            toast.textContent = message;
            toast.hidden = false;
            toastTimer = window.setTimeout(function () {
                toast.hidden = true;
            }, 2600);
        }

        function readState() {
            try {
                return JSON.parse(window.localStorage.getItem(storageKey) || '{}');
            } catch (error) {
                return {};
            }
        }

        function saveCardState(card) {
            var state = readState();
            var code = card.dataset.bookingCode;

            state[code] = {
                category: card.dataset.category,
                action: card.dataset.action,
                date: card.dataset.date,
                dateLabel: card.dataset.dateLabel,
                time: card.dataset.time,
                status: card.dataset.status,
                statusClass: card.dataset.statusClass
            };

            try {
                window.localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (error) {
                showToast('Perubahan diterapkan, tetapi belum dapat disimpan di browser.');
            }
        }

        function actionLabel(action) {
            var labels = {
                edit: 'Ubah Booking',
                pay: 'Bayar Sekarang',
                review: 'Beri Ulasan',
                rebook: 'Booking Lagi'
            };

            return labels[action] || 'Lihat Detail';
        }

        function syncCard(card) {
            var dateElement = card.querySelector('.booking-card-date');
            var timeElement = card.querySelector('.booking-card-time');
            var statusElement = card.querySelector('.booking-status-pill');
            var primaryButton = card.querySelector('.booking-primary-button');

            dateElement.innerHTML = '&#128197; ' + card.dataset.dateLabel;
            timeElement.innerHTML = '&#9201; ' + card.dataset.time;
            statusElement.className = 'booking-status-pill ' + card.dataset.statusClass;
            statusElement.textContent = card.dataset.status;
            primaryButton.dataset.bookingAction = card.dataset.action;
            primaryButton.textContent = actionLabel(card.dataset.action);
        }

        function restoreSavedState() {
            cards.forEach(function (card) {
                syncCard(card);
            });
        }

        function updateTabCounts() {
            filterButtons.forEach(function (button) {
                var count = cards.filter(function (card) {
                    return card.dataset.category === button.dataset.bookingFilter;
                }).length;
                var countElement = button.querySelector('small');

                if (countElement) {
                    countElement.textContent = count;
                }
            });
        }

        function sortCards() {
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            cards.sort(function (firstCard, secondCard) {
                var firstDate = new Date(firstCard.dataset.date + 'T00:00:00');
                var secondDate = new Date(secondCard.dataset.date + 'T00:00:00');

                if (sortControl.value === 'newest') {
                    return secondDate - firstDate;
                }

                if (sortControl.value === 'oldest') {
                    return firstDate - secondDate;
                }

                return Math.abs(firstDate - today) - Math.abs(secondDate - today);
            });

            cards.forEach(function (card) {
                list.appendChild(card);
            });
            list.appendChild(emptyState);
        }

        function applyView() {
            var visibleCount = 0;

            sortCards();
            cards.forEach(function (card) {
                var visible = card.dataset.category === activeFilter;
                card.hidden = !visible;

                if (visible) {
                    visibleCount++;
                }
            });

            emptyState.hidden = visibleCount !== 0;
            updateTabCounts();
        }

        function setModal(modal, open) {
            if (!modal) {
                return;
            }

            if (open) {
                lastFocusedElement = document.activeElement;
                modal.hidden = false;
                document.body.classList.add('booking-modal-open');
                window.setTimeout(function () {
                    var focusTarget = modal.querySelector('button, input, select');
                    if (focusTarget) {
                        focusTarget.focus();
                    }
                }, 0);
                return;
            }

            modal.hidden = true;
            if (!document.querySelector('.booking-modal:not([hidden])')) {
                document.body.classList.remove('booking-modal-open');
            }
            if (lastFocusedElement) {
                lastFocusedElement.focus();
            }
        }

        function openDetail(card) {
            selectedCard = card;
            document.getElementById('bookingDetailCode').textContent = card.dataset.bookingCode;
            document.getElementById('bookingDetailVenue').textContent = card.dataset.venue;
            document.getElementById('bookingDetailLocation').textContent = card.dataset.location;
            document.getElementById('bookingDetailType').textContent = card.dataset.type;
            document.getElementById('bookingDetailDate').textContent = card.dataset.dateLabel;
            document.getElementById('bookingDetailTime').textContent = card.dataset.time;
            document.getElementById('bookingDetailDuration').textContent = card.dataset.duration;
            document.getElementById('bookingDetailPrice').textContent = card.dataset.priceLabel;

            var status = document.getElementById('bookingDetailStatus');
            status.className = 'booking-status-pill ' + card.dataset.statusClass;
            status.textContent = card.dataset.status;
            setModal(detailModal, true);
        }

        function openEdit(card) {
            selectedCard = card;
            document.getElementById('bookingEditVenue').textContent = card.dataset.venue + ' • ' + card.dataset.bookingCode;
            var slotControl = document.getElementById('bookingEditSlot');
            var schedules = rescheduleSchedules[card.dataset.fieldId] || [];
            slotControl.innerHTML = '<option value="">Pilih jadwal tersedia</option>';
            schedules.forEach(function (schedule) {
                var option = document.createElement('option');
                option.value = schedule.date + '@' + schedule.time;
                option.textContent = schedule.dateLabel + ' - ' + schedule.time + ' (' + schedule.price + ')';
                slotControl.appendChild(option);
            });
            if (!schedules.length) {
                var emptyOption = document.createElement('option');
                emptyOption.disabled = true;
                emptyOption.textContent = 'Belum ada jadwal pengganti';
                slotControl.appendChild(emptyOption);
            }
            document.getElementById('bookingEditCode').value = card.dataset.bookingCode;
            document.getElementById('bookingEditAction').value = 'reschedule';
            setModal(editModal, true);
        }

        function openPayment(card) {
            selectedCard = card;
            document.getElementById('bookingPaymentCode').textContent = card.dataset.venue + ' • ' + card.dataset.bookingCode;
            document.getElementById('bookingPaymentTotal').textContent = card.dataset.priceLabel;
            document.getElementById('bookingPaymentMethod').value = '';
            document.getElementById('bookingPaymentBookingId').value = card.dataset.bookingCode;
            setModal(paymentModal, true);
        }

        function copyText(value) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(value);
            }

            return new Promise(function (resolve, reject) {
                var textArea = document.createElement('textarea');
                textArea.value = value;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();

                try {
                    document.execCommand('copy');
                    resolve();
                } catch (error) {
                    reject(error);
                }

                textArea.remove();
            });
        }

        function setupHeadMenu(toggleId, menuId) {
            var toggle = document.getElementById(toggleId);
            var menu = document.getElementById(menuId);

            if (!toggle || !menu) {
                return;
            }

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var willOpen = menu.hidden;

                document.querySelectorAll('.booking-head-popover').forEach(function (popover) {
                    popover.hidden = true;
                });
                document.querySelectorAll('.booking-head-menu > button').forEach(function (button) {
                    button.setAttribute('aria-expanded', 'false');
                });

                menu.hidden = !willOpen;
                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });

            menu.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeFilter = button.dataset.bookingFilter;
                filterButtons.forEach(function (tab) {
                    var active = tab === button;
                    tab.classList.toggle('active', active);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                applyView();
            });
        });

        sortControl.addEventListener('change', applyView);
        rulesButton.addEventListener('click', function () {
            setModal(rulesModal, true);
        });

        list.addEventListener('click', function (event) {
            var card = event.target.closest('.booking-match-card');
            var copyButton = event.target.closest('.booking-copy-code');
            var detailButton = event.target.closest('.booking-detail-button, .booking-row-arrow');
            var primaryButton = event.target.closest('.booking-primary-button');

            if (!card) {
                return;
            }

            if (copyButton) {
                copyText(copyButton.dataset.copyCode).then(function () {
                    showToast('Kode booking berhasil disalin.');
                }).catch(function () {
                    showToast('Kode booking belum dapat disalin.');
                });
                return;
            }

            if (detailButton) {
                openDetail(card);
                return;
            }

            if (primaryButton) {
                var action = primaryButton.dataset.bookingAction;

                if (action === 'edit') {
                    openEdit(card);
                } else if (action === 'pay') {
                    openPayment(card);
                } else if (action === 'review') {
                    window.location.href = dashboard.dataset.reviewUrl;
                } else if (action === 'rebook') {
                    window.location.href = dashboard.dataset.rebookUrl;
                }
            }
        });

        editForm.addEventListener('submit', function (event) {
            if (!selectedCard || !editForm.reportValidity()) {
                event.preventDefault();
                return;
            }
            document.getElementById('bookingEditAction').value = 'reschedule';
        });

        cancelButton.addEventListener('click', function () {
            if (!selectedCard || !window.confirm('Yakin ingin membatalkan booking ini?')) {
                return;
            }
            document.getElementById('bookingEditAction').value = 'cancel';
            editForm.submit();
        });

        pendingCancelButton.addEventListener('click', function () {
            if (!selectedCard || !window.confirm('Yakin ingin membatalkan booking ini?')) { return; }
            document.getElementById('bookingEditCode').value = selectedCard.dataset.bookingCode;
            document.getElementById('bookingEditAction').value = 'cancel';
            editForm.submit();
        });

        paymentForm.addEventListener('submit', function (event) {
            if (!selectedCard || !paymentForm.reportValidity()) {
                event.preventDefault();
                return;
            }
        });

        document.querySelectorAll('[data-booking-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                setModal(button.closest('.booking-modal'), false);
            });
        });

        document.querySelectorAll('.booking-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    setModal(modal, false);
                }
            });
        });

        setupHeadMenu('bookingNotificationToggle', 'bookingNotificationMenu');
        setupHeadMenu('bookingAccountToggle', 'bookingAccountMenu');

        var notificationToggle = document.getElementById('bookingNotificationToggle');
        var notificationBadge = notificationToggle ? notificationToggle.querySelector('sup') : null;

        try {
            if (notificationBadge && window.localStorage.getItem('arenaSportBookingNotificationRead') === '1') {
                notificationBadge.hidden = true;
            }
        } catch (error) {
            // Tampilan notifikasi tetap dapat digunakan tanpa penyimpanan browser.
        }

        if (notificationToggle && notificationBadge) {
            notificationToggle.addEventListener('click', function () {
                notificationBadge.hidden = true;

                try {
                    window.localStorage.setItem('arenaSportBookingNotificationRead', '1');
                } catch (error) {
                    // Tidak perlu menghentikan menu jika penyimpanan browser dibatasi.
                }
            });
        }

        document.addEventListener('click', function () {
            document.querySelectorAll('.booking-head-popover').forEach(function (popover) {
                popover.hidden = true;
            });
            document.querySelectorAll('.booking-head-menu > button').forEach(function (button) {
                button.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            var openModal = document.querySelector('.booking-modal:not([hidden])');
            if (openModal) {
                setModal(openModal, false);
                return;
            }

            document.querySelectorAll('.booking-head-popover').forEach(function (popover) {
                popover.hidden = true;
            });
            document.querySelectorAll('.booking-head-menu > button').forEach(function (button) {
                button.setAttribute('aria-expanded', 'false');
            });
        });

        restoreSavedState();

        var requestedBooking = new URLSearchParams(window.location.search).get('booking');
        var requestedCard = requestedBooking ? cards.find(function (card) { return card.dataset.bookingCode === requestedBooking; }) : null;
        if (requestedCard) {
            activeFilter = requestedCard.dataset.category;
            filterButtons.forEach(function (button) {
                var active = button.dataset.bookingFilter === activeFilter;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }
        applyView();
        if (requestedCard) { openDetail(requestedCard); }
    }());
</script>
