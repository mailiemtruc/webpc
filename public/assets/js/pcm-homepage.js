/**
 * PCMarket Homepage Interactive Scripts
 * Xử lý Slider tự động, chuyển Tab Hot Sale, và Thêm vào giỏ hàng nhanh
 */

document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
    initHotSaleTabs();
    initQuickCart();
    initMegaMenu();
});

/**
 * 1. Khởi tạo Slider Hero Banner
 */
function initHeroSlider() {
    const slider = document.getElementById('pcmHeroSlider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.pcm-slide');
    const dots = slider.querySelectorAll('.pcm-dot');
    const prevBtn = slider.querySelector('.pcm-slider-prev');
    const nextBtn = slider.querySelector('.pcm-slider-next');

    if (!slides.length) return;

    let currentIndex = 0;
    let timer = null;
    const intervalTime = 5000;

    function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentIndex = index;

        slides.forEach((s, i) => {
            s.classList.toggle('active', i === currentIndex);
        });

        dots.forEach((d, i) => {
            d.classList.toggle('active', i === currentIndex);
        });
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function prevSlide() {
        showSlide(currentIndex - 1);
    }

    function startAutoSlide() {
        stopAutoSlide();
        timer = setInterval(nextSlide, intervalTime);
    }

    function stopAutoSlide() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoSlide();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAutoSlide();
        });
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const idx = parseInt(dot.getAttribute('data-index') || '0', 10);
            showSlide(idx);
            startAutoSlide();
        });
    });

    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);

    startAutoSlide();
}

/**
 * 2. Chuyển đổi Tab trong phần HOT SALE MỖI NGÀY
 */
function initHotSaleTabs() {
    const tabBtns = document.querySelectorAll('.pcm-tab-btn');
    if (!tabBtns.length) return;

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetSelector = btn.getAttribute('data-tab-target');
            if (!targetSelector) return;

            // Xóa active cũ
            tabBtns.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });

            document.querySelectorAll('.pcm-tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Active tab mới
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            const targetContent = document.querySelector(targetSelector);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

/**
 * 3. Thêm vào giỏ hàng nhanh (AJAX Quick Add to Cart)
 */
function initQuickCart() {
    const cartButtons = document.querySelectorAll('[data-pcm-add-cart]');
    if (!cartButtons.length) return;

    const baseEl = document.body;
    const appBase = baseEl ? baseEl.getAttribute('data-app-base') : '';

    cartButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const productId = btn.getAttribute('data-pcm-add-cart');
            const productName = btn.getAttribute('data-name') || 'Sản phẩm';

            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;

            try {
                const res = await fetch(`${appBase}/api/cart/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${encodeURIComponent(productId)}&quantity=1`
                });

                // Cập nhật số lượng giỏ hàng trên Header nếu có badge
                const bagCount = document.querySelector('.bag-count');
                if (bagCount) {
                    const current = parseInt(bagCount.textContent || '0', 10);
                    bagCount.textContent = (current + 1).toString();
                }

                btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                btn.style.backgroundColor = '#16a34a';

                showToast(`Đã thêm vào giỏ: ${productName}`);

                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.style.backgroundColor = '';
                    btn.disabled = false;
                }, 1500);

            } catch (err) {
                console.warn('Cart API fallback:', err);
                // Fallback nếu route API của Dev 3 chưa bật: điều hướng đến chi tiết sản phẩm
                const parentCard = btn.closest('.pcm-card');
                const link = parentCard ? parentCard.querySelector('.pcm-card-thumb') : null;
                if (link && link.href) {
                    window.location.href = link.href;
                } else {
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }
            }
        });
    });
}

/**
 * Toast thông báo nhanh góc màn hình
 */
function showToast(message) {
    let container = document.getElementById('pcmToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'pcmToastContainer';
        container.style.position = 'fixed';
        container.style.bottom = '24px';
        container.style.right = '24px';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '8px';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.style.background = '#1e293b';
    toast.style.color = '#ffffff';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
    toast.style.fontSize = '13.5px';
    toast.style.fontWeight = '600';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '10px';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(12px)';
    toast.style.transition = 'all 0.3s ease';

    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #22c55e; font-size: 16px;"></i> <span>${escapeHtml(message)}</span>`;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(12px)';
        setTimeout(() => toast.remove(), 300);
    }, 2800);
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}



/**
 * 4. Khởi tạo Mega Menu Đa Cấp
 */
function initMegaMenu() {
    const container = document.querySelector('.pcm-mega-menu-container');
    if (!container) return;

    const trigger = container.querySelector('.pcm-mega-menu-trigger');
    const catItems = container.querySelectorAll('.pcm-mega-cat-item');
    const subPanels = container.querySelectorAll('.pcm-mega-subpanel');

    // Chuyển đổi panel khi rê chuột qua danh mục lớn
    catItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            catItems.forEach(i => i.classList.remove('is-active'));
            item.classList.add('is-active');

            const targetSelector = item.getAttribute('data-target');
            if (targetSelector) {
                subPanels.forEach(p => p.classList.remove('is-active'));
                const activePanel = container.querySelector(targetSelector);
                if (activePanel) {
                    activePanel.classList.add('is-active');
                }
            }
        });
    });

    // Toggle menu khi click trên mobile/tablet
    if (trigger) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            container.classList.toggle('is-open');
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                container.classList.remove('is-open');
            }
        });
    }
}
