/* Shared progressive enhancement for the storefront and management workspace. */
(function () {
    'use strict';
    var search = document.getElementById('store-search');
    var menu = document.getElementById('mobile-menu');
    var searchTrigger = document.querySelector('[data-search-toggle]');
    var menuTrigger = document.querySelector('[data-menu-toggle]');
    function setPanel(panel, trigger, open) {
        if (!panel || !trigger) return;
        panel.hidden = !open;
        trigger.setAttribute('aria-expanded', String(open));
    }
    if (searchTrigger) searchTrigger.addEventListener('click', function () {
        var open = search.hidden;
        setPanel(menu, menuTrigger, false);
        setPanel(search, searchTrigger, open);
        if (open) search.querySelector('input').focus();
    });
    if (menuTrigger) menuTrigger.addEventListener('click', function () {
        var open = menu.hidden;
        setPanel(search, searchTrigger, false);
        setPanel(menu, menuTrigger, open);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        if (search && !search.hidden) { setPanel(search, searchTrigger, false); searchTrigger.focus(); }
        if (menu && !menu.hidden) { setPanel(menu, menuTrigger, false); menuTrigger.focus(); }
    });
    document.querySelectorAll('[data-search-form]').forEach(function (form) {
        var input = form.querySelector('input[name="q"]');
        var clear = form.querySelector('[data-search-clear]');
        function update() { clear.hidden = !input.value.length; }
        clear.addEventListener('click', function () { input.value = ''; update(); input.focus(); });
        input.addEventListener('input', update);
        update();
    });
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        var input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;
        button.addEventListener('click', function () {
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.setAttribute('aria-pressed', String(!visible));
            button.setAttribute('aria-label', visible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
            button.querySelector('i').className = visible ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
            input.focus();
        });
    });
    document.querySelectorAll('[data-validate-form]').forEach(function (form) {
        var submitted = false;
        var fields = Array.from(form.querySelectorAll('input:not([type="hidden"])'));
        var draftKey = 'pcmarket-form:' + window.location.pathname;
        var nonSecret = fields.filter(function (input) { return ['email', 'full_name', 'phone'].includes(input.name); });
        try {
            var draft = JSON.parse(sessionStorage.getItem(draftKey) || '{}');
            if (document.querySelector('.flash-error')) nonSecret.forEach(function (input) { input.value = draft[input.name] || ''; });
            sessionStorage.removeItem(draftKey);
        } catch (error) { /* Forms remain usable when browser storage is unavailable. */ }
        function validate(input) {
            var message = '';
            if (input.required && !input.value.trim()) message = 'Vui lòng điền thông tin này.';
            else if (input.type === 'email' && input.validity.typeMismatch) message = 'Vui lòng nhập địa chỉ email hợp lệ.';
            else if (input.name === 'password' && input.minLength > 0 && input.value.length < input.minLength) message = 'Mật khẩu cần ít nhất ' + input.minLength + ' ký tự.';
            else if (input.name === 'password_confirmation' && input.value !== form.querySelector('[name="password"]').value) message = 'Mật khẩu xác nhận chưa trùng khớp.';
            var error = document.getElementById(input.id + '-error');
            input.setAttribute('aria-invalid', String(Boolean(message)));
            if (error) {
                error.textContent = message;
                error.hidden = !message;
                if (message) input.setAttribute('aria-describedby', error.id);
                else input.removeAttribute('aria-describedby');
            }
            return !message;
        }
        form.addEventListener('submit', function (event) {
            submitted = true;
            var validity = fields.map(validate);
            var firstInvalid = validity.indexOf(false);
            if (firstInvalid !== -1) { event.preventDefault(); fields[firstInvalid].focus(); return; }
            try {
                var draft = {};
                nonSecret.forEach(function (input) { draft[input.name] = input.value; });
                sessionStorage.setItem(draftKey, JSON.stringify(draft));
            } catch (error) { /* Server submission does not depend on storage. */ }
            var submit = form.querySelector('[type="submit"]');
            submit.disabled = true;
            submit.setAttribute('aria-busy', 'true');
            submit.textContent = 'Đang xử lý…';
        });
        fields.forEach(function (input) { input.addEventListener('input', function () {
            if (!submitted) return;
            validate(input);
            if (input.name === 'password') {
                var confirmation = form.querySelector('[name="password_confirmation"]');
                if (confirmation && confirmation.value) validate(confirmation);
            }
        }); });
    });
    window.addEventListener('pageshow', function (event) { if (event.persisted) window.location.reload(); });
    document.querySelectorAll('[data-dismiss]').forEach(function (button) {
        button.addEventListener('click', function () { button.closest('.flash').remove(); });
    });
    var dialog = document.getElementById('feature-dialog');
    if (dialog) {
        document.querySelectorAll('[data-feature]').forEach(function (button) {
            button.addEventListener('click', function () {
                var builder = button.dataset.feature === 'builder';
                document.getElementById('feature-title').textContent = builder ? 'Cấu hình riêng đang trên đường đến.' : 'Đặt hàng trực tuyến sắp có mặt.';
                document.getElementById('feature-message').textContent = builder ? 'Công cụ chọn linh kiện đang được hoàn thiện. Trong lúc chờ, hãy khám phá các linh kiện và thông số tại cửa hàng.' : 'Giỏ hàng và thanh toán đang được hoàn thiện. Bạn vẫn có thể xem sản phẩm, so sánh giá và tìm hiểu cấu hình.';
                dialog.showModal();
            });
        });
        dialog.querySelector('[data-dialog-close]').addEventListener('click', function () { dialog.close(); });
        dialog.addEventListener('click', function (event) { if (event.target === dialog) { var box = dialog.getBoundingClientRect(); if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) dialog.close(); } });
    }
    document.querySelectorAll('[data-product-image]').forEach(function (image) {
        function fallback() {
            if (image.dataset.fallback) return;
            image.dataset.fallback = 'true';
            image.src = new URL('../images/product-placeholder.svg', document.querySelector('script[src*="ui.js"]').src).href;
        }
        image.addEventListener('error', fallback);
        if (image.complete && image.naturalWidth === 0) fallback();
    });
    var sidebar = document.getElementById('admin-sidebar');
    var adminTrigger = document.querySelector('[data-admin-menu]');
    var backdrop = document.querySelector('[data-admin-backdrop]');
    function closeSidebar() {
        if (!sidebar || !adminTrigger) return;
        sidebar.classList.remove('is-open');
        adminTrigger.setAttribute('aria-expanded', 'false');
        if (backdrop) backdrop.hidden = true;
        if (window.innerWidth < 992) sidebar.inert = true;
    }
    if (sidebar && adminTrigger) {
        function adaptSidebar() {
            sidebar.inert = window.innerWidth < 992 && !sidebar.classList.contains('is-open');
            if (window.innerWidth >= 992 && backdrop) backdrop.hidden = true;
        }
        adaptSidebar();
        window.addEventListener('resize', adaptSidebar);
        adminTrigger.addEventListener('click', function () {
            var open = !sidebar.classList.contains('is-open');
            sidebar.classList.toggle('is-open', open);
            sidebar.inert = !open;
            adminTrigger.setAttribute('aria-expanded', String(open));
            backdrop.hidden = !open;
            if (open) sidebar.querySelector('a').focus();
        });
        if (backdrop) backdrop.addEventListener('click', function () { closeSidebar(); adminTrigger.focus(); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && sidebar.classList.contains('is-open')) { closeSidebar(); adminTrigger.focus(); }
            if (event.key === 'Tab' && window.innerWidth < 992 && sidebar.classList.contains('is-open')) {
                var focusable = sidebar.querySelectorAll('a, button:not(:disabled)');
                var first = focusable[0], last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
    }
}());
