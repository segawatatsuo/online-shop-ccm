document.addEventListener('DOMContentLoaded', () => {
    /************** 共通処理 **************/
    
    // ハンバーガーメニュー
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }

    // スクロールアニメーション
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const fadeElements = document.querySelectorAll('.fade-in');
    if (fadeElements.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        fadeElements.forEach(el => observer.observe(el));
    }

    // カートボタンのクリック効果
    document.querySelectorAll('.add-to-cart').forEach(button => {
        if (!button) return;
        button.addEventListener('click', function () {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => { this.style.transform = 'translateY(-2px)'; }, 100);
        });
    });

    /************** detail-page **************/
    const imageModal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');

    function openModal(element) {
        if (!imageModal || !modalImg) return;
        const img = element.querySelector('img');
        modalImg.src = (img && img.src) ? img.src : 'https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
        modalImg.alt = (img && img.alt) ? img.alt : element.textContent;
        imageModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!imageModal) return;
        imageModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    if (imageModal) {
        window.addEventListener('click', (event) => { if (event.target === imageModal) closeModal(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });
    }

    /************** cart.html **************/
    function updateTotal() {
        const subtotalCells = document.querySelectorAll('.subtotal');
        if (!subtotalCells.length) return;
        let total = 0;
        subtotalCells.forEach(cell => {
            const amount = parseInt(cell.textContent.replace(/[\\,]/g, '')) || 0;
            total += amount;
        });
        const totalAmount = document.getElementById('totalAmount');
        if (totalAmount) totalAmount.textContent = `¥${total.toLocaleString()}`;
    }

    function updateQuantity(button) {
        if (!button) return;
        const row = button.closest('tr');
        const quantityInput = row?.querySelector('.quantity-input');
        const subtotalCell = row?.querySelector('.subtotal');
        if (!quantityInput || !subtotalCell) return;

        const price = parseInt(quantityInput.dataset.price) || 0;
        let quantity = parseInt(quantityInput.value) || 1;
        if (quantity < 1) quantity = 1;
        quantityInput.value = quantity;

        subtotalCell.textContent = `¥${(price * quantity).toLocaleString()}`;
        updateTotal();
    }

    function deleteItem(button) {
        const row = button.closest('tr');
        if (!row) return;
        row.style.opacity = '0';
        row.style.transform = 'translateX(100px)';
        setTimeout(() => {
            row.remove();
            updateTotal();
        }, 300);
    }

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
            const button = this.parentElement?.querySelector('.update-btn');
            updateQuantity(button);
        });
    });

    window.addEventListener('load', updateTotal);

    // お届け先セクション
    const sameAsOrdererCheckbox = document.getElementById('same_as_orderer');
    const deliverySection = document.getElementById('delivery_section');
    if (sameAsOrdererCheckbox && deliverySection) {
        sameAsOrdererCheckbox.addEventListener('change', () => {
            deliverySection.style.display = sameAsOrdererCheckbox.checked ? 'none' : 'block';
        });
        if (sameAsOrdererCheckbox.checked) deliverySection.style.display = 'none';
    }

    /************** 全体安全エラー監視 **************/
    window.addEventListener('error', function(event) {
        // Chrome/Edge 拡張のリソースエラーは無視
        if (!event.filename.startsWith('chrome-extension://')) {
            console.warn('JSエラー:', event.message, 'at', event.filename, ':', event.lineno);
        }
    });
});
