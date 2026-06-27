<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Arena Sport'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(app_asset_versioned('css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(app_asset_versioned('css/home.css')); ?>">
</head>
<body class="site-home">
    <header>
        <div class="container nav-container">
            <a href="<?php echo e(app_url('/')); ?>" class="logo">
                <img src="<?php echo e(app_asset('img/logo.png')); ?>" alt="Arena Sport Logo">
            </a>
            <nav>
                <ul>
                    <li><a class="is-active" href="<?php echo e(app_url('/#beranda')); ?>" data-home-nav="beranda">Beranda</a></li>
                    <li><a href="<?php echo e(app_url('/#lapangan')); ?>" data-home-nav="lapangan">Lapangan</a></li>
                    <li><a href="<?php echo e(app_url('/#cara-kerja')); ?>" data-home-nav="cara-kerja">Cara Kerja</a></li>
                    <li><a href="<?php echo e(app_url('/#tentang-kami')); ?>" data-home-nav="tentang-kami">Tentang Kami</a></li>
                    <li><a href="<?php echo e(app_url('/#kontak')); ?>" data-home-nav="kontak">Kontak</a></li>
                </ul>
            </nav>
            <div class="nav-actions">
                <div class="location-select">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Kota Parepare</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <a href="<?php echo e(app_url('public/login.php')); ?>" class="btn-login">
                    <i class="fa-regular fa-user"></i>
                    <span>Login / Daftar</span>
                </a>
            </div>
        </div>
    </header>

    <?php echo $content; ?>

    <footer>
        <div class="container">
            <p>&copy; 2026 Arena Sport Management System.</p>
        </div>
    </footer>
    <script>
        (function () {
            if (!document.body.classList.contains('site-home')) {
                return;
            }

            var navLinks = Array.prototype.slice.call(document.querySelectorAll('header nav a[data-home-nav]'));
            var homeUrl = <?php echo json_encode(app_url('/'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            var navLocked = false;
            var navUnlockTimer = null;
            var navClickToken = 0;
            var sections = navLinks
                .map(function (link) {
                    return document.getElementById(link.getAttribute('data-home-nav'));
                })
                .filter(Boolean);

            var getHeaderOffset = function () {
                var header = document.querySelector('header');

                return header ? Math.ceil(header.getBoundingClientRect().height) + 16 : 0;
            };

            var setActiveNav = function (sectionId) {
                navLinks.forEach(function (link) {
                    var isCurrent = link.getAttribute('data-home-nav') === sectionId;

                    link.classList.remove('active');
                    link.classList.toggle('is-active', isCurrent);

                    if (isCurrent) {
                        link.setAttribute('aria-current', 'page');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });

                document.body.setAttribute('data-current-nav', sectionId);
            };

            var sectionUrl = function (sectionId) {
                return homeUrl + (sectionId === 'beranda' ? '' : '#' + encodeURIComponent(sectionId));
            };

            var getHashSection = function () {
                var hash = window.location.hash.replace(/^#/, '');

                try {
                    hash = decodeURIComponent(hash);
                } catch (error) {
                    hash = window.location.hash.replace(/^#/, '');
                }

                return hash || 'beranda';
            };

            var prefersReducedMotion = function () {
                return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            };

            var scrollToSection = function (sectionId, updateHash, behavior) {
                var target = document.getElementById(sectionId);

                if (!target) {
                    return false;
                }

                setActiveNav(sectionId);

                var targetTop = target.getBoundingClientRect().top + window.pageYOffset - getHeaderOffset();
                var maxScroll = Math.max(document.documentElement.scrollHeight - window.innerHeight, 0);
                var scrollTop = Math.min(Math.max(targetTop, 0), maxScroll);
                var scrollBehavior = prefersReducedMotion() ? 'auto' : (behavior || 'auto');

                try {
                    window.scrollTo({
                        top: scrollTop,
                        behavior: scrollBehavior
                    });
                } catch (error) {
                    window.scrollTo(0, scrollTop);
                }

                if (updateHash) {
                    var nextUrl = sectionUrl(sectionId);

                    if (window.history && window.history.pushState) {
                        window.history.pushState(null, '', nextUrl);
                    } else if (sectionId !== 'beranda') {
                        window.location.hash = sectionId;
                    }
                }

                return scrollTop;
            };

            navLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    var sectionId = this.getAttribute('data-home-nav');

                    if (!document.getElementById(sectionId)) {
                        return;
                    }

                    event.preventDefault();
                    navClickToken += 1;
                    var currentToken = navClickToken;

                    if (navUnlockTimer) {
                        window.clearTimeout(navUnlockTimer);
                    }

                    navLocked = true;

                    var targetScrollTop = scrollToSection(sectionId, true, 'smooth');

                    if (targetScrollTop === false) {
                        navLocked = false;
                        navUnlockTimer = null;
                        return;
                    }

                    var startedAt = Date.now();
                    var finishWhenSettled = function () {
                        if (currentToken !== navClickToken) {
                            return;
                        }

                        var isAtTarget = Math.abs(window.pageYOffset - targetScrollTop) <= 2;
                        var timedOut = Date.now() - startedAt > 1200;

                        if (isAtTarget || timedOut || prefersReducedMotion()) {
                            navLocked = false;
                            setActiveNav(sectionId);
                            navUnlockTimer = null;
                            return;
                        }

                        navUnlockTimer = window.setTimeout(finishWhenSettled, 50);
                    };

                    navUnlockTimer = window.setTimeout(finishWhenSettled, 80);
                });
            });

            var updateActiveFromScroll = function () {
                if (navLocked) {
                    return;
                }

                var headerOffset = getHeaderOffset() + 8;
                var currentSection = 'beranda';
                var maxScroll = Math.max(document.documentElement.scrollHeight - window.innerHeight, 0);

                sections.forEach(function (section) {
                    var sectionTop = section.getBoundingClientRect().top + window.pageYOffset;

                    if (window.pageYOffset >= sectionTop - headerOffset) {
                        currentSection = section.id;
                    }
                });

                if (window.pageYOffset >= maxScroll - 2 && sections.length) {
                    currentSection = sections[sections.length - 1].id;
                }

                setActiveNav(currentSection);
            };

            window.addEventListener('scroll', updateActiveFromScroll, { passive: true });
            window.addEventListener('hashchange', function () {
                scrollToSection(getHashSection(), false, 'smooth');
            });

            if (window.location.hash) {
                var sectionId = getHashSection();
                window.setTimeout(function () {
                    scrollToSection(sectionId, false, 'auto');
                    window.setTimeout(updateActiveFromScroll, 250);
                }, 120);
            } else {
                setActiveNav('beranda');
            }
        })();
    </script>
</body>
</html>
