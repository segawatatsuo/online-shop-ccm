
        // ハンバーガーメニュー
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

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

        // ヘッダーのスクロール効果
        /*
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });
        */

        // カートボタンのクリック効果
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-2px)';
                }, 100);
            });
        });



        /****************************** deail-page ********************************** */


        
        // ハンバーガーメニュー
        /*
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // ヘッダーのスクロール効果
        /*
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });
        */

        // モーダル機能
        function openModal(element) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const img = element.querySelector('img');
            
            if (img && img.src) {
                modalImg.src = img.src;
                modalImg.alt = img.alt;
            } else {
                // プレースホルダー用の画像URL
                modalImg.src = 'https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                modalImg.alt = element.textContent;
            }
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // モーダル外クリックで閉じる
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('imageModal');
            if (event.target === modal) {
                closeModal();
            }
        });

        // ESCキーでモーダルを閉じる
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // カートボタンのクリック効果
        document.querySelector('.add-to-cart').addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'translateY(-3px)';
            }, 100);
        });



/********************cart.html*********************************** */



        // ハンバーガーメニュー
        /*
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        */
        // ヘッダーのスクロール効果
        /*
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.08)'; 
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });
        */


        // 数量更新機能
        function updateQuantity(button) {
            const row = button.closest('tr');
            const quantityInput = row.querySelector('.quantity-input');
            const subtotalCell = row.querySelector('.subtotal');
            const price = parseInt(quantityInput.dataset.price);
            let quantity = parseInt(quantityInput.value);

            if (quantity < 1) { // 数量が1未満にならないように修正
                quantity = 1;
                quantityInput.value = 1;
            }

            const subtotal = price * quantity;
            subtotalCell.textContent = `\\${subtotal.toLocaleString()}`;
            updateTotal();

            // アニメーション効果を削除
        }

        // 商品削除機能
        function deleteItem(button) {
            const row = button.closest('tr');
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
            let total = 0;

            subtotalCells.forEach(cell => {
                const amount = parseInt(cell.textContent.replace(/[\\,]/g, ''));
                total += amount;
            });

            document.getElementById('totalAmount').textContent = `\\${total.toLocaleString()}`;
        }

        // 買い物を続けるボタン
        function continueShopping() {
            alert('商品一覧ページに戻ります');
        }

        // 購入手続きボタン
        function proceedToCheckout() {
            alert('購入手続きページに進みます');
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



/**************kakunin************** */


        // ハンバーガーメニュー
        /*
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        */
        // ヘッダーのスクロール効果
        /*
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.08)'; 
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });
        */

        // 数量更新機能
        function updateQuantity(button) {
            const row = button.closest('tr');
            const quantityInput = row.querySelector('.quantity-input');
            const subtotalCell = row.querySelector('.subtotal');
            // ここに数量と小計を更新するロジックを追加
            console.log(`数量を ${quantityInput.value} に更新`);
            // 例: subtotalCell.textContent = (quantityInput.value * pricePerItem).toFixed(2);
        }

        // 削除機能
        function deleteItem(button) {
            const row = button.closest('tr');
            row.remove();
            // ここに合計金額を再計算するロジックを追加
            console.log('商品を削除しました');
        }

        // お届け先情報表示/非表示
        const sameAsOrdererCheckbox = document.getElementById('same_as_orderer');
        const deliverySection = document.getElementById('delivery_section');

        sameAsOrdererCheckbox.addEventListener('change', () => {
            if (sameAsOrdererCheckbox.checked) {
                deliverySection.style.display = 'none';
            } else {
                deliverySection.style.display = 'block';
            }
        });

        // 初期ロード時にお届け先セクションの表示を調整
        if (sameAsOrdererCheckbox.checked) {
            deliverySection.style.display = 'none';
        }
