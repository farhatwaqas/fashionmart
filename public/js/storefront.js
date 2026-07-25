(function () {
    'use strict';

    /* Lazy load fade-in */
    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', function () {
                img.classList.add('loaded');
            });
        }
    });

    /* Product gallery */
    var galleryMain = document.querySelector('.fc-gallery-main');
    var galleryThumbs = document.querySelectorAll('.fc-gallery-thumb');

    if (galleryMain && galleryThumbs.length) {
        var mainImg = galleryMain.querySelector('img');

        galleryThumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                var src = thumb.dataset.src || thumb.querySelector('img')?.src;
                var alt = thumb.dataset.alt || thumb.querySelector('img')?.alt || '';

                if (src && mainImg) {
                    mainImg.src = src;
                    mainImg.alt = alt;
                }

                galleryThumbs.forEach(function (t) { t.classList.remove('active'); });
                thumb.classList.add('active');
                galleryMain.classList.remove('is-zoomed');
            });
        });

        /* Image zoom on hover / click */
        galleryMain.addEventListener('click', function () {
            galleryMain.classList.toggle('is-zoomed');
        });

        galleryMain.addEventListener('mousemove', function (e) {
            if (!galleryMain.classList.contains('is-zoomed')) return;

            var rect = galleryMain.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;

            galleryMain.style.setProperty('--zoom-x', x + '%');
            galleryMain.style.setProperty('--zoom-y', y + '%');
        });
    }

    /* Quantity stepper */
    document.querySelectorAll('[data-qty-stepper]').forEach(function (stepper) {
        var input = stepper.querySelector('[data-qty-input]');
        var btnMinus = stepper.querySelector('[data-qty-minus]');
        var btnPlus = stepper.querySelector('[data-qty-plus]');
        var min = parseInt(stepper.dataset.min || '1', 10);
        var max = parseInt(stepper.dataset.max || '99', 10);

        if (!input) return;

        function clamp(val) {
            return Math.max(min, Math.min(max, val));
        }

        if (btnMinus) {
            btnMinus.addEventListener('click', function () {
                input.value = clamp(parseInt(input.value, 10) - 1 || min);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        if (btnPlus) {
            btnPlus.addEventListener('click', function () {
                input.value = clamp(parseInt(input.value, 10) + 1 || min);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        input.addEventListener('change', function () {
            input.value = clamp(parseInt(input.value, 10) || min);
        });
    });
})();
