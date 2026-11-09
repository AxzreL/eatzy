<?php
\defined('_JEXEC') or die;

$items = $this->items ?? [];
$categories = $this->categories ?? ['Mains', 'Starters', 'Drinks', 'Desserts'];
$heroDish = $items[0] ?? null;
?>

<div class="eatzy-app-container">
    <!-- Live Kitchen Pulse Pill -->
    <div class="eatzy-kitchen-pulse-bar">
        <div class="eatzy-pulse-left">
            <span class="eatzy-pulse-dot"></span>
            <span>KITCHEN LIVE &bull; BGC FLAGSHIP</span>
        </div>
        <div class="eatzy-pulse-right">
            <span><i class="fi fi-br-bolt" style="margin-right:4px;"></i> Avg Prep: <strong>14 Mins</strong> &bull; <i class="fi fi-br-clock" style="margin-right:4px; margin-left:4px;"></i> Est Delivery: <strong>25 Mins</strong></span>
        </div>
    </div>

    <!-- Hero Banner: Cyber-Bistro Showcase (Section 01, 05, 06) -->
    <header class="eatzy-hero-banner">
        <div class="eatzy-hero-left">
            <div class="eatzy-badge-strip">
                <span class="eatzy-pill">Direct Food Tech</span>
                <span class="eatzy-pill">Commission-Free</span>
                <span class="eatzy-pill">&lt; 90s Checkout Target</span>
            </div>
            <h1 class="eatzy-hero-title">
                CRAVE. CLICK. <span>CHOW.</span>
            </h1>
            <p class="eatzy-hero-sub">
                Eliminating third-party aggregator markups with chef-driven fast-casual dining, direct lightning delivery, and friction-free table reservations.
            </p>
            
            <div class="eatzy-hero-ctas">
                <a href="#eatzy-menu-section" class="eatzy-btn eatzy-btn-primary">
                    <i class="fi fi-br-utensils"></i> <span>ORDER ONLINE NOW</span>
                </a>
                <a href="index.php?option=com_eatzy&view=book" class="eatzy-btn eatzy-btn-outline">
                    <i class="fi fi-br-calendar"></i> <span>RESERVE A TABLE</span>
                </a>
            </div>
        </div>

        <!-- Hero Showcase Card (Right Column) -->
        <?php if ($heroDish): ?>
            <div class="eatzy-hero-showcase">
                <div class="eatzy-showcase-card">
                    <div class="eatzy-showcase-img-wrap">
                        <?php 
                            $heroImgSrc = $heroDish->image_url ?: 'images/foods/Burgers-Truffle-Burger-scaled.jpg';
                            if ($heroImgSrc && !str_starts_with($heroImgSrc, 'http')) {
                                $parts = explode('/', $heroImgSrc);
                                $encodedParts = array_map('rawurlencode', $parts);
                                $heroImgSrc = implode('/', $encodedParts);
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($heroImgSrc); ?>" 
                             alt="<?php echo htmlspecialchars($heroDish->name); ?>" 
                             class="eatzy-showcase-img" 
                             loading="eager"
                             referrerpolicy="no-referrer"
                             onerror="this.onerror=null; this.src='images/foods/Burgers-Truffle-Burger-scaled.jpg';">
                        <div class="eatzy-showcase-overlay"></div>
                        <span class="eatzy-dish-badge eatzy-badge-special"><i class="fi fi-br-flame" style="margin-right:4px;"></i> TODAY'S SIGNATURE</span>
                    </div>
                    <div class="eatzy-showcase-body">
                        <div class="eatzy-showcase-meta">
                            <h3 class="eatzy-showcase-title"><?php echo htmlspecialchars($heroDish->name); ?></h3>
                            <span class="eatzy-showcase-price">₱<?php echo number_format($heroDish->price, 2); ?></span>
                        </div>
                        <p style="font-size:12px; color:#888ea0; margin:0 0 12px 0;"><?php echo htmlspecialchars($heroDish->description); ?></p>
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div class="eatzy-showcase-stats">
                                <span><i class="fi fi-br-star" style="margin-right:4px; color:#ffb703;"></i> 4.9 (420+ reviews)</span>
                                <span>&bull; 18 ordered this hour</span>
                            </div>
                            <button class="eatzy-btn eatzy-btn-primary" style="padding:8px 18px; font-size:12px;" onclick="EatzyApp.addToCart(<?php echo $heroDish->id; ?>, '<?php echo addslashes($heroDish->name); ?>', <?php echo (float)$heroDish->price; ?>)">
                                <i class="fi fi-br-plus" style="margin-right:4px;"></i> Add &bull; ₱<?php echo number_format($heroDish->price, 2); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <!-- Dining Mode Selector -->
    <div class="eatzy-mode-tabs">
        <button class="eatzy-mode-btn active" onclick="EatzyApp.setMode('delivery', this)">
            <i class="fi fi-br-moped"></i> <span>Express Delivery</span>
        </button>
        <button class="eatzy-mode-btn" onclick="EatzyApp.setMode('pickup', this)">
            <i class="fi fi-br-shopping-bag"></i> <span>Direct Takeout</span>
        </button>
        <a href="index.php?option=com_eatzy&view=book" class="eatzy-mode-btn" style="text-decoration:none;">
            <i class="fi fi-br-calendar"></i> <span>Dine-In Table</span>
        </a>
    </div>

    <!-- Menu Section -->
    <section id="eatzy-menu-section" class="eatzy-menu-section">
        <!-- Live Search & Category Pills -->
        <div class="eatzy-controls-bar">
            <div class="eatzy-search-box">
                <i class="fi fi-br-search eatzy-search-icon"></i>
                <input type="text" id="eatzy-search-input" placeholder="Search craft burgers, pasta, drinks, desserts..." oninput="EatzyApp.filterMenu()">
            </div>
            
            <div class="eatzy-category-pills" id="eatzy-category-pills">
                <button class="eatzy-cat-btn active" data-cat="all" onclick="EatzyApp.setCategory('all', this)">All Dishes (<?php echo count($items); ?>)</button>
                <?php foreach ($categories as $cat): 
                    $catCount = count(array_filter($items, fn($i) => ($i->category === $cat)));
                ?>
                    <button class="eatzy-cat-btn" data-cat="<?php echo htmlspecialchars($cat); ?>" onclick="EatzyApp.setCategory('<?php echo htmlspecialchars($cat); ?>', this)">
                        <?php echo htmlspecialchars($cat); ?> (<?php echo $catCount; ?>)
                    </button>
                <?php endforeach; ?>
                <button class="eatzy-cat-btn" data-tag="Vegetarian" onclick="EatzyApp.setTag('Vegetarian', this)"><i class="fi fi-br-leaf" style="margin-right:4px;"></i> Vegetarian</button>
                <button class="eatzy-cat-btn" data-tag="Special" onclick="EatzyApp.setTag('Special', this)"><i class="fi fi-br-flame" style="margin-right:4px;"></i> Today's Specials</button>
            </div>
        </div>

        <!-- Menu Bento Grid (2-column on mobile, responsive desktop) -->
        <div class="eatzy-menu-grid" id="eatzy-menu-grid">
            <?php foreach ($items as $item): 
                $dietary = $item->dietary_tags ? explode(',', $item->dietary_tags) : [];
                $spiceIcon = str_repeat('🌶️', (int)$item->spice_level);
            ?>
                <div class="eatzy-card" 
                     data-id="<?php echo $item->id; ?>"
                     data-category="<?php echo htmlspecialchars($item->category); ?>"
                     data-name="<?php echo htmlspecialchars(strtolower($item->name)); ?>"
                     data-tags="<?php echo htmlspecialchars($item->dietary_tags); ?>"
                     data-special="<?php echo $item->is_special ? '1' : '0'; ?>">
                    
                    <div class="eatzy-card-media">
                        <?php 
                            $imgSrc = $item->image_url ?: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=700&q=80';
                            if ($imgSrc && !str_starts_with($imgSrc, 'http')) {
                                $parts = explode('/', $imgSrc);
                                $encodedParts = array_map('rawurlencode', $parts);
                                $imgSrc = implode('/', $encodedParts);
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                             alt="<?php echo htmlspecialchars($item->name); ?>" 
                             class="eatzy-card-img" 
                             loading="lazy"
                             referrerpolicy="no-referrer"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=700&q=80';">
                        <div class="eatzy-card-overlay"></div>
                        
                        <?php if ($item->is_special): ?>
                            <span class="eatzy-dish-badge eatzy-badge-special"><i class="fi fi-br-flame" style="margin-right:3px;"></i> TODAY'S SPECIAL</span>
                        <?php endif; ?>
                        <?php if (!empty($spiceIcon)): ?>
                            <span class="eatzy-dish-badge eatzy-badge-spice"><?php echo $spiceIcon; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="eatzy-card-body">
                        <div class="eatzy-card-header">
                            <h3 class="eatzy-dish-title"><?php echo htmlspecialchars($item->name); ?></h3>
                            <span class="eatzy-dish-price">₱<?php echo number_format($item->price, 2); ?></span>
                        </div>
                        <p class="eatzy-dish-desc"><?php echo htmlspecialchars($item->description); ?></p>
                        
                        <?php if (!empty($item->dietary_tags)): ?>
                            <div class="eatzy-dish-tags">
                                <?php foreach ($dietary as $tag): ?>
                                    <span class="eatzy-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="eatzy-card-footer">
                            <div id="eatzy-card-action-<?php echo $item->id; ?>">
                                <button class="eatzy-add-btn" 
                                        onclick="EatzyApp.addToCart(<?php echo $item->id; ?>, '<?php echo addslashes($item->name); ?>', <?php echo (float)$item->price; ?>)">
                                    <i class="fi fi-br-plus" style="font-size:12px;"></i> <span>Add to Cart &bull; ₱<?php echo number_format($item->price, 2); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Floating Sticky Cart Trigger for Mobile -->
    <div class="eatzy-floating-cart-bar" id="eatzy-floating-cart-bar" onclick="EatzyApp.toggleCart(true)">
        <div class="eatzy-float-left">
            <i class="fi fi-br-shopping-bag eatzy-float-cart-icon" style="font-size:18px;"></i>
            <span class="eatzy-float-count" id="eatzy-float-qty">0 items</span>
        </div>
        <div class="eatzy-float-right">
            <span class="eatzy-float-total" id="eatzy-float-total">₱0.00</span>
            <span>View Bag <i class="fi fi-br-arrow-right" style="margin-left:4px; font-size:11px;"></i></span>
        </div>
    </div>

    <!-- Slide-out Express Cart Drawer (Cyber-Bistro Deluxe) -->
    <div class="eatzy-cart-overlay" id="eatzy-cart-overlay" onclick="EatzyApp.toggleCart(false)"></div>
    <aside class="eatzy-cart-drawer" id="eatzy-cart-drawer">
        <div class="eatzy-drawer-header">
            <div class="eatzy-drawer-title">
                <h2>Your Order Bag</h2>
                <span class="eatzy-pill" id="eatzy-drawer-count" style="margin-left:8px;">0 items</span>
            </div>
            <button class="eatzy-drawer-close" onclick="EatzyApp.toggleCart(false)"><i class="fi fi-br-cross" style="font-size:14px;"></i></button>
        </div>

        <div class="eatzy-drawer-body">
            <!-- Free Delivery Progress Bar -->
            <div class="eatzy-delivery-tier" id="eatzy-delivery-tier">
                <span id="eatzy-delivery-tier-text">Add ₱1,000.00 for FREE delivery!</span>
                <div class="eatzy-progress-bg">
                    <div class="eatzy-progress-bar" id="eatzy-progress-bar" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Cart Items List -->
            <div class="eatzy-cart-items" id="eatzy-cart-items">
                <div class="eatzy-empty-cart">
                    <i class="fi fi-br-shopping-bag" style="font-size:36px; display:block; margin:0 auto 10px auto; opacity:0.35;"></i>
                    <p>Your bag is empty.</p>
                    <span>Tap any delicious dish from the menu to start!</span>
                </div>
            </div>

            <!-- Promo Code Input & One-Click Quick Chips -->
            <div class="eatzy-promo-section">
                <div class="eatzy-promo-input-group">
                    <input type="text" id="eatzy-promo-code" placeholder="Enter promo code" style="text-transform: uppercase;">
                    <button class="eatzy-promo-btn" onclick="EatzyApp.applyPromo()">Apply</button>
                </div>
                <div class="eatzy-quick-promos">
                    <span class="eatzy-quick-chip" onclick="EatzyApp.quickApplyPromo('EATZYFIRST')"><i class="fi fi-br-badge-percent" style="margin-right:4px;"></i> EATZYFIRST (15% OFF)</span>
                    <span class="eatzy-quick-chip" onclick="EatzyApp.quickApplyPromo('FREEDEL')"><i class="fi fi-br-tags" style="margin-right:4px;"></i> FREEDEL</span>
                </div>
                <div id="eatzy-promo-msg" class="eatzy-promo-msg" style="margin-top:6px; font-size:12px;"></div>
            </div>

            <!-- Diner Guest Details (<90s Target) -->
            <div class="eatzy-guest-form">
                <h3>Guest Delivery Information</h3>
                <div class="eatzy-form-group">
                    <label>Full Name *</label>
                    <input type="text" id="diner-name" placeholder="Enter your full name" required autocomplete="name">
                </div>
                <div class="eatzy-form-group">
                    <label>Philippine Mobile Number (Live SMS Tracking) *</label>
                    <input type="tel" id="diner-phone" placeholder="09XXXXXXXXX (11 digits)" maxlength="11" required autocomplete="tel">
                </div>
                <div class="eatzy-form-group">
                    <label>Delivery Address *</label>
                    <textarea id="diner-address" rows="2" placeholder="Unit, Floor, Building, Street, Barangay, City" required autocomplete="street-address"></textarea>
                </div>
                <div class="eatzy-form-group">
                    <label>Special Delivery Instructions</label>
                    <input type="text" id="diner-notes" placeholder="e.g. Ring doorbell, leave with reception">
                </div>
            </div>

            <!-- Payment Method Cards -->
            <div class="eatzy-payment-methods">
                <h3>Payment Method</h3>
                <label class="eatzy-payment-option active">
                    <input type="radio" name="eatzy_payment" value="gcash" checked onchange="EatzyApp.selectPayment(this)">
                    <span class="eatzy-pay-icon" style="background:#007dfa; color:white; font-weight:bold; padding:4px 10px; border-radius:6px;"><i class="fi fi-br-wallet"></i> GCash</span>
                    <span class="eatzy-pay-label" style="font-size:13px; font-weight:700;">GCash e-Wallet (Instant Clearance)</span>
                </label>
                <label class="eatzy-payment-option">
                    <input type="radio" name="eatzy_payment" value="maya" onchange="EatzyApp.selectPayment(this)">
                    <span class="eatzy-pay-icon" style="background:#00d084; color:black; font-weight:bold; padding:4px 10px; border-radius:6px;"><i class="fi fi-br-credit-card"></i> Maya</span>
                    <span class="eatzy-pay-label" style="font-size:13px; font-weight:700;">Maya e-Wallet &amp; Cards</span>
                </label>
                <label class="eatzy-payment-option">
                    <input type="radio" name="eatzy_payment" value="cod" onchange="EatzyApp.selectPayment(this)">
                    <span class="eatzy-pay-icon" style="background:#4b5563; color:white; font-weight:bold; padding:4px 10px; border-radius:6px;"><i class="fi fi-br-dollar"></i> Cash</span>
                    <span class="eatzy-pay-label" style="font-size:13px; font-weight:700;">Cash on Delivery (COD)</span>
                </label>
            </div>

            <!-- Pricing Breakdown -->
            <div class="eatzy-price-summary">
                <div class="eatzy-summary-row">
                    <span>Subtotal</span>
                    <span id="eatzy-summary-subtotal">₱0.00</span>
                </div>
                <div class="eatzy-summary-row" id="eatzy-discount-row" style="display:none; color: var(--eatzy-mint); font-weight:700;">
                    <span>Promo Discount</span>
                    <span id="eatzy-summary-discount">-₱0.00</span>
                </div>
                <div class="eatzy-summary-row">
                    <span>Delivery Fee</span>
                    <span id="eatzy-summary-delivery">₱65.00</span>
                </div>
                <div class="eatzy-summary-row eatzy-summary-total">
                    <span>Total Amount</span>
                    <span id="eatzy-summary-total">₱65.00</span>
                </div>
            </div>
        </div>

        <div class="eatzy-drawer-footer">
            <button class="eatzy-checkout-btn" id="eatzy-checkout-btn" onclick="EatzyApp.initiateCheckout()">
                <span>Proceed to Pay</span> <i class="fi fi-br-arrow-right" style="margin-left:6px; font-size:12px;"></i>
            </button>
        </div>
    </aside>

    <!-- Fintech-Grade GCash / Maya Modal -->
    <div class="eatzy-modal-overlay" id="eatzy-payment-modal" style="display:none;">
        <div class="eatzy-payment-dialog">
            <div class="eatzy-gateway-header" id="eatzy-gateway-header">
                <div class="eatzy-gateway-brand">
                    <span class="eatzy-gateway-logo" id="eatzy-gateway-logo">GCash</span>
                    <span class="eatzy-pill" style="font-size:9px; padding:2px 8px;"><i class="fi fi-br-shield-check" style="margin-right:3px;"></i> 256-bit TLS</span>
                </div>
                <button class="eatzy-modal-close" onclick="EatzyApp.closePaymentModal()"><i class="fi fi-br-cross" style="font-size:14px;"></i></button>
            </div>

            <div class="eatzy-gateway-body" style="padding: 24px; text-align: center;">
                <span style="font-size:11px; text-transform:uppercase; color:#888ea0; letter-spacing:0.5px;">Merchant Recipient</span>
                <h4 style="margin:4px 0 16px 0; font-size:15px; color:#fff;">EATZY PHILIPPINES INC. (eatzy.ph)</h4>
                
                <div class="eatzy-amount-box">
                    <span style="font-size:11px; color:#888ea0; text-transform:uppercase;">Amount Due</span>
                    <h2 class="eatzy-amount-value" id="eatzy-modal-amount">₱0.00</h2>
                    <span class="eatzy-pill" id="eatzy-modal-orderno" style="font-size:10px;">EATZ-XXXX</span>
                </div>

                <div class="eatzy-qr-mockup" style="margin-bottom:16px;">
                    <div style="display:inline-block; background:#fff; padding:12px; border-radius:14px; box-shadow:0 10px 25px rgba(0,0,0,0.5);">
                        <div style="width:140px; height:140px; background:repeating-conic-gradient(#000 0% 25%, #fff 0% 50%) 50% / 14px 14px; border-radius:4px;"></div>
                        <span style="display:block; color:#000; font-size:10px; font-weight:800; margin-top:6px;">Scan to Pay with App</span>
                    </div>
                </div>

                <div class="eatzy-mpin-box" style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; color:#9da3b4; margin-bottom:6px;">Enter 6-digit MPIN to authorize:</label>
                    <input type="password" maxlength="6" id="eatzy-mpin-field" placeholder="••••••" class="eatzy-mpin-input" autocomplete="off" inputmode="numeric">
                </div>

                <div style="font-size:11px; color:#888ea0; margin-bottom:16px;">
                    <i class="fi fi-br-shield-check" style="color:var(--eatzy-mint); margin-right:4px;"></i> Verified 256-bit Encrypted Transaction Clearance
                </div>

                <button class="eatzy-btn-approve" id="eatzy-btn-approve" onclick="EatzyApp.approvePayment()">
                    <span>Authorize &amp; Pay Now</span> <i class="fi fi-br-arrow-right" style="margin-left:6px; font-size:12px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
