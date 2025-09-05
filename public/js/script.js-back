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
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => {
    observer.observe(el);
});

// カートボタンのクリック効果
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'translateY(-2px)';
        }, 100);
    });
});

// モーダル機能
function openModal(element) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const img = element.querySelector('img');
    
    if (!modal || !modalImg) return;

    if (img && img.src) {
        modalImg.src = img.src;
        modalImg.alt = img.alt;
    } else {
        modalImg.src = 'https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
        modalImg.alt = element.textContent;
    }
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// モーダル外クリックで閉じる
window.addEventListener('click', (event) => {
    const modal = document.getElementById('imageModal');
    if (modal && event.target === modal) {
        closeModal();
    }
});

// ESCキーでモーダルを閉じる
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// ================= cart.html ==================

// 数量更新機能
function updateQuantity(button) {
    if (!button) return;
    const row = button.closest('tr');
    if (!row) return;
    const quantityInput = row.querySelector('.quantity-input');
    const subtotalCell = row.querySelector('.subtotal');
    if (!quantityInput || !subtotalCell) return;

    const price = parseInt(quantityInput.dataset.price);
    let quantity = parseInt(quantityInput.value);

    if (quantity < 1) {
        quantity = 1;
        quantityInput.value = 1;
    }

    const subtotal = price * quantity;
    subtotalCell.textContent = `¥${subtotal.toLocaleString()}`;
    updateTotal();
}

// 商品削除機能
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

// 合計金額更新
function updateTotal() {
    const subtotalCells = document.querySelectorAll('.subtotal');
    if (!subtotalCells.length) return;

    let total = 0;
    subtotalCells.forEach(cell => {
        const amount = parseInt(cell.textContent.replace(/[¥,]/g, '')) || 0;
        total += amount;
    });

    const totalAmount = document.getElementById('totalAmount');
    if (totalAmount) {
        totalAmount.textContent = `¥${total.toLocaleString()}`;
    }
}

// 数量入力フィールドの変更イベント
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function () {
        const button = this.parentElement.querySelector('.update-btn');
        updateQuantity(button);
    });
});

// 初期表示時の合計金額を計算
window.addEventListener('load', updateTotal);

// ================= kakunin ==================

// お届け先情報表示/非表示
const sameAsOrdererCheckbox = document.getElementById('same_as_orderer');
const deliverySection = document.getElementById('delivery_section');

if (sameAsOrdererCheckbox && deliverySection) {
    sameAsOrdererCheckbox.addEventListener('change', () => {
        deliverySection.style.display = sameAsOrdererCheckbox.checked ? 'none' : 'block';
    });

    // 初期ロード時にお届け先セクションの表示を調整
    deliverySection.style.display = sameAsOrdererCheckbox.checked ? 'none' : 'block';
}
