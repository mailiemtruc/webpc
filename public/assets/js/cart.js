document.addEventListener('DOMContentLoaded', function () {
    const bodyEl = document.body;
    const baseUrl = (bodyEl && bodyEl.getAttribute('data-app-base'))
        || (window.location.pathname.includes('/webpc/public')
            ? window.location.origin + '/webpc/public'
            : window.location.origin);

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') || '' : '';
    }

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(Number(amount) || 0));
    }

    function showCouponFeedback(message, type) {
        const box = document.getElementById('coupon-feedback');
        if (!box) {
            if (message) alert(message);
            return;
        }
        if (!message) {
            box.style.display = 'none';
            box.textContent = '';
            return;
        }
        const palette = {
            success: { color: '#15803d', bg: '#f0fdf4', border: '#bbf7d0' },
            error: { color: '#b91c1c', bg: '#fef2f2', border: '#fecaca' },
            info: { color: '#0369a1', bg: '#f0f9ff', border: '#bae6fd' },
        }[type] || { color: '#334155', bg: '#f8fafc', border: '#e2e8f0' };

        box.style.display = 'block';
        box.style.padding = '8px 10px';
        box.style.borderRadius = '6px';
        box.style.color = palette.color;
        box.style.background = palette.bg;
        box.style.border = `1px solid ${palette.border}`;
        box.textContent = message;
    }

    async function postApi(endpoint, params = {}) {
        const csrfToken = getCsrfToken();
        const body = new URLSearchParams({ ...params, _csrf: csrfToken }).toString();

        const response = await fetch(`${baseUrl}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body,
        });

        const data = await response.json().catch(() => null);
        if (!response.ok || !data || !data.success) {
            throw new Error((data && data.message) ? data.message : 'Không thể xử lý yêu cầu.');
        }
        return data;
    }

    function applyCartResponseToUI(data) {
        const cartData = data.cart || {};
        const items = Object.values(cartData);

        if (items.length === 0) {
            location.reload();
            return;
        }

        items.forEach(item => {
            const subtotalEl = document.querySelector(`.item-subtotal-${item.id}`);
            if (subtotalEl) {
                subtotalEl.innerText = formatVND(Number(item.price) * Number(item.quantity)) + ' đ';
            }
        });

        const subtotalEl = document.getElementById('cart-subtotal');
        const grandTotalEl = document.getElementById('cart-grand-total');
        const itemCountEl = document.getElementById('cart-item-count');
        const bagCountEl = document.querySelector('.bag-count');

        if (subtotalEl) subtotalEl.innerText = formatVND(data.subtotal) + ' VNĐ';
        if (grandTotalEl) grandTotalEl.innerText = formatVND(data.finalTotal) + ' VNĐ';
        if (itemCountEl) itemCountEl.innerText = String(data.lineCount ?? items.length);
        if (bagCountEl && typeof data.cartCount === 'number') {
            bagCountEl.innerText = String(data.cartCount);
        }

        const discountRow = document.getElementById('coupon-discount-row');
        const appliedCodeEl = document.getElementById('applied-coupon-code');
        const discountAmountEl = document.getElementById('cart-discount-amount');
        const codeInput = document.getElementById('coupon-code-input');

        if (data.coupon && data.coupon.code) {
            if (appliedCodeEl) appliedCodeEl.innerText = data.coupon.code;
            if (discountAmountEl) discountAmountEl.innerText = '-' + formatVND(data.discountAmount) + ' VNĐ';
            if (discountRow) discountRow.style.display = 'flex';
        } else {
            if (discountRow) discountRow.style.display = 'none';
            if (data.couponNotice) {
                if (codeInput) codeInput.value = '';
                showCouponFeedback(data.couponNotice, 'info');
            }
        }
    }

    async function removeItem(productId) {
        try {
            const data = await postApi('/api/cart/remove', { product_id: productId });
            const row = document.getElementById(`cart-row-${productId}`);
            if (row) row.remove();
            applyCartResponseToUI(data);
        } catch (err) {
            alert(err.message);
        }
    }

    // 1. Xử lý nút xóa sản phẩm khỏi giỏ
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-item');
        if (!removeBtn) return;

        e.preventDefault();
        const productId = removeBtn.getAttribute('data-id');
        if (productId && confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            removeItem(productId);
        }
    });

    // 2. Xử lý thay đổi số lượng sản phẩm
    document.addEventListener('change', async function (e) {
        if (!e.target.classList.contains('cart-qty-input')) return;

        const input = e.target;
        const productId = input.getAttribute('data-id');
        const newQty = parseInt(input.value, 10);
        const oldQty = parseInt(input.getAttribute('data-old-val') || '1', 10);

        if (isNaN(newQty) || newQty <= 0) {
            if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                removeItem(productId);
            } else {
                input.value = oldQty;
            }
            return;
        }

        try {
            const data = await postApi('/api/cart/update', {
                product_id: productId,
                quantity: newQty,
            });
            input.setAttribute('data-old-val', String(newQty));
            applyCartResponseToUI(data);
        } catch (err) {
            alert(err.message);
            input.value = oldQty;
        }
    });

    // 3. Áp dụng mã giảm giá
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    const codeInput = document.getElementById('coupon-code-input');

    async function handleApplyCoupon() {
        const code = codeInput ? codeInput.value.trim() : '';
        if (!code) {
            showCouponFeedback('Vui lòng nhập mã giảm giá!', 'error');
            return;
        }

        try {
            const data = await postApi('/api/coupon/apply', { code });
            const appliedCodeEl = document.getElementById('applied-coupon-code');
            const discountAmountEl = document.getElementById('cart-discount-amount');
            const discountRow = document.getElementById('coupon-discount-row');
            const subtotalEl = document.getElementById('cart-subtotal');
            const grandTotalEl = document.getElementById('cart-grand-total');

            if (appliedCodeEl) appliedCodeEl.innerText = data.couponCode;
            if (discountAmountEl) discountAmountEl.innerText = '-' + formatVND(data.discountAmount) + ' VNĐ';
            if (discountRow) discountRow.style.display = 'flex';
            if (subtotalEl && typeof data.subtotal === 'number') {
                subtotalEl.innerText = formatVND(data.subtotal) + ' VNĐ';
            }
            if (grandTotalEl) grandTotalEl.innerText = formatVND(data.finalTotal) + ' VNĐ';

            showCouponFeedback(data.message, 'success');
        } catch (err) {
            showCouponFeedback(err.message, 'error');
        }
    }

    if (btnApplyCoupon) {
        btnApplyCoupon.addEventListener('click', handleApplyCoupon);
    }
    if (codeInput) {
        codeInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleApplyCoupon();
            }
        });
    }

    // 4. Hủy mã giảm giá
    const btnRemoveCoupon = document.getElementById('btn-remove-coupon');
    if (btnRemoveCoupon) {
        btnRemoveCoupon.addEventListener('click', async function () {
            try {
                const data = await postApi('/api/coupon/remove');
                if (codeInput) codeInput.value = '';
                const discountRow = document.getElementById('coupon-discount-row');
                const grandTotalEl = document.getElementById('cart-grand-total');
                if (discountRow) discountRow.style.display = 'none';
                if (grandTotalEl) grandTotalEl.innerText = formatVND(data.finalTotal) + ' VNĐ';
                showCouponFeedback(data.message, 'info');
            } catch (err) {
                showCouponFeedback(err.message, 'error');
            }
        });
    }
});
