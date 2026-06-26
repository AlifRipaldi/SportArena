<?php
$customerNotifications = isset($customerNotifications) && is_array($customerNotifications) ? $customerNotifications : array();
$customerNotificationUnreadCount = isset($customerNotificationUnreadCount) ? (int) $customerNotificationUnreadCount : 0;
$customerNotificationBadge = $customerNotificationUnreadCount > 99 ? '99+' : (string) $customerNotificationUnreadCount;
$customerNotificationToken = isset($bookingCsrfToken) ? (string) $bookingCsrfToken : '';
?>

<div
    class="customer-notification-wrap"
    data-customer-notification
    data-notification-read-url="<?php echo e(app_url('dashboard/notifikasi/baca')); ?>"
    data-notification-read-all-url="<?php echo e(app_url('dashboard/notifikasi/baca-semua')); ?>"
    data-notification-token="<?php echo e($customerNotificationToken); ?>"
>
    <button class="profile-notification" type="button" aria-label="Buka notifikasi" aria-expanded="false" aria-controls="customerNotificationPanel" data-customer-notification-toggle>
        <span>&#128276;</span>
        <sup data-customer-notification-badge <?php echo $customerNotificationUnreadCount > 0 ? '' : 'hidden'; ?>><?php echo e($customerNotificationBadge); ?></sup>
    </button>

    <section class="customer-notification-panel" id="customerNotificationPanel" aria-label="Daftar notifikasi" data-customer-notification-panel hidden>
        <header class="customer-notification-head">
            <div>
                <strong>Notifikasi</strong>
                <small><span data-customer-notification-unread><?php echo e($customerNotificationUnreadCount); ?></span> belum dibaca</small>
                <button class="customer-notification-read-all" type="button" data-customer-notification-read-all <?php echo $customerNotificationUnreadCount > 0 ? '' : 'disabled'; ?>>Tandai semua dibaca</button>
            </div>
            <button type="button" aria-label="Tutup notifikasi" data-customer-notification-close>&times;</button>
        </header>

        <div class="customer-notification-list">
            <?php if (empty($customerNotifications)): ?>
                <div class="customer-notification-empty">
                    <span>&#128276;</span>
                    <p>Belum ada notifikasi.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($customerNotifications as $notification): ?>
                <?php
                    $notificationId = isset($notification['ID_Notifikasi']) ? (int) $notification['ID_Notifikasi'] : 0;
                    if ($notificationId <= 0) {
                        continue;
                    }

                    $type = isset($notification['Tipe']) ? strtolower((string) $notification['Tipe']) : 'info';
                    $icon = '&#9432;';
                    if (strpos($type, 'booking') !== false) {
                        $icon = '&#128197;';
                    } elseif (strpos($type, 'pay') !== false || strpos($type, 'bayar') !== false) {
                        $icon = '&#128179;';
                    } elseif (strpos($type, 'promo') !== false) {
                        $icon = '&#127873;';
                    }

                    $createdAt = isset($notification['created_at']) ? strtotime((string) $notification['created_at']) : false;
                    $notificationTime = $createdAt ? date('d/m/Y H:i', $createdAt) . ' WITA' : '';
                ?>
                <article class="customer-notification-item <?php echo empty($notification['Dibaca_pada']) ? 'unread' : ''; ?>" data-customer-notification-item data-notification-id="<?php echo e($notificationId); ?>">
                    <button class="customer-notification-link" type="button" data-customer-notification-open>
                        <span class="customer-notification-icon"><?php echo $icon; ?></span>
                        <span class="customer-notification-copy">
                            <strong><?php echo e(isset($notification['Judul']) ? $notification['Judul'] : 'Notifikasi'); ?></strong>
                            <small><?php echo e(isset($notification['Pesan']) ? $notification['Pesan'] : ''); ?></small>
                            <?php if ($notificationTime !== ''): ?>
                                <time><?php echo e($notificationTime); ?></time>
                            <?php endif; ?>
                        </span>
                    </button>
                    <form class="customer-notification-delete-form" method="post" action="<?php echo e(app_url('dashboard/notifikasi/hapus')); ?>">
                        <input type="hidden" name="booking_token" value="<?php echo e($customerNotificationToken); ?>">
                        <input type="hidden" name="id_notifikasi" value="<?php echo e($notificationId); ?>">
                        <button type="submit" aria-label="Hapus notifikasi">&times;</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script>
    (function () {
        if (window.__arenaCustomerNotificationReady) {
            return;
        }

        window.__arenaCustomerNotificationReady = true;

        function closeAllNotifications() {
            document.querySelectorAll('[data-customer-notification]').forEach(function (root) {
                var panel = root.querySelector('[data-customer-notification-panel]');
                var toggle = root.querySelector('[data-customer-notification-toggle]');

                if (panel) {
                    panel.hidden = true;
                }

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        document.querySelectorAll('[data-customer-notification]').forEach(function (root) {
            var toggle = root.querySelector('[data-customer-notification-toggle]');
            var panel = root.querySelector('[data-customer-notification-panel]');
            var close = root.querySelector('[data-customer-notification-close]');
            var readUrl = root.getAttribute('data-notification-read-url');
            var readAllUrl = root.getAttribute('data-notification-read-all-url');
            var token = root.getAttribute('data-notification-token') || '';
            var badge = root.querySelector('[data-customer-notification-badge]');
            var unreadText = root.querySelector('[data-customer-notification-unread]');
            var readAllButton = root.querySelector('[data-customer-notification-read-all]');

            if (!toggle || !panel) {
                return;
            }

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var shouldOpen = panel.hidden;
                closeAllNotifications();
                panel.hidden = !shouldOpen;
                toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            });

            panel.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            if (close) {
                close.addEventListener('click', closeAllNotifications);
            }

            if (readAllButton) {
                readAllButton.addEventListener('click', function () {
                    if (!readAllUrl || !token || readAllButton.disabled) {
                        return;
                    }

                    readAllButton.disabled = true;

                    var request = new FormData();
                    request.append('booking_token', token);

                    fetch(readAllUrl, {
                        method: 'POST',
                        body: request,
                        credentials: 'same-origin'
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Notification read all request failed');
                        }

                        root.querySelectorAll('[data-customer-notification-item].unread').forEach(function (item) {
                            item.classList.remove('unread');
                        });

                        if (unreadText) {
                            unreadText.textContent = '0';
                        }

                        if (badge) {
                            badge.textContent = '0';
                            badge.hidden = true;
                        }
                    }).catch(function () {
                        readAllButton.disabled = false;
                    });
                });
            }

            root.querySelectorAll('[data-customer-notification-open]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var item = button.closest('[data-customer-notification-item]');

                    if (!item) {
                        return;
                    }

                    item.classList.toggle('expanded');

                    if (!item.classList.contains('unread') || !readUrl || !token) {
                        return;
                    }

                    var request = new FormData();
                    request.append('id_notifikasi', item.getAttribute('data-notification-id'));
                    request.append('booking_token', token);

                    fetch(readUrl, {
                        method: 'POST',
                        body: request,
                        credentials: 'same-origin'
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Notification read request failed');
                        }

                        item.classList.remove('unread');

                        var unreadCount = unreadText ? Math.max(0, parseInt(unreadText.textContent || '0', 10) - 1) : 0;
                        if (unreadText) {
                            unreadText.textContent = String(unreadCount);
                        }

                        if (badge) {
                            badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                            badge.hidden = unreadCount <= 0;
                        }

                        if (readAllButton && unreadCount <= 0) {
                            readAllButton.disabled = true;
                        }
                    }).catch(function () {
                        item.classList.remove('expanded');
                    });
                });
            });
        });

        document.addEventListener('click', closeAllNotifications);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllNotifications();
            }
        });
    })();
</script>
