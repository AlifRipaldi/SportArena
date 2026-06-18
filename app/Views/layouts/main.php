<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Arena Sport'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(app_asset('css/style.css?v=11')); ?>">
    <link rel="stylesheet" href="<?php echo e(app_asset('css/home.css?v=8')); ?>">
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
            var navLocked = false;
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
                });

                document.body.setAttribute('data-current-nav', sectionId);
            };

            var scrollToSection = function (sectionId, updateHash) {
                var target = document.getElementById(sectionId);

                if (!target) {
                    return;
                }

                setActiveNav(sectionId);

                var targetTop = target.getBoundingClientRect().top + window.pageYOffset - getHeaderOffset();
                var scrollTop = Math.max(targetTop, 0);

                window.scrollTo({
                    top: scrollTop,
                    behavior: 'auto'
                });

                if (updateHash) {
                    var nextHash = sectionId === 'beranda' ? '' : '#' + sectionId;
                    var nextUrl = window.location.pathname + window.location.search + nextHash;

                    window.history.pushState(null, '', nextUrl);
                }
            };

            navLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    var sectionId = this.getAttribute('data-home-nav');

                    if (!document.getElementById(sectionId)) {
                        return;
                    }

                    event.preventDefault();
                    navLocked = true;
                    scrollToSection(sectionId, true);

                    window.setTimeout(function () {
                        navLocked = false;
                        setActiveNav(sectionId);
                    }, 120);
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
                var sectionId = window.location.hash.replace('#', '') || 'beranda';
                scrollToSection(sectionId, false);
            });

            if (window.location.hash) {
                var sectionId = window.location.hash.replace('#', '');
                window.setTimeout(function () {
                    scrollToSection(sectionId, false);
                    window.setTimeout(updateActiveFromScroll, 250);
                }, 120);
            } else {
                setActiveNav('beranda');
            }
        })();
    </script>
</body>
</html>
