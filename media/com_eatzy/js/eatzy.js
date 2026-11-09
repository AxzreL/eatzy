/**
 * EATZY - Ultra-Modern Cyber-Bistro Application Engine
 * Dynamic Inline Steppers • Promo Quick-Chips • GCash/Maya Webhook Clearance
 */

const EatzyApp = (function() {
    let cart = [];
    let appliedPromo = null;
    let selectedPayment = 'gcash';
    let currentOrder = null;
    let currentMode = 'delivery';

    function init() {
        try {
            const saved = localStorage.getItem('eatzy_cart');
            if (saved) {
                cart = JSON.parse(saved);
            }
        } catch (e) {}
        renderCart();
        updateCardSteppers();
        restoreDinerProfile();
        initTracker();
    }

    // Customer profile memory: auto-restore and auto-save as user inputs
    function restoreDinerProfile() {
        try {
            const profile = JSON.parse(localStorage.getItem('eatzy_diner_profile') || '{}');
            const nameEl = document.getElementById('diner-name');
            const phoneEl = document.getElementById('diner-phone');
            const addrEl = document.getElementById('diner-address');
            const notesEl = document.getElementById('diner-notes');

            if (nameEl && !nameEl.value && profile.name) nameEl.value = profile.name;
            if (phoneEl && !phoneEl.value && profile.phone) phoneEl.value = profile.phone;
            if (addrEl && !addrEl.value && profile.address) addrEl.value = profile.address;
            if (notesEl && !notesEl.value && profile.notes) notesEl.value = profile.notes;

            [nameEl, phoneEl, addrEl, notesEl].forEach(el => {
                if (el) {
                    el.addEventListener('input', saveDinerProfile);
                }
            });
        } catch (e) {}
    }

    function saveDinerProfile() {
        try {
            const profile = {
                name: document.getElementById('diner-name')?.value.trim() || '',
                phone: document.getElementById('diner-phone')?.value.trim() || '',
                address: document.getElementById('diner-address')?.value.trim() || '',
                notes: document.getElementById('diner-notes')?.value.trim() || ''
            };
            localStorage.setItem('eatzy_diner_profile', JSON.stringify(profile));
        } catch (e) {}
    }

    function saveCart() {
        try {
            localStorage.setItem('eatzy_cart', JSON.stringify(cart));
        } catch (e) {}
        renderCart();
        updateCardSteppers();
    }

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id, name, price: parseFloat(price), quantity: 1 });
        }
        saveCart();
        showSmsToast(`Added 1x ${name} to your express cart.`);
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        item.quantity += delta;
        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        saveCart();
    }

    function toggleCart(open) {
        const drawer = document.getElementById('eatzy-cart-drawer');
        const overlay = document.getElementById('eatzy-cart-overlay');
        if (open) {
            drawer.classList.add('open');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.remove('open');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Dynamic Card Steppers: morphs "+ Add to Cart" into "[- QTY +]"
    function updateCardSteppers() {
        document.querySelectorAll('.eatzy-card').forEach(card => {
            const id = parseInt(card.getAttribute('data-id'), 10);
            const actionWrap = document.getElementById(`eatzy-card-action-${id}`);
            if (!actionWrap) return;

            const cartItem = cart.find(i => i.id === id);
            const name = card.getAttribute('data-name');
            const price = parseFloat(card.querySelector('.eatzy-dish-price')?.innerText?.replace(/[^0-9.]/g, '') || 0);

            if (cartItem && cartItem.quantity > 0) {
                actionWrap.innerHTML = `
                    <div class="eatzy-inline-stepper">
                        <button type="button" onclick="EatzyApp.updateQty(${id}, -1)"><i class="fi fi-br-minus" style="font-size:10px;"></i></button>
                        <span>${cartItem.quantity} in Bag</span>
                        <button type="button" onclick="EatzyApp.updateQty(${id}, 1)"><i class="fi fi-br-plus" style="font-size:10px;"></i></button>
                    </div>
                `;
            } else {
                actionWrap.innerHTML = `
                    <button class="eatzy-add-btn" onclick="EatzyApp.addToCart(${id}, '${escapeHtml(name)}', ${price})">
                        <i class="fi fi-br-plus" style="font-size:12px; margin-right:4px;"></i> <span>Add to Cart &bull; ₱${price.toFixed(2)}</span>
                    </button>
                `;
            }
        });
    }

    function renderCart() {
        const cartItemsWrap = document.getElementById('eatzy-cart-items');
        const floatBar = document.getElementById('eatzy-floating-cart-bar');
        const floatQty = document.getElementById('eatzy-float-qty');
        const floatTotal = document.getElementById('eatzy-float-total');
        const drawerCount = document.getElementById('eatzy-drawer-count');
        const navBadge = document.getElementById('eatzy-nav-cart-badge');
        const topMobileBadge = document.getElementById('eatzy-mobile-top-cart-badge');
        const bottomBadge = document.getElementById('eatzy-bottom-cart-badge');
        const subtotalEl = document.getElementById('eatzy-summary-subtotal');
        const discountRow = document.getElementById('eatzy-discount-row');
        const discountEl = document.getElementById('eatzy-summary-discount');
        const deliveryEl = document.getElementById('eatzy-summary-delivery');
        const totalEl = document.getElementById('eatzy-summary-total');
        const tierText = document.getElementById('eatzy-delivery-tier-text');
        const tierBar = document.getElementById('eatzy-progress-bar');

        const totalQty = cart.reduce((acc, item) => acc + item.quantity, 0);
        const subtotal = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);

        if (drawerCount) drawerCount.innerText = `${totalQty} items`;
        if (navBadge) navBadge.innerText = totalQty;
        if (topMobileBadge) topMobileBadge.innerText = totalQty;
        if (bottomBadge) bottomBadge.innerText = totalQty;
        if (floatQty) floatQty.innerText = `${totalQty} ${totalQty === 1 ? 'item' : 'items'}`;

        if (floatBar) {
            floatBar.style.display = (totalQty > 0) ? 'flex' : 'none';
        }

        let deliveryFee = (subtotal >= 1000 || subtotal === 0 || currentMode === 'pickup') ? 0 : 65;
        if (appliedPromo === 'FREEDEL') deliveryFee = 0;

        if (tierText && tierBar) {
            if (currentMode === 'pickup') {
                tierText.innerHTML = '🥡 Direct Takeout selected &bull; <strong>₱0 Delivery Fee</strong>';
                tierBar.style.width = '100%';
            } else if (subtotal >= 1000) {
                tierText.innerHTML = '🎉 You unlocked <strong>FREE Direct Delivery</strong>!';
                tierBar.style.width = '100%';
            } else {
                const diff = (1000 - subtotal).toFixed(2);
                const percent = Math.min(100, Math.round((subtotal / 1000) * 100));
                tierText.innerHTML = `Add <strong>₱${diff}</strong> more for FREE delivery!`;
                tierBar.style.width = `${percent}%`;
            }
        }

        let discount = 0;
        if (appliedPromo === 'EATZYFIRST') {
            discount = subtotal * 0.15;
        } else if (appliedPromo === 'CRAVE50') {
            discount = Math.min(50, subtotal);
        }

        const grandTotal = Math.max(0, (subtotal - discount) + deliveryFee);

        if (subtotalEl) subtotalEl.innerText = `₱${subtotal.toFixed(2)}`;
        if (deliveryEl) deliveryEl.innerText = (deliveryFee === 0) ? 'FREE' : `₱${deliveryFee.toFixed(2)}`;
        if (totalEl) totalEl.innerText = `₱${grandTotal.toFixed(2)}`;
        if (floatTotal) floatTotal.innerText = `₱${grandTotal.toFixed(2)}`;

        if (discountRow && discountEl) {
            if (discount > 0) {
                discountRow.style.display = 'flex';
                discountEl.innerText = `-₱${discount.toFixed(2)}`;
            } else {
                discountRow.style.display = 'none';
            }
        }

        if (cartItemsWrap) {
            if (cart.length === 0) {
                cartItemsWrap.innerHTML = `
                    <div class="eatzy-empty-cart">
                        <i class="fi fi-br-shopping-bag" style="font-size:36px; display:block; margin:0 auto 10px auto; opacity:0.35;"></i>
                        <p style="font-size:16px; font-weight:700; color:#fff; margin-bottom:4px;">Your bag is empty.</p>
                        <span>Select any delicious dish from the menu to start.</span>
                    </div>
                `;
            } else {
                cartItemsWrap.innerHTML = cart.map(item => `
                    <div class="eatzy-cart-item-row">
                        <div>
                            <div style="font-weight:800; font-size:14px; color:#fff;">${escapeHtml(item.name)}</div>
                            <div style="font-size:12px; color:var(--eatzy-primary); font-weight:700;">₱${(item.price * item.quantity).toFixed(2)}</div>
                        </div>
                        <div class="eatzy-item-controls" style="display:flex; align-items:center; gap:8px;">
                            <button class="eatzy-qty-btn" style="background:#202330; border:none; color:#fff; width:26px; height:26px; border-radius:6px; font-weight:bold; cursor:pointer;" onclick="EatzyApp.updateQty(${item.id}, -1)"><i class="fi fi-br-minus" style="font-size:10px;"></i></button>
                            <span style="font-weight:800; font-size:13px; min-width:18px; text-align:center;">${item.quantity}</span>
                            <button class="eatzy-qty-btn" style="background:#202330; border:none; color:#fff; width:26px; height:26px; border-radius:6px; font-weight:bold; cursor:pointer;" onclick="EatzyApp.updateQty(${item.id}, 1)"><i class="fi fi-br-plus" style="font-size:10px;"></i></button>
                        </div>
                    </div>
                `).join('');
            }
        }
    }

    function quickApplyPromo(code) {
        const input = document.getElementById('eatzy-promo-code');
        if (input) input.value = code;
        applyPromo();
    }

    function applyPromo() {
        const codeInput = document.getElementById('eatzy-promo-code');
        const msgEl = document.getElementById('eatzy-promo-msg');
        if (!codeInput) return;

        const code = codeInput.value.trim().toUpperCase();
        if (code === 'EATZYFIRST') {
            appliedPromo = code;
            msgEl.style.color = 'var(--eatzy-mint)';
            msgEl.innerText = '✓ Promo EATZYFIRST applied! 15% discount deducted.';
        } else if (code === 'FREEDEL') {
            appliedPromo = code;
            msgEl.style.color = 'var(--eatzy-mint)';
            msgEl.innerText = '✓ Promo FREEDEL applied! Delivery fee waived.';
        } else if (code === 'CRAVE50') {
            appliedPromo = code;
            msgEl.style.color = 'var(--eatzy-mint)';
            msgEl.innerText = '✓ Promo CRAVE50 applied! ₱50.00 discount.';
        } else {
            msgEl.style.color = 'var(--eatzy-primary)';
            msgEl.innerText = 'Invalid promo code. Tap a chip above to test.';
            appliedPromo = null;
        }
        renderCart();
    }

    function selectPayment(radio) {
        selectedPayment = radio.value;
        document.querySelectorAll('.eatzy-payment-option').forEach(el => el.classList.remove('active'));
        radio.closest('.eatzy-payment-option').classList.add('active');
    }

    function setMode(mode, btn) {
        currentMode = mode;
        document.querySelectorAll('.eatzy-mode-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        renderCart();
    }

    function saveOrderToHistory(orderData) {
        try {
            const myOrders = JSON.parse(localStorage.getItem('eatzy_my_orders') || '[]');
            const count = (orderData.items || cart).reduce((acc, it) => acc + (it.quantity || 1), 0);
            myOrders.unshift({
                order_number: orderData.order_number,
                total: parseFloat(orderData.total || 0),
                items_count: count,
                date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }),
                payment_method: orderData.payment_method || selectedPayment
            });
            localStorage.setItem('eatzy_my_orders', JSON.stringify(myOrders.slice(0, 10)));
            localStorage.setItem('eatzy_last_order', orderData.order_number);
        } catch (e) {}
    }

    function initiateCheckout() {
        if (cart.length === 0) {
            alert('Your bag is empty! Add dishes from the menu first.');
            return;
        }

        const name = document.getElementById('diner-name')?.value.trim();
        const phone = document.getElementById('diner-phone')?.value.trim();
        const address = document.getElementById('diner-address')?.value.trim();
        const notes = document.getElementById('diner-notes')?.value.trim() || '';

        if (!name) {
            alert('Please enter your full name for the order.');
            document.getElementById('diner-name')?.focus();
            return;
        }

        const cleanPhone = phone ? phone.replace(/[^0-9]/g, '') : '';
        if (cleanPhone.length < 10) {
            alert('Please enter a valid Philippine mobile number (e.g. 09171234567) so we can send SMS updates.');
            document.getElementById('diner-phone')?.focus();
            return;
        }

        if (!address) {
            alert('Please enter your complete delivery address (Unit/Street/Barangay/City).');
            document.getElementById('diner-address')?.focus();
            return;
        }

        saveDinerProfile();

        const checkoutBtn = document.getElementById('eatzy-checkout-btn');
        checkoutBtn.innerText = 'Connecting to Gateway...';
        checkoutBtn.disabled = true;

        const payload = {
            customer_name: name,
            customer_phone: phone,
            delivery_address: address,
            delivery_notes: notes,
            payment_method: selectedPayment,
            items: cart,
            promo_code: appliedPromo
        };

        fetch('index.php?option=com_eatzy&task=placeOrder', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            checkoutBtn.innerText = 'Proceed to Pay →';
            checkoutBtn.disabled = false;

            if (!data.success) {
                alert('Order error: ' + (data.message || 'Please try again.'));
                return;
            }

            currentOrder = data;

            if (selectedPayment === 'cod') {
                saveOrderToHistory(data);
                localStorage.removeItem('eatzy_cart');
                cart = [];
                renderCart();
                updateCardSteppers();
                toggleCart(false);
                window.location.href = getTrackUrl(data.order_number);
                return;
            }

            openPaymentModal(data);
        })
        .catch(err => {
            checkoutBtn.innerText = 'Proceed to Pay →';
            checkoutBtn.disabled = false;

            // Hybrid Vercel client-side clearance fallback
            const subtotal = cart.reduce((acc, it) => acc + (it.price * it.quantity), 0);
            let discount = 0;
            if (appliedPromo === 'EATZYFIRST') discount = subtotal * 0.15;
            else if (appliedPromo === 'CRAVE50') discount = Math.min(50, subtotal);
            const deliveryFee = (subtotal >= 1000 || currentMode === 'pickup' || appliedPromo === 'FREEDEL') ? 0 : 65;
            const grandTotal = Math.max(0, (subtotal - discount) + deliveryFee);
            const orderNumber = 'EATZ-' + Math.random().toString(36).substring(2, 8).toUpperCase();
            
            const localOrder = {
                success: true,
                order_number: orderNumber,
                total: grandTotal,
                subtotal: subtotal,
                discount: discount,
                delivery_fee: deliveryFee,
                payment_method: selectedPayment,
                transaction_ref: selectedPayment.toUpperCase() + '-TXN-' + Math.floor(100000 + Math.random() * 900000),
                customer_name: name,
                customer_phone: phone,
                delivery_address: address,
                delivery_notes: notes,
                items: JSON.parse(JSON.stringify(cart)),
                order_status: 'Kitchen Preparing'
            };

            currentOrder = localOrder;

            if (selectedPayment === 'cod') {
                saveOrderToHistory(localOrder);
                localStorage.removeItem('eatzy_cart');
                cart = [];
                renderCart();
                updateCardSteppers();
                toggleCart(false);
                window.location.href = getTrackUrl(orderNumber);
                return;
            }

            openPaymentModal(localOrder);
        });
    }

    function getTrackUrl(orderNumber) {
        if (window.location.pathname.includes('.html') || !window.location.search.includes('option=')) {
            return `track.html?id=${encodeURIComponent(orderNumber)}`;
        }
        return `index.php?option=com_eatzy&view=track&id=${encodeURIComponent(orderNumber)}`;
    }

    function openPaymentModal(orderData) {
        const modal = document.getElementById('eatzy-payment-modal');
        const logo = document.getElementById('eatzy-gateway-logo');
        const header = document.getElementById('eatzy-gateway-header');
        const amountEl = document.getElementById('eatzy-modal-amount');
        const orderNoEl = document.getElementById('eatzy-modal-orderno');
        const mpinInput = document.getElementById('eatzy-mpin-field');

        if (orderData.payment_method === 'maya') {
            logo.innerText = 'Maya';
            header.style.background = 'linear-gradient(135deg, #008f5d 0%, #00d084 100%)';
        } else {
            logo.innerText = 'GCash';
            header.style.background = 'linear-gradient(135deg, #0052cc 0%, #007dfa 100%)';
        }

        amountEl.innerText = `₱${parseFloat(orderData.total).toFixed(2)}`;
        orderNoEl.innerText = `Order: ${orderData.order_number}`;
        modal.style.display = 'flex';

        if (mpinInput) {
            mpinInput.value = '';
            setTimeout(() => mpinInput.focus(), 150);
        }
    }

    function closePaymentModal() {
        document.getElementById('eatzy-payment-modal').style.display = 'none';
    }

    function approvePayment() {
        if (!currentOrder) return;

        const mpinInput = document.getElementById('eatzy-mpin-field');
        const mpin = mpinInput ? mpinInput.value.trim() : '';
        if (mpin.length < 4) {
            alert('Please enter your 4 or 6-digit MPIN to authorize payment.');
            if (mpinInput) mpinInput.focus();
            return;
        }

        const approveBtn = document.getElementById('eatzy-btn-approve');
        approveBtn.innerText = 'Verifying Webhook Clearance...';
        approveBtn.disabled = true;

        function completePaymentClearance() {
            saveOrderToHistory(currentOrder);
            closePaymentModal();
            localStorage.removeItem('eatzy_cart');
            cart = [];
            renderCart();
            updateCardSteppers();
            toggleCart(false);

            showSmsToast(`[GLOBE SMS] EATZY: Payment of ₱${parseFloat(currentOrder.total).toFixed(2)} cleared! Ref: ${currentOrder.transaction_ref}. Kitchen is firing your order.`);

            setTimeout(() => {
                window.location.href = getTrackUrl(currentOrder.order_number);
            }, 1200);
        }

        fetch('index.php?option=com_eatzy&task=webhook', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_number: currentOrder.order_number,
                transaction_ref: currentOrder.transaction_ref,
                status: 'SUCCESS'
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(res => {
            completePaymentClearance();
        })
        .catch(err => {
            // Client-side fallback for static Vercel clearance
            completePaymentClearance();
        });
    }

    function showSmsToast(msg) {
        const toast = document.getElementById('eatzy-sms-toast');
        const body = document.getElementById('eatzy-sms-body');
        if (!toast || !body) return;
        body.innerText = msg;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 4500);
    }

    let activeCategory = 'all';
    let activeTag = null;

    function setCategory(cat, btn) {
        activeCategory = cat;
        activeTag = null;
        document.querySelectorAll('.eatzy-cat-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        filterMenu();
    }

    function setTag(tag, btn) {
        activeTag = tag;
        activeCategory = 'all';
        document.querySelectorAll('.eatzy-cat-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        filterMenu();
    }

    function filterMenu() {
        const searchVal = (document.getElementById('eatzy-search-input')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.eatzy-card');

        cards.forEach(card => {
            const cat = card.getAttribute('data-category');
            const name = card.getAttribute('data-name');
            const tags = card.getAttribute('data-tags');
            const special = card.getAttribute('data-special');

            let matchesCat = (activeCategory === 'all' || cat === activeCategory);
            let matchesTag = true;
            if (activeTag === 'Vegetarian') matchesTag = tags.includes('Vegetarian');
            if (activeTag === 'Special') matchesTag = (special === '1');

            let matchesSearch = (!searchVal || name.includes(searchVal) || cat.toLowerCase().includes(searchVal));

            if (matchesCat && matchesTag && matchesSearch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function claimPoints(btn) {
        if (!btn) return;
        btn.innerHTML = '<span><i class="fi fi-br-check" style="margin-right:4px;"></i> 38 Points Saved!</span>';
        btn.style.background = 'rgba(255, 255, 255, 0.08)';
        btn.style.borderColor = 'rgba(255, 255, 255, 0.2)';
        btn.style.color = '#fff';
        btn.disabled = true;
        showSmsToast('38 EATZY Loyalty Points added to your dining profile! Redeemable on next checkout.');
    }

    let trackerPollInterval = null;

    function initTracker() {
        // 1. Populate recent orders card if on the empty lookup page
        const recentOrdersList = document.getElementById('eatzy-recent-orders-list');
        const recentOrdersCard = document.getElementById('eatzy-recent-orders-card');
        if (recentOrdersList && recentOrdersCard) {
            try {
                const myOrders = JSON.parse(localStorage.getItem('eatzy_my_orders') || '[]');
                if (myOrders.length > 0) {
                    recentOrdersCard.style.display = 'block';
                    recentOrdersList.innerHTML = myOrders.map(ord => `
                        <a href="${getTrackUrl(ord.order_number)}" class="eatzy-recent-order-item">
                            <div>
                                <strong style="color:var(--eatzy-primary); font-family:monospace; font-size:14px;">${escapeHtml(ord.order_number)}</strong>
                                <div style="font-size:11px; color:#888ea0; margin-top:2px;">${escapeHtml(ord.date || '')} &bull; ${ord.items_count || 1} item(s)</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:800; color:#fff; font-size:13px;">₱${parseFloat(ord.total).toFixed(2)}</div>
                                <span style="font-size:10px; color:var(--eatzy-mint); text-transform:uppercase;">${escapeHtml(ord.payment_method || 'GCash')}</span>
                            </div>
                        </a>
                    `).join('');
                } else {
                    recentOrdersCard.style.display = 'none';
                }
            } catch (e) {}
        }

        // 2. Active order tracking live polling
        const trackerWrap = document.querySelector('.eatzy-tracker-wrapper[data-order-number]');
        if (trackerWrap) {
            const orderNumber = trackerWrap.getAttribute('data-order-number');
            const currentStatus = trackerWrap.getAttribute('data-order-status');
            highlightActiveSimBtn(currentStatus);

            if (orderNumber) {
                if (trackerPollInterval) clearInterval(trackerPollInterval);
                trackerPollInterval = setInterval(() => {
                    pollOrderStatus(orderNumber);
                }, 4000);
            }
        }

        // 3. Countdown timer
        const el = document.getElementById('eatzy-eta-countdown');
        if (!el) return;
        let mins = 21;
        let secs = 45;
        setInterval(() => {
            if (secs <= 0) {
                if (mins > 0) {
                    mins--;
                    secs = 59;
                }
            } else {
                secs--;
            }
            const sStr = secs < 10 ? '0' + secs : secs;
            el.innerText = `${mins} Mins ${sStr}s Remaining`;
        }, 1000);
    }

    function pollOrderStatus(orderNumber) {
        if (!orderNumber) return;
        fetch(`index.php?option=com_eatzy&task=getOrderStatus&order_number=${encodeURIComponent(orderNumber)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.order && data.order.order_status) {
                    applyStatusToDOM(data.order.order_status);
                }
            })
            .catch(() => {});
    }

    function advanceOrderStatus(newStatus) {
        const trackerWrap = document.querySelector('.eatzy-tracker-wrapper[data-order-number]');
        if (!trackerWrap) return;
        const orderNumber = trackerWrap.getAttribute('data-order-number');
        if (!orderNumber) return;

        highlightActiveSimBtn(newStatus);

        function updateLocalAndDOM() {
            applyStatusToDOM(newStatus);
            try {
                const myOrders = JSON.parse(localStorage.getItem('eatzy_my_orders') || '[]');
                const found = myOrders.find(o => o.order_number === orderNumber);
                if (found) {
                    found.order_status = newStatus;
                    localStorage.setItem('eatzy_my_orders', JSON.stringify(myOrders));
                }
            } catch (e) {}
            let alertMsg = `[EXPRESS DISPATCH] Order ${orderNumber} is now: ${newStatus}`;
            if (newStatus === 'Delivered') {
                alertMsg = `[EXPRESS DISPATCH] Order ${orderNumber} DELIVERED! Bon Appétit!`;
            }
            showSmsToast(alertMsg);
        }

        fetch(`index.php?option=com_eatzy&task=updateOrderStatus&order_number=${encodeURIComponent(orderNumber)}&status=${encodeURIComponent(newStatus)}`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                updateLocalAndDOM();
            })
            .catch(err => {
                // Client-side simulation fallback for static Vercel
                updateLocalAndDOM();
            });
    }

    function highlightActiveSimBtn(status) {
        document.querySelectorAll('.eatzy-sim-btn').forEach(btn => {
            const btnText = btn.innerText.toLowerCase();
            const target = (status || '').toLowerCase();
            if (btnText.includes(target) || (target.includes('preparing') && btnText.includes('prep'))) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function applyStatusToDOM(status) {
        const trackerWrap = document.querySelector('.eatzy-tracker-wrapper[data-order-number]');
        if (!trackerWrap) return;

        trackerWrap.setAttribute('data-order-status', status);

        const statusText = document.getElementById('eatzy-status-text');
        if (statusText) statusText.innerText = status;

        highlightActiveSimBtn(status);

        const steps = ['Received', 'Kitchen Preparing', 'Out for Delivery', 'Delivered'];
        const currentIdx = steps.indexOf(status);
        if (currentIdx === -1) return;

        document.querySelectorAll('.eatzy-step-node').forEach(node => {
            const stepKey = node.getAttribute('data-step-key');
            const stepIdx = steps.indexOf(stepKey);
            const marker = node.querySelector('.eatzy-step-marker');

            if (stepIdx < currentIdx) {
                node.classList.add('done');
                node.classList.remove('active');
                if (marker) marker.innerHTML = '<i class="fi fi-br-check"></i>';
            } else if (stepIdx === currentIdx) {
                node.classList.add('done', 'active');
                if (marker) marker.innerText = (stepIdx + 1);
            } else {
                node.classList.remove('done', 'active');
                if (marker) marker.innerText = (stepIdx + 1);
            }
        });

        // Update ETA countdown display text if delivered
        const etaCountdown = document.getElementById('eatzy-eta-countdown');
        if (etaCountdown && status === 'Delivered') {
            etaCountdown.innerHTML = '<span style="color:var(--eatzy-mint);"><i class="fi fi-br-check-circle" style="margin-right:6px;"></i> Safely Delivered</span>';
        }
    }

    function escapeHtml(str) {
        return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    return {
        init,
        addToCart,
        updateQty,
        toggleCart,
        quickApplyPromo,
        applyPromo,
        selectPayment,
        setMode,
        initiateCheckout,
        closePaymentModal,
        approvePayment,
        setCategory,
        setTag,
        filterMenu,
        claimPoints,
        advanceOrderStatus,
        pollOrderStatus
    };
})();

document.addEventListener('DOMContentLoaded', EatzyApp.init);
