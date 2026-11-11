document.addEventListener('DOMContentLoaded', function () {
    // Tự động xác định Base URL chuẩn (chạy đúng trên cả php -S, Laragon, XAMPP)
    const getBaseUrl = () => {
        const path = window.location.pathname;
        if (path.includes('/webpc/public')) {
            return window.location.origin + '/webpc/public';
        }
        return window.location.origin;
    };

    const baseUrl = getBaseUrl();

    // 1. Xử lý sự kiện click nút Xóa (Dùng Event Delegation để bấm vào icon hay button đều nhận)
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-item');
        if (removeBtn) {
            e.preventDefault();
            const productId = removeBtn.getAttribute('data-id');
            if (productId && confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                removeItem(productId);
            }
        }
    });

    // 2. Xử lý thay đổi số lượng
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('cart-qty-input')) {
            const input = e.target;
            const productId = input.getAttribute('data-id');
            const newQty = parseInt(input.value);
            const oldQty = parseInt(input.getAttribute('data-old-val') || 1);

            if (isNaN(newQty) || newQty <= 0) {
                if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    removeItem(productId);
                } else {
                    input.value = oldQty;
                }
                return;
            }

            // Gọi API Update
            fetch(`${baseUrl}/api/cart/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=${newQty}`
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Không thể cập nhật số lượng');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    input.setAttribute('data-old-val', newQty);
                    recalculateTotals(data.cart);
                }
            })
            .catch(error => {
                alert(error.message);
                input.value = oldQty; // Phục hồi số lượng cũ nếu bị từ chối 422
            });
        }
    });

    // Hàm thực hiện xóa sản phẩm qua Ajax
    function removeItem(productId) {
        fetch(`${baseUrl}/api/cart/remove`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}`
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Xóa sản phẩm thất bại');
            return data;
        })
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`cart-row-${productId}`);
                if (row) row.remove();

                // Nếu giỏ hàng rỗng thì reload trang để hiện giao diện trống
                if (!data.cart || Object.keys(data.cart).length === 0) {
                    location.reload();
                } else {
                    recalculateTotals(data.cart);
                }
            }
        })
        .catch(error => alert(error.message));
    }

    // Hàm tính toán và cập nhật lại tổng tiền trên giao diện
    function recalculateTotals(cartData) {
        let grandTotal = 0;
        let totalItems = 0;

        Object.values(cartData).forEach(item => {
            const itemSubtotal = item.price * item.quantity;
            grandTotal += itemSubtotal;
            totalItems += item.quantity;

            const subtotalEl = document.querySelector(`.item-subtotal-${item.id}`);
            if (subtotalEl) {
                subtotalEl.innerText = formatVND(itemSubtotal) + ' đ';
            }
        });

        const subtotalEl = document.getElementById('cart-subtotal');
        const grandTotalEl = document.getElementById('cart-grand-total');
        const itemCountEl = document.getElementById('cart-item-count');

        if (subtotalEl) subtotalEl.innerText = formatVND(grandTotal) + ' VNĐ';
        if (grandTotalEl) grandTotalEl.innerText = formatVND(grandTotal) + ' VNĐ';
        if (itemCountEl) itemCountEl.innerText = Object.keys(cartData).length;
    }

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }
});