(function () {
    'use strict';

    /* Sidebar toggle (mobile) */
    var sidebar = document.querySelector('.admin-sidebar');
    var toggle = document.querySelector('.admin-sidebar-toggle');
    var backdrop = document.querySelector('.admin-sidebar-backdrop');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            backdrop?.classList.toggle('show');
        });

        backdrop?.addEventListener('click', function () {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }

    /* Confirm deletes */
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var message = el.dataset.confirm || 'Are you sure you want to delete this item?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var message = form.dataset.confirm || 'Are you sure?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    /* Image drag reorder with SortableJS */
    var imageList = document.getElementById('admin-image-reorder-list');

    if (imageList && typeof Sortable !== 'undefined') {
        Sortable.create(imageList, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function () {
                var form = document.getElementById('admin-image-reorder-form');
                if (!form) return;

                var container = form.querySelector('[data-order-inputs]');
                if (!container) return;

                container.innerHTML = '';

                imageList.querySelectorAll('[data-image-id]').forEach(function (item) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order[]';
                    input.value = item.dataset.imageId;
                    container.appendChild(input);
                });

                form.submit();
            }
        });
    }
})();
