<?php
// Owner - Lapangan Saya
$fieldPayloads = array();
$facilityOptions = array(
    array('name' => 'Parkir', 'icon' => 'fa-square-parking'),
    array('name' => 'Toilet', 'icon' => 'fa-restroom'),
    array('name' => 'Musholla', 'icon' => 'fa-mosque'),
    array('name' => 'WiFi', 'icon' => 'fa-wifi'),
    array('name' => 'Kantin', 'icon' => 'fa-store'),
    array('name' => 'CCTV', 'icon' => 'fa-video'),
    array('name' => 'Loker', 'icon' => 'fa-lock'),
    array('name' => 'Ruang Tunggu', 'icon' => 'fa-couch'),
);

foreach ($lapangan as $field) {
    $priceNumber = isset($field['priceNumber']) ? (int) $field['priceNumber'] : (int) preg_replace('/[^0-9]/', '', isset($field['price']) ? $field['price'] : '0');
    $photos = isset($field['photos']) && is_array($field['photos']) ? $field['photos'] : array();

    $fieldPayloads[$field['id']] = array(
        'id' => isset($field['id']) ? $field['id'] : '',
        'name' => isset($field['name']) ? $field['name'] : '',
        'type' => isset($field['type']) ? $field['type'] : '',
        'location' => isset($field['location']) ? $field['location'] : '',
        'price' => isset($field['price']) ? $field['price'] : 'Rp0',
        'priceNumber' => $priceNumber,
        'status' => isset($field['status']) ? $field['status'] : 'Aktif',
        'cardStatus' => isset($field['cardStatus']) ? $field['cardStatus'] : (isset($field['status']) ? $field['status'] : 'Aktif'),
        'rating' => isset($field['rating']) ? $field['rating'] : '4.8',
        'reviews' => isset($field['reviews']) ? $field['reviews'] : '0',
        'visual' => isset($field['visual']) ? $field['visual'] : 'futsal',
        'description' => isset($field['description']) ? $field['description'] : 'Arena olahraga dengan fasilitas lengkap untuk permainan yang nyaman.',
        'hours' => isset($field['hours']) ? $field['hours'] : '06:00 - 23:00 Setiap Hari',
        'facilities' => isset($field['facilities']) ? $field['facilities'] : array('Parkir', 'Toilet', 'Musholla', 'WiFi', 'Kantin', 'CCTV'),
        'photos' => $photos,
        'rules' => isset($field['rules']) ? $field['rules'] : array('Jaga kebersihan area lapangan', 'Gunakan perlengkapan olahraga yang sesuai'),
    );
}
?>

<section class="owner-lapangan-page">
    <div class="owner-lapangan-hero">
        <div>
            <h1>Lapangan Saya</h1>
            <p>Kelola semua lapangan yang Anda miliki</p>
        </div>
        <button class="btn-primary owner-add-field-btn" type="button" data-owner-field-modal-open aria-haspopup="dialog" aria-controls="ownerAddFieldModal">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Lapangan</span>
        </button>
    </div>

    <div class="owner-lapangan-grid" aria-label="Kartu lapangan saya">
        <?php foreach ($lapangan as $field): ?>
            <?php
            $cardStatus = isset($field['cardStatus']) ? $field['cardStatus'] : $field['status'];
            $cardStatusClass = strtolower($cardStatus) === 'aktif' ? 'success' : 'warning';
            $fieldPhotos = isset($field['photos']) && is_array($field['photos']) ? $field['photos'] : array();
            $cardPhotoUrl = isset($fieldPhotos[0]['url']) ? $fieldPhotos[0]['url'] : '';
            ?>
            <article class="owner-lapangan-card" data-owner-field-card="<?php echo e($field['id']); ?>">
                <div class="owner-lapangan-visual <?php echo e($field['visual']); ?> <?php echo $cardPhotoUrl !== '' ? 'has-photo' : ''; ?>">
                    <?php if ($cardPhotoUrl !== ''): ?>
                        <img class="owner-lapangan-photo" src="<?php echo e($cardPhotoUrl); ?>" alt="Foto <?php echo e($field['name']); ?>" loading="lazy">
                    <?php endif; ?>
                    <span class="admin-badge <?php echo e($cardStatusClass); ?>" data-owner-field-status><?php echo e($cardStatus); ?></span>
                    <button class="btn-icon owner-field-like" type="button" aria-label="Favorit <?php echo e($field['name']); ?>">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>

                <div class="owner-lapangan-body">
                    <h2 data-owner-field-name><?php echo e($field['name']); ?></h2>
                    <p class="owner-lapangan-location">
                        <i class="fa-solid fa-location-dot"></i>
                        <span data-owner-field-location><?php echo e($field['location']); ?></span>
                    </p>
                    <p class="owner-lapangan-rating">
                        <i class="fa-solid fa-star"></i>
                        <strong><?php echo e($field['rating']); ?></strong>
                        <span>(<?php echo e($field['reviews']); ?> ulasan)</span>
                    </p>
                    <p class="owner-lapangan-price">
                        <strong data-owner-field-price><?php echo e($field['price']); ?></strong>
                        <span>/jam</span>
                    </p>

                    <div class="owner-lapangan-actions">
                        <button class="owner-lapangan-btn" type="button" data-owner-field-manage-open data-owner-field-mode="edit" data-owner-field-id="<?php echo e($field['id']); ?>">Edit</button>
                        <button class="owner-lapangan-btn primary" type="button" data-owner-field-manage-open data-owner-field-mode="detail" data-owner-field-id="<?php echo e($field['id']); ?>">Detail</button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (empty($lapangan)): ?>
            <article class="owner-lapangan-empty">
                <i class="fa-regular fa-futbol"></i>
                <strong>Belum ada lapangan</strong>
                <span>Tambahkan lapangan pertama untuk mulai mengisi database.</span>
            </article>
        <?php endif; ?>
    </div>

    <article class="admin-panel owner-lapangan-table-panel">
        <div class="admin-panel-header owner-lapangan-table-header">
            <h2>Daftar Lapangan</h2>
        </div>

        <div class="admin-table-responsive">
            <table class="admin-table owner-lapangan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lapangan</th>
                        <th>Jenis</th>
                        <th>Harga/Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lapangan)): ?>
                        <tr>
                            <td colspan="6" class="owner-lapangan-empty-cell">Belum ada data lapangan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lapangan as $index => $field): ?>
                            <?php $statusClass = strtolower($field['status']) === 'aktif' ? 'success' : 'warning'; ?>
                            <tr data-owner-field-row="<?php echo e($field['id']); ?>">
                                <td><?php echo e($index + 1); ?></td>
                                <td data-owner-field-name><?php echo e($field['name']); ?></td>
                                <td><?php echo e($field['type']); ?></td>
                                <td data-owner-field-price><?php echo e($field['price']); ?></td>
                                <td><span class="admin-badge <?php echo e($statusClass); ?>" data-owner-field-status><?php echo e($field['status']); ?></span></td>
                                <td>
                                    <div class="owner-table-actions">
                                        <button class="btn-icon owner-table-edit" type="button" aria-label="Edit <?php echo e($field['name']); ?>" data-owner-field-manage-open data-owner-field-mode="edit" data-owner-field-id="<?php echo e($field['id']); ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn-icon owner-table-detail" type="button" aria-label="Detail <?php echo e($field['name']); ?>" data-owner-field-manage-open data-owner-field-mode="detail" data-owner-field-id="<?php echo e($field['id']); ?>">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                        <button class="btn-icon owner-table-delete" type="button" aria-label="Hapus <?php echo e($field['name']); ?>" data-owner-field-delete data-owner-field-id="<?php echo e($field['id']); ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<div class="owner-field-manage-modal" id="ownerFieldManageModal" data-owner-field-manage-modal hidden>
    <div class="owner-field-manage-backdrop" data-owner-field-manage-close></div>

    <section class="owner-field-manage-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerFieldManageTitle">
        <article class="owner-field-edit-panel">
            <header class="owner-field-screen-head">
                <button type="button" data-owner-field-manage-close aria-label="Kembali ke daftar lapangan">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div>
                    <h2 id="ownerFieldManageTitle">Edit Arena</h2>
                    <p>Perbarui informasi arena Anda</p>
                </div>
                <span class="owner-field-edit-status" data-owner-manage-edit-status>Aktif</span>
            </header>

            <div class="owner-field-court-preview" data-owner-manage-visual></div>

            <form class="owner-field-edit-form" action="<?php echo e(app_url('pemilik/lapangan/update')); ?>" method="post" enctype="multipart/form-data" autocomplete="off" data-owner-field-edit-form>
                <input type="hidden" name="id_lapangan" value="">
                <input type="hidden" name="type" value="">

                <section class="owner-field-edit-section">
                    <h3>Informasi Arena</h3>

                    <label>
                        <span>Nama Arena</span>
                        <input type="text" name="name" autocomplete="off" required>
                    </label>

                    <label>
                        <span>Lokasi</span>
                        <span class="owner-field-inline-input">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" name="location" autocomplete="off" required>
                        </span>
                    </label>

                    <label>
                        <span>Harga Sewa / Jam</span>
                        <span class="owner-field-price-edit">
                            <strong>Rp</strong>
                            <input type="number" name="price" min="0" required>
                        </span>
                    </label>

                    <label>
                        <span>Deskripsi</span>
                        <textarea name="description" rows="4"></textarea>
                    </label>
                </section>

                <section class="owner-field-edit-section owner-field-photo-edit-section">
                    <h3>Foto Lapangan</h3>
                    <div class="owner-field-edit-photo-list" data-owner-edit-photos></div>
                    <div data-owner-edit-deleted-photos></div>

                    <label class="owner-field-upload-drop" for="ownerEditFieldPhoto">
                        <input id="ownerEditFieldPhoto" type="file" name="foto_lapangan[]" accept="image/png,image/jpeg" multiple data-owner-edit-photo>
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>
                            <strong>Tambah Foto</strong>
                            <small>PNG, JPG maksimal 5MB per foto. Total maksimal 5 foto</small>
                        </span>
                    </label>
                    <div class="owner-field-edit-new-photo-list" data-owner-edit-new-photos></div>
                </section>

                <section class="owner-field-edit-section">
                    <fieldset class="owner-field-facility-checks owner-field-edit-facility-checks">
                        <legend>Fasilitas</legend>

                        <div class="owner-field-facility-check-grid">
                            <?php foreach ($facilityOptions as $facility): ?>
                                <label class="owner-field-facility-check">
                                    <input type="checkbox" name="facilities[]" value="<?php echo e($facility['name']); ?>" data-owner-edit-facility>
                                    <span>
                                        <i class="fa-solid <?php echo e($facility['icon']); ?>"></i>
                                        <strong><?php echo e($facility['name']); ?></strong>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </section>

                <section class="owner-field-edit-section owner-field-status-section">
                    <h3>Status Arena</h3>
                    <label class="owner-field-status-toggle">
                        <span data-owner-manage-status-label>Aktif</span>
                        <input type="checkbox" name="active">
                        <i></i>
                    </label>
                </section>

                <div class="owner-field-edit-actions">
                    <button type="button" class="owner-field-cancel-btn" data-owner-field-manage-close>Batal</button>
                    <button type="submit" class="owner-field-save-btn">
                        <i class="fa-regular fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </article>

        <aside class="owner-field-detail-panel">
            <header class="owner-field-screen-head">
                <button type="button" data-owner-field-manage-close aria-label="Kembali ke daftar lapangan">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div>
                    <h2 data-owner-manage-detail-title>Detail Arena</h2>
                    <p>Informasi lengkap arena</p>
                </div>
                <button class="owner-field-detail-like" type="button" aria-label="Favorit lapangan">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </header>

            <div class="owner-field-court-preview" data-owner-manage-detail-visual></div>

            <div class="owner-field-detail-body">
                <div class="owner-field-detail-title-row">
                    <h3 data-owner-manage-detail-name>Arena Futsal A</h3>
                    <span data-owner-manage-detail-status>Aktif</span>
                </div>

                <p class="owner-field-detail-location">
                    <i class="fa-solid fa-location-dot"></i>
                    <span data-owner-manage-detail-location>Parepare</span>
                </p>

                <p class="owner-field-detail-rating">
                    <i class="fa-solid fa-star"></i>
                    <strong data-owner-manage-detail-rating>4.8</strong>
                    <span>(<em data-owner-manage-detail-reviews>120</em> ulasan)</span>
                </p>

                <p class="owner-field-detail-price">
                    <strong data-owner-manage-detail-price>Rp80.000</strong>
                    <span>/jam</span>
                </p>

                <section>
                    <h4>Deskripsi</h4>
                    <p data-owner-manage-detail-description>Arena futsal dengan lapangan berkualitas dan fasilitas lengkap.</p>
                </section>

                <section>
                    <h4>Fasilitas</h4>
                    <div class="owner-field-facility-list" data-owner-manage-detail-facilities></div>
                </section>

                <section>
                    <h4>Jam Operasional</h4>
                    <p class="owner-field-detail-hours">
                        <i class="fa-regular fa-clock"></i>
                        <span data-owner-manage-detail-hours>06:00 - 23:00 Setiap Hari</span>
                    </p>
                </section>

                <section>
                    <h4>Aturan Arena</h4>
                    <ul class="owner-field-rule-list" data-owner-manage-detail-rules></ul>
                </section>

                <button class="owner-field-detail-edit-btn" type="button" data-owner-field-focus-edit>
                    <i class="fa-regular fa-pen-to-square"></i>
                    <span>Edit Arena</span>
                </button>
            </div>
        </aside>
    </section>
</div>

<script>
    (function () {
        var modal = document.querySelector('[data-owner-field-manage-modal]');
        var openButtons = document.querySelectorAll('[data-owner-field-manage-open]');

        if (!modal || !openButtons.length) {
            return;
        }

        var ownerFieldData = <?php echo json_encode($fieldPayloads, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var deleteAction = <?php echo json_encode(app_url('pemilik/lapangan/hapus'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var closeButtons = modal.querySelectorAll('[data-owner-field-manage-close]');
        var deleteButtons = document.querySelectorAll('[data-owner-field-delete]');
        var editPanel = modal.querySelector('.owner-field-edit-panel');
        var detailPanel = modal.querySelector('.owner-field-detail-panel');
        var editForm = modal.querySelector('[data-owner-field-edit-form]');
        var editTitle = modal.querySelector('#ownerFieldManageTitle');
        var editStatus = modal.querySelector('[data-owner-manage-edit-status]');
        var editStatusLabel = modal.querySelector('[data-owner-manage-status-label]');
        var editStatusToggle = modal.querySelector('.owner-field-status-toggle');
        var editVisual = modal.querySelector('[data-owner-manage-visual]');
        var editFacilityInputs = editForm ? editForm.querySelectorAll('[data-owner-edit-facility]') : [];
        var editPhotoList = modal.querySelector('[data-owner-edit-photos]');
        var editDeletedPhotos = modal.querySelector('[data-owner-edit-deleted-photos]');
        var editPhotoInput = modal.querySelector('[data-owner-edit-photo]');
        var editNewPhotoList = modal.querySelector('[data-owner-edit-new-photos]');
        var detailTitle = modal.querySelector('[data-owner-manage-detail-title]');
        var detailVisual = modal.querySelector('[data-owner-manage-detail-visual]');
        var detailName = modal.querySelector('[data-owner-manage-detail-name]');
        var detailStatus = modal.querySelector('[data-owner-manage-detail-status]');
        var detailLocation = modal.querySelector('[data-owner-manage-detail-location]');
        var detailRating = modal.querySelector('[data-owner-manage-detail-rating]');
        var detailReviews = modal.querySelector('[data-owner-manage-detail-reviews]');
        var detailPrice = modal.querySelector('[data-owner-manage-detail-price]');
        var detailDescription = modal.querySelector('[data-owner-manage-detail-description]');
        var detailFacilities = modal.querySelector('[data-owner-manage-detail-facilities]');
        var detailHours = modal.querySelector('[data-owner-manage-detail-hours]');
        var detailRules = modal.querySelector('[data-owner-manage-detail-rules]');
        var focusEditButton = modal.querySelector('[data-owner-field-focus-edit]');
        var detailCloseButton = modal.querySelector('.owner-field-detail-panel [data-owner-field-manage-close]');
        var currentFieldId = null;
        var lastTrigger = null;
        var removedPhotoPaths = [];

        var facilityIcons = {
            Parkir: 'fa-square-parking',
            Toilet: 'fa-restroom',
            Musholla: 'fa-mosque',
            WiFi: 'fa-wifi',
            Kantin: 'fa-store',
            CCTV: 'fa-video',
            Loker: 'fa-lock',
            'Ruang Tunggu': 'fa-couch'
        };

        function setText(element, value) {
            if (element) {
                element.textContent = value;
            }
        }

        function priceNumber(value) {
            var cleanValue = String(value || '').replace(/[^0-9]/g, '');
            var number = parseInt(cleanValue, 10);

            return Number.isNaN(number) ? 0 : number;
        }

        function formatPrice(value) {
            return 'Rp' + priceNumber(value).toLocaleString('id-ID');
        }

        function isActiveStatus(status) {
            return String(status || '').toLowerCase() === 'aktif';
        }

        function safeVisual(visual) {
            return String(visual || 'futsal').replace(/[^a-z0-9_-]/gi, '') || 'futsal';
        }

        function normalizeFacilities(facilities) {
            if (!Array.isArray(facilities)) {
                return [];
            }

            return facilities.map(function (facility) {
                return String(facility || '').trim();
            }).filter(Boolean);
        }

        function normalizePhotos(photos) {
            if (!Array.isArray(photos)) {
                return [];
            }

            return photos.filter(function (photo) {
                return photo && photo.url && photo.path;
            });
        }

        function setStatusClass(element, status) {
            if (!element) {
                return;
            }

            element.textContent = status;
            element.classList.remove('success', 'warning', 'inactive', 'is-inactive');

            if (isActiveStatus(status)) {
                element.classList.add('success');
            } else {
                element.classList.add('warning', 'is-inactive');
            }
        }

        function setPlainStatus(element, status) {
            if (!element) {
                return;
            }

            element.textContent = status;
            element.classList.toggle('is-inactive', !isActiveStatus(status));
        }

        function setCourtVisual(element, visual, photos) {
            var normalizedPhotos = normalizePhotos(photos);

            if (!element) {
                return;
            }

            element.className = 'owner-field-court-preview ' + safeVisual(visual);
            element.innerHTML = '';
            element.onpointerdown = null;
            element.onpointerup = null;

            if (normalizedPhotos.length) {
                var image = document.createElement('img');
                var currentIndex = parseInt(element.dataset.ownerPhotoIndex || '0', 10);

                element.classList.add('has-photo');
                currentIndex = Number.isNaN(currentIndex) ? 0 : currentIndex;

                if (currentIndex < 0 || currentIndex >= normalizedPhotos.length) {
                    currentIndex = 0;
                }

                element.dataset.ownerPhotoIndex = currentIndex;
                image.className = 'owner-field-preview-photo';
                image.src = normalizedPhotos[currentIndex].url;
                image.alt = 'Foto lapangan ' + (currentIndex + 1);
                element.appendChild(image);

                if (normalizedPhotos.length > 1) {
                    var prevButton = document.createElement('button');
                    var nextButton = document.createElement('button');
                    var dots = document.createElement('div');
                    var startX = null;

                    function goToPhoto(index) {
                        element.dataset.ownerPhotoIndex = (index + normalizedPhotos.length) % normalizedPhotos.length;
                        setCourtVisual(element, visual, normalizedPhotos);
                    }

                    prevButton.type = 'button';
                    prevButton.className = 'owner-field-court-photo-nav prev';
                    prevButton.setAttribute('aria-label', 'Foto sebelumnya');
                    prevButton.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
                    prevButton.addEventListener('click', function () {
                        goToPhoto(currentIndex - 1);
                    });

                    nextButton.type = 'button';
                    nextButton.className = 'owner-field-court-photo-nav next';
                    nextButton.setAttribute('aria-label', 'Foto berikutnya');
                    nextButton.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
                    nextButton.addEventListener('click', function () {
                        goToPhoto(currentIndex + 1);
                    });

                    dots.className = 'owner-field-court-photo-dots';
                    normalizedPhotos.forEach(function (photo, index) {
                        var dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = index === currentIndex ? 'active' : '';
                        dot.setAttribute('aria-label', 'Lihat foto ' + (index + 1));
                        dot.addEventListener('click', function () {
                            goToPhoto(index);
                        });
                        dots.appendChild(dot);
                    });

                    element.onpointerdown = function (event) {
                        if (event.target.closest('button')) {
                            return;
                        }

                        startX = event.clientX;
                    };

                    element.onpointerup = function (event) {
                        if (startX === null || event.target.closest('button')) {
                            startX = null;
                            return;
                        }

                        var deltaX = event.clientX - startX;
                        startX = null;

                        if (Math.abs(deltaX) < 36) {
                            return;
                        }

                        goToPhoto(deltaX < 0 ? currentIndex + 1 : currentIndex - 1);
                    };

                    element.appendChild(prevButton);
                    element.appendChild(nextButton);
                    element.appendChild(dots);
                }
            }
        }

        function draftPhotos(baseField) {
            return normalizePhotos(baseField.photos).filter(function (photo) {
                return removedPhotoPaths.indexOf(photo.path) === -1;
            });
        }

        function addDeletedPhotoInput(path) {
            if (!editDeletedPhotos) {
                return;
            }

            var input = document.createElement('input');

            input.type = 'hidden';
            input.name = 'delete_photos[]';
            input.value = path;
            editDeletedPhotos.appendChild(input);
        }

        function renderEditPhotos(field) {
            if (!editPhotoList || !editDeletedPhotos) {
                return;
            }

            removedPhotoPaths = [];
            editPhotoList.innerHTML = '';
            editDeletedPhotos.innerHTML = '';

            var photos = normalizePhotos(field.photos);

            if (!photos.length) {
                var empty = document.createElement('p');
                empty.className = 'owner-field-edit-photo-empty';
                empty.textContent = 'Belum ada foto tersimpan.';
                editPhotoList.appendChild(empty);
                return;
            }

            photos.forEach(function (photo, index) {
                var item = document.createElement('div');
                var image = document.createElement('img');
                var button = document.createElement('button');

                item.className = 'owner-field-edit-photo-item';
                image.src = photo.url;
                image.alt = 'Foto lapangan ' + (index + 1);
                button.type = 'button';
                button.innerHTML = '<i class="fa-regular fa-trash-can"></i><span>Hapus</span>';
                button.addEventListener('click', function () {
                    if (removedPhotoPaths.indexOf(photo.path) === -1) {
                        removedPhotoPaths.push(photo.path);
                        addDeletedPhotoInput(photo.path);
                    }

                    item.remove();

                    if (!editPhotoList.querySelector('.owner-field-edit-photo-item')) {
                        var empty = document.createElement('p');
                        empty.className = 'owner-field-edit-photo-empty';
                        empty.textContent = 'Semua foto lama akan dihapus setelah disimpan.';
                        editPhotoList.appendChild(empty);
                    }

                    renderDetail(getDraftField());
                    setCourtVisual(editVisual, field.visual, getDraftField().photos);
                });

                item.appendChild(image);
                item.appendChild(button);
                editPhotoList.appendChild(item);
            });
        }

        function renderEditNewPhotos() {
            if (!editNewPhotoList || !editPhotoInput) {
                return;
            }

            var files = Array.prototype.slice.call(editPhotoInput.files || []);
            editNewPhotoList.innerHTML = '';

            if (!files.length) {
                return;
            }

            files.slice(0, 5).forEach(function (file) {
                var item = document.createElement('span');
                item.textContent = file.name;
                editNewPhotoList.appendChild(item);
            });
        }

        function iconForFacility(name) {
            return facilityIcons[name] || 'fa-circle-check';
        }

        function renderFacilities(field) {
            if (!detailFacilities) {
                return;
            }

            detailFacilities.innerHTML = '';

            var facilities = normalizeFacilities(field.facilities);

            if (!facilities.length) {
                var empty = document.createElement('p');
                empty.className = 'owner-field-facility-empty';
                empty.textContent = 'Belum ada fasilitas dipilih.';
                detailFacilities.appendChild(empty);
                return;
            }

            facilities.forEach(function (facility) {
                var item = document.createElement('span');
                var icon = document.createElement('i');
                var label = document.createElement('span');

                item.className = 'owner-field-facility-item';
                icon.className = 'fa-solid ' + iconForFacility(facility);
                label.textContent = facility;

                item.appendChild(icon);
                item.appendChild(label);
                detailFacilities.appendChild(item);
            });
        }

        function renderRules(field) {
            if (!detailRules) {
                return;
            }

            detailRules.innerHTML = '';

            (field.rules || []).forEach(function (rule) {
                var item = document.createElement('li');
                var icon = document.createElement('i');
                var text = document.createElement('span');

                icon.className = 'fa-solid fa-check';
                text.textContent = rule;

                item.appendChild(icon);
                item.appendChild(text);
                detailRules.appendChild(item);
            });
        }

        function renderDetail(field) {
            var status = field.status || 'Aktif';

            setText(detailTitle, 'Detail ' + (field.name || 'Arena'));
            setCourtVisual(detailVisual, field.visual, field.photos);
            setText(detailName, field.name || 'Arena');
            setPlainStatus(detailStatus, status);
            setText(detailLocation, field.location || '-');
            setText(detailRating, field.rating || '0');
            setText(detailReviews, field.reviews || '0');
            setText(detailPrice, field.price || formatPrice(field.priceNumber));
            setText(detailDescription, field.description || 'Belum ada deskripsi.');
            setText(detailHours, field.hours || '06:00 - 23:00 Setiap Hari');
            renderFacilities(field);
            renderRules(field);
        }

        function setEditStatus(status) {
            var active = isActiveStatus(status);

            setPlainStatus(editStatus, status);
            setText(editStatusLabel, status);

            if (editStatusToggle) {
                editStatusToggle.classList.toggle('is-inactive', !active);
            }
        }

        function fillFacilityChecks(facilities) {
            var selectedFacilities = normalizeFacilities(facilities);

            editFacilityInputs.forEach(function (input) {
                input.checked = selectedFacilities.indexOf(input.value) !== -1;
            });
        }

        function getSelectedFacilities() {
            var facilities = [];

            editFacilityInputs.forEach(function (input) {
                if (input.checked) {
                    facilities.push(input.value);
                }
            });

            return facilities;
        }

        function fillEditForm(field) {
            if (!editForm) {
                return;
            }

            editForm.elements.id_lapangan.value = field.id || '';
            editForm.elements.type.value = field.type || '';
            editForm.elements.name.value = field.name || '';
            editForm.elements.location.value = field.location || '';
            editForm.elements.price.value = priceNumber(field.priceNumber || field.price);
            editForm.elements.description.value = field.description || '';
            editForm.elements.active.checked = isActiveStatus(field.status);
            if (editPhotoInput) {
                editPhotoInput.value = '';
            }
            if (editNewPhotoList) {
                editNewPhotoList.innerHTML = '';
            }
            renderEditPhotos(field);
            fillFacilityChecks(field.facilities);
            setText(editTitle, 'Edit ' + (field.name || 'Arena'));
            setEditStatus(field.status || 'Aktif');
            setCourtVisual(editVisual, field.visual, field.photos);
        }

        function getDraftField() {
            var baseField = ownerFieldData[currentFieldId] || {};
            var draftPrice = priceNumber(editForm.elements.price.value);
            var draftStatus = editForm.elements.active.checked ? 'Aktif' : 'Nonaktif';

            return Object.assign({}, baseField, {
                name: editForm.elements.name.value.trim() || baseField.name || 'Arena',
                location: editForm.elements.location.value.trim() || baseField.location || '-',
                price: formatPrice(draftPrice),
                priceNumber: draftPrice,
                status: draftStatus,
                cardStatus: draftStatus,
                description: editForm.elements.description.value.trim() || 'Belum ada deskripsi.',
                facilities: getSelectedFacilities(),
                photos: draftPhotos(baseField)
            });
        }

        function renderPanels(field) {
            fillEditForm(field);
            renderDetail(field);
        }

        function deleteField(fieldId) {
            fieldId = String(fieldId || '');

            var field = ownerFieldData[fieldId];
            var fieldName = field && field.name ? field.name : 'lapangan ini';

            if (!field) {
                return;
            }

            if (!window.confirm('Hapus ' + fieldName + ' dari daftar lapangan?')) {
                return;
            }

            var form = document.createElement('form');
            var input = document.createElement('input');

            form.method = 'post';
            form.action = deleteAction;
            input.type = 'hidden';
            input.name = 'id_lapangan';
            input.value = fieldId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        function setManageMode(mode) {
            var isDetailMode = mode === 'detail';

            modal.classList.toggle('is-detail-mode', isDetailMode);
            modal.classList.toggle('is-edit-mode', !isDetailMode);

            if (editPanel) {
                editPanel.setAttribute('aria-hidden', isDetailMode ? 'true' : 'false');
            }

            if (detailPanel) {
                detailPanel.setAttribute('aria-hidden', isDetailMode ? 'false' : 'true');
            }
        }

        function openManage(button) {
            var fieldId = button.dataset.ownerFieldId;
            var mode = button.dataset.ownerFieldMode === 'detail' ? 'detail' : 'edit';
            var field = ownerFieldData[fieldId];

            if (!field) {
                return;
            }

            currentFieldId = fieldId;
            lastTrigger = button;
            modal.hidden = false;
            setManageMode(mode);
            document.body.classList.add('owner-field-modal-open');
            renderPanels(field);

            window.setTimeout(function () {
                if (mode === 'detail' && detailCloseButton) {
                    detailCloseButton.focus();
                    return;
                }

                editForm.elements.name.focus();
            }, 80);
        }

        function closeManage() {
            modal.hidden = true;
            document.body.classList.remove('owner-field-modal-open');

            if (lastTrigger && lastTrigger.isConnected) {
                lastTrigger.focus();
            }
        }

        openButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openManage(button);
            });
        });

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeManage);
        });

        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                deleteField(button.dataset.ownerFieldId);
            });
        });

        if (editForm) {
            editForm.addEventListener('input', function () {
                if (!currentFieldId) {
                    return;
                }

                var draftField = getDraftField();
                setEditStatus(draftField.status);
                renderDetail(draftField);
            });

            editForm.elements.active.addEventListener('change', function () {
                if (!currentFieldId) {
                    return;
                }

                var draftField = getDraftField();
                setEditStatus(draftField.status);
                renderDetail(draftField);
            });

            editFacilityInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    if (!currentFieldId) {
                        return;
                    }

                    renderDetail(getDraftField());
                });
            });

            if (editPhotoInput) {
                editPhotoInput.addEventListener('change', renderEditNewPhotos);
            }

            editForm.addEventListener('submit', function (event) {
                if (!currentFieldId) {
                    event.preventDefault();
                    return;
                }
            });
        }

        if (focusEditButton) {
            focusEditButton.addEventListener('click', function () {
                setManageMode('edit');
                window.setTimeout(function () {
                    editForm.elements.name.focus();
                }, 80);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeManage();
            }
        });
    })();
</script>

<div class="owner-field-modal" id="ownerAddFieldModal" data-owner-field-modal hidden>
    <div class="owner-field-modal-backdrop" data-owner-field-modal-close></div>

    <section class="owner-field-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="ownerAddFieldTitle">
        <header class="owner-field-modal-header">
            <div>
                <h2 id="ownerAddFieldTitle">Tambah Lapangan</h2>
                <p>Lengkapi informasi lapangan Anda</p>
            </div>
            <button class="owner-field-modal-close" type="button" data-owner-field-modal-close aria-label="Tutup tambah lapangan">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <form class="owner-field-form" action="<?php echo e(app_url('pemilik/lapangan/tambah')); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="owner-field-form-grid">
                <div class="owner-field-form-column">
                    <label class="owner-field-form-group">
                        <span>Nama Lapangan <em>*</em></span>
                        <input type="text" name="nama_lapangan" placeholder="Contoh: Arena Futsal C" autocomplete="off" required>
                    </label>

                    <label class="owner-field-form-group">
                        <span>Jenis Lapangan <em>*</em></span>
                        <span class="owner-field-select-wrap">
                            <select name="jenis_lapangan" required>
                                <option value="">Pilih jenis lapangan</option>
                                <option value="Futsal">Futsal</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Mini Soccer">Mini Soccer</option>
                                <option value="Basket">Basket</option>
                                <option value="Tenis">Tenis</option>
                            </select>
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </label>

                    <label class="owner-field-form-group">
                        <span>Lokasi <em>*</em></span>
                        <input type="text" name="lokasi" placeholder="Contoh: Parepare" autocomplete="off" required>
                    </label>

                    <label class="owner-field-form-group">
                        <span>Harga per Jam <em>*</em></span>
                        <span class="owner-field-price-input">
                            <strong>Rp</strong>
                            <input type="number" name="harga_per_jam" placeholder="Contoh: 75000" min="0" required>
                        </span>
                    </label>

                    <label class="owner-field-form-group">
                        <span>Deskripsi <small>(Opsional)</small></span>
                        <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat tentang lapangan..."></textarea>
                    </label>

                    <label class="owner-field-upload-drop" for="ownerFieldPhoto">
                        <input id="ownerFieldPhoto" type="file" name="foto_lapangan[]" accept="image/png,image/jpeg" multiple data-owner-field-photo>
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>
                            <strong>Upload Foto Lapangan</strong>
                            <small>PNG, JPG maksimal 5MB per foto. Maksimal 5 foto</small>
                        </span>
                    </label>
                </div>

                <div class="owner-field-preview-column">
                    <span class="owner-field-preview-title">Preview Foto</span>
                    <div class="owner-field-photo-preview" data-owner-field-preview>
                        <div class="owner-field-preview-frame" aria-live="polite">
                            <img src="" alt="Preview foto lapangan" data-owner-field-preview-image hidden>
                            <span class="owner-field-preview-empty" data-owner-field-preview-empty>
                                <i class="fa-regular fa-image"></i>
                                <strong>Belum ada foto</strong>
                            </span>
                        </div>
                        <button class="owner-field-preview-nav prev" type="button" data-owner-field-preview-prev aria-label="Foto sebelumnya" hidden>
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button class="owner-field-preview-nav next" type="button" data-owner-field-preview-next aria-label="Foto berikutnya" hidden>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button class="owner-field-preview-remove" type="button" data-owner-field-preview-remove aria-label="Hapus foto yang sedang tampil" hidden>
                            <i class="fa-regular fa-trash-can"></i>
                            <span>Hapus Foto</span>
                        </button>
                        <span class="owner-field-preview-count" data-owner-field-preview-count hidden>1 / 1</span>
                        <div class="owner-field-preview-dots" data-owner-field-preview-dots hidden></div>
                    </div>
                </div>
            </div>

            <fieldset class="owner-field-facility-checks">
                <legend>Fasilitas</legend>

                <div class="owner-field-facility-check-grid">
                    <?php foreach ($facilityOptions as $facility): ?>
                        <label class="owner-field-facility-check">
                            <input type="checkbox" name="fasilitas[]" value="<?php echo e($facility['name']); ?>">
                            <span>
                                <i class="fa-solid <?php echo e($facility['icon']); ?>"></i>
                                <strong><?php echo e($facility['name']); ?></strong>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="owner-field-modal-actions">
                <button class="owner-field-cancel-btn" type="button" data-owner-field-modal-close>Batal</button>
                <button class="owner-field-save-btn" type="submit">Simpan Lapangan</button>
            </div>
        </form>
    </section>
</div>

<script>
    (function () {
        var modal = document.querySelector('[data-owner-field-modal]');
        var openButton = document.querySelector('[data-owner-field-modal-open]');

        if (!modal || !openButton) {
            return;
        }

        var closeButtons = modal.querySelectorAll('[data-owner-field-modal-close]');
        var addFieldForm = modal.querySelector('.owner-field-form');
        var photoInput = modal.querySelector('[data-owner-field-photo]');
        var previewImage = modal.querySelector('[data-owner-field-preview-image]');
        var previewEmpty = modal.querySelector('[data-owner-field-preview-empty]');
        var previewPrev = modal.querySelector('[data-owner-field-preview-prev]');
        var previewNext = modal.querySelector('[data-owner-field-preview-next]');
        var previewRemove = modal.querySelector('[data-owner-field-preview-remove]');
        var previewCount = modal.querySelector('[data-owner-field-preview-count]');
        var previewDots = modal.querySelector('[data-owner-field-preview-dots]');
        var firstInput = modal.querySelector('input[name="nama_lapangan"]');
        var maxPhotos = 5;
        var activePhotoIndex = 0;
        var selectedPhotoFiles = [];
        var selectedPhotoUrls = [];

        function revokePreviewUrls() {
            selectedPhotoUrls.forEach(function (photo) {
                URL.revokeObjectURL(photo.url);
            });
        }

        function updateInputFiles(files) {
            if (typeof DataTransfer === 'undefined') {
                return;
            }

            var dataTransfer = new DataTransfer();

            files.forEach(function (file) {
                dataTransfer.items.add(file);
            });

            photoInput.files = dataTransfer.files;
        }

        function renderPreview() {
            var hasPhotos = selectedPhotoUrls.length > 0;
            var hasMultiplePhotos = selectedPhotoUrls.length > 1;

            if (!hasPhotos) {
                previewImage.hidden = true;
                previewImage.removeAttribute('src');
                previewEmpty.hidden = false;
            } else {
                previewImage.src = selectedPhotoUrls[activePhotoIndex].url;
                previewImage.alt = 'Preview foto lapangan ' + (activePhotoIndex + 1);
                previewImage.hidden = false;
                previewEmpty.hidden = true;
            }

            if (previewPrev && previewNext) {
                previewPrev.hidden = !hasMultiplePhotos;
                previewNext.hidden = !hasMultiplePhotos;
            }

            if (previewRemove) {
                previewRemove.hidden = !hasPhotos;
            }

            if (previewCount) {
                previewCount.hidden = !hasPhotos;
                previewCount.textContent = (activePhotoIndex + 1) + ' / ' + selectedPhotoUrls.length;
            }

            if (previewDots) {
                previewDots.hidden = !hasMultiplePhotos;
                previewDots.innerHTML = '';

                selectedPhotoUrls.forEach(function (photo, index) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = index === activePhotoIndex ? 'active' : '';
                    dot.setAttribute('aria-label', 'Lihat foto ' + (index + 1));
                    dot.addEventListener('click', function () {
                        activePhotoIndex = index;
                        renderPreview();
                    });
                    previewDots.appendChild(dot);
                });
            }
        }

        function movePreview(step) {
            if (selectedPhotoUrls.length < 2) {
                return;
            }

            activePhotoIndex = (activePhotoIndex + step + selectedPhotoUrls.length) % selectedPhotoUrls.length;
            renderPreview();
        }

        function removeActivePhoto() {
            if (!selectedPhotoUrls.length) {
                return;
            }

            URL.revokeObjectURL(selectedPhotoUrls[activePhotoIndex].url);
            selectedPhotoUrls.splice(activePhotoIndex, 1);
            selectedPhotoFiles.splice(activePhotoIndex, 1);

            if (activePhotoIndex >= selectedPhotoUrls.length) {
                activePhotoIndex = Math.max(0, selectedPhotoUrls.length - 1);
            }

            updateInputFiles(selectedPhotoFiles);

            if (!selectedPhotoFiles.length) {
                photoInput.value = '';
            }

            renderPreview();
        }

        function openModal() {
            modal.hidden = false;
            document.body.classList.add('owner-field-modal-open');

            if (firstInput) {
                setTimeout(function () {
                    firstInput.focus();
                }, 80);
            }
        }

        function resetModalState() {
            if (addFieldForm) {
                addFieldForm.reset();
            }

            revokePreviewUrls();
            selectedPhotoFiles = [];
            selectedPhotoUrls = [];
            activePhotoIndex = 0;

            if (photoInput) {
                photoInput.value = '';
            }

            renderPreview();
        }

        function closeModal() {
            resetModalState();
            modal.hidden = true;
            document.body.classList.remove('owner-field-modal-open');
            openButton.focus();
        }

        openButton.addEventListener('click', openModal);

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        if (previewPrev && previewNext) {
            previewPrev.addEventListener('click', function () {
                movePreview(-1);
            });

            previewNext.addEventListener('click', function () {
                movePreview(1);
            });
        }

        if (previewRemove) {
            previewRemove.addEventListener('click', function () {
                removeActivePhoto();
            });
        }

        if (photoInput && previewImage && previewEmpty) {
            photoInput.addEventListener('change', function () {
                var files = Array.prototype.slice.call(photoInput.files || []);
                var selectedFiles = files.slice(0, maxPhotos);

                if (files.length > maxPhotos) {
                    alert('Maksimal upload 5 foto lapangan.');
                    updateInputFiles(selectedFiles);
                }

                revokePreviewUrls();
                selectedPhotoFiles = selectedFiles;
                selectedPhotoUrls = [];
                activePhotoIndex = 0;

                if (!selectedFiles.length) {
                    renderPreview();
                    return;
                }

                selectedPhotoUrls = selectedFiles.map(function (file) {
                    return {
                        name: file.name,
                        url: URL.createObjectURL(file)
                    };
                });

                renderPreview();
            });
        }

        renderPreview();
    })();
</script>
