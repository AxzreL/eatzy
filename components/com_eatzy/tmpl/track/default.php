<?php
\defined('_JEXEC') or die;

$order = $this->order;
if (!$order): ?>
<div class="eatzy-app-container">
    <div class="eatzy-tracker-wrapper">
        <header class="eatzy-page-header">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                <a href="index.php?option=com_eatzy&view=menu" class="eatzy-back-link" style="margin-bottom:0;"><i class="fi fi-br-arrow-left" style="margin-right:6px;"></i> Back to Food Menu</a>
                <span class="eatzy-pill"><i class="fi fi-br-bolt" style="margin-right:4px; font-size:11px;"></i> Live Dispatch Tracking</span>
            </div>
            <h1 class="eatzy-page-title">Track Your EATZY Order</h1>
            <p class="eatzy-page-sub">Enter your order ticket number or mobile number to track kitchen prep and express dispatch.</p>
        </header>

        <!-- Ticket Lookup Search Bar -->
        <div class="eatzy-order-lookup-bar">
            <form action="index.php" method="get" class="eatzy-lookup-form">
                <input type="hidden" name="option" value="com_eatzy">
                <input type="hidden" name="view" value="track">
                <i class="fi fi-br-search eatzy-lookup-icon"></i>
                <input type="text" name="id" class="eatzy-lookup-input" placeholder="Enter ticket # (e.g. EATZ-648D1D) or 11-digit phone" autocomplete="off" required>
                <button type="submit" class="eatzy-lookup-btn" aria-label="Lookup Ticket">
                    <i class="fi fi-br-search" style="font-size:12px;"></i>
                    <span class="eatzy-lookup-text-full">Track Order</span>
                    <span class="eatzy-lookup-text-short">Track</span>
                </button>
            </form>
        </div>

        <!-- Recent Device Orders (Populated via JS localStorage) -->
        <div id="eatzy-recent-orders-card" class="eatzy-stepper-card" style="display:none; margin-top:20px;">
            <h3 style="font-size:14px; font-weight:800; color:#fff; text-transform:uppercase; margin:0 0 14px 0;">
                <i class="fi fi-br-clock" style="margin-right:6px; color:var(--eatzy-primary);"></i> Orders Placed on This Device
            </h3>
            <div id="eatzy-recent-orders-list" style="display:flex; flex-direction:column; gap:10px;"></div>
        </div>

        <div style="text-align:center; padding:30px 10px;">
            <p style="color:#888ea0; font-size:13px; margin-bottom:16px;">Craving chef-crafted burgers, pasta, or drinks?</p>
            <a href="index.php?option=com_eatzy&view=menu" class="eatzy-btn eatzy-btn-primary">
                <i class="fi fi-br-utensils"></i> <span>Order Food Online Now</span>
            </a>
        </div>
    </div>
</div>
<?php return; endif;

$status = $order->order_status ?? 'Received';
$steps = [
    'Received' => [
        'title' => 'Order Received &amp; Authorized',
        'desc' => 'Payment cleared via ' . strtoupper($order->payment_method ?: 'GCash') . ' webhook.'
    ],
    'Kitchen Preparing' => [
        'title' => 'Chefs Firing in Kitchen',
        'desc' => 'Brioche buns toasted and wagyu-blend patties sizzling on the grill.'
    ],
    'Out for Delivery' => [
        'title' => 'Express Rider En Route',
        'desc' => 'Your insulated thermal bag is on the road to your delivery address.'
    ],
    'Delivered' => [
        'title' => 'Order Delivered &bull; Bon Appétit!',
        'desc' => 'Package safely handed over. Enjoy your direct-to-diner feast!'
    ]
];

$stepKeys = array_keys($steps);
$currentStepIdx = array_search($status, $stepKeys);
if ($currentStepIdx === false) $currentStepIdx = 0;
?>

<div class="eatzy-app-container">
    <div class="eatzy-tracker-wrapper" data-order-number="<?php echo htmlspecialchars($order->order_number); ?>" data-order-status="<?php echo htmlspecialchars($status); ?>">
        <!-- Top Back Navigation & Header -->
        <header class="eatzy-page-header">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                <a href="index.php?option=com_eatzy&view=menu" class="eatzy-back-link" style="margin-bottom:0;"><i class="fi fi-br-arrow-left" style="margin-right:6px;"></i> Back to Food Menu</a>
                <span class="eatzy-pill"><i class="fi fi-br-bolt" style="margin-right:4px; font-size:11px;"></i> Real-Time Dispatch</span>
            </div>

            <!-- Ticket Lookup Search Bar -->
            <div class="eatzy-order-lookup-bar">
                <form action="index.php" method="get" class="eatzy-lookup-form">
                    <input type="hidden" name="option" value="com_eatzy">
                    <input type="hidden" name="view" value="track">
                    <i class="fi fi-br-search eatzy-lookup-icon"></i>
                    <input type="text" name="id" class="eatzy-lookup-input" placeholder="Search ticket # (e.g. EATZ-648D1D)" value="<?php echo htmlspecialchars($order->order_number); ?>" autocomplete="off" autocapitalize="characters">
                    <button type="submit" class="eatzy-lookup-btn" aria-label="Lookup Ticket">
                        <i class="fi fi-br-search" style="font-size:12px;"></i>
                        <span class="eatzy-lookup-text-full">Lookup Ticket</span>
                        <span class="eatzy-lookup-text-short">Lookup</span>
                    </button>
                </form>
            </div>

            <div class="eatzy-tracker-pill-strip">
                <span class="eatzy-pill"><i class="fi fi-br-check" style="margin-right:4px;"></i> Order Confirmed</span>
                <span class="eatzy-pill" style="text-transform:uppercase;"><i class="fi fi-br-badge-check" style="margin-right:4px;"></i> <?php echo htmlspecialchars($order->payment_method ?: 'GCash'); ?> Cleared</span>
                <span class="eatzy-pill" id="eatzy-live-kds-pill"><i class="fi fi-br-hat-chef" style="margin-right:4px;"></i> Status: <strong id="eatzy-status-text" style="color:var(--eatzy-mint); margin-left:3px;"><?php echo htmlspecialchars($status); ?></strong></span>
            </div>
            <h1 class="eatzy-tracker-order-id"><?php echo htmlspecialchars($order->order_number); ?></h1>
            <p class="eatzy-page-sub">Direct Kitchen &amp; Express Dispatch Tracking</p>
        </header>

        <!-- Live Estimated Arrival Card -->
        <div class="eatzy-eta-card">
            <div class="eatzy-eta-icon"><i class="fi fi-br-moped" style="font-size:28px;"></i></div>
            <div class="eatzy-eta-content">
                <span class="eatzy-eta-label"><i class="fi fi-br-clock" style="margin-right:4px; font-size:11px;"></i> ESTIMATED DISPATCH &amp; DELIVERY</span>
                <h2 class="eatzy-eta-time" id="eatzy-eta-countdown">20 Mins Remaining</h2>
                <p class="eatzy-eta-sub">Delivering to: <strong><?php echo htmlspecialchars($order->customer_name); ?></strong> &bull; <?php echo htmlspecialchars($order->customer_phone); ?></p>
                <p class="eatzy-eta-addr"><?php echo htmlspecialchars($order->delivery_address); ?></p>
                <?php if (!empty($order->delivery_notes)): ?>
                    <p style="font-size:11px; color:#888ea0; margin:4px 0 0 0;"><i class="fi fi-br-info" style="margin-right:3px;"></i> Note: <?php echo htmlspecialchars($order->delivery_notes); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4-Stage Visual Stepper -->
        <div class="eatzy-stepper-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:15px; font-weight:800; color:#fff; text-transform:uppercase; letter-spacing:0.5px; margin:0;">Order Milestone Timeline</h3>
                <span class="eatzy-pill" style="font-size:10px;"><span class="eatzy-pulse-dot" style="display:inline-block; width:6px; height:6px; margin-right:4px;"></span> Live Polling</span>
            </div>
            <div class="eatzy-stepper" id="eatzy-stepper-nodes">
                <?php foreach ($steps as $key => $sData): 
                    $idx = array_search($key, $stepKeys);
                    $isDone = ($idx <= $currentStepIdx);
                    $isActive = ($idx === $currentStepIdx);
                ?>
                    <div class="eatzy-step-node <?php echo $isDone ? 'done' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>" data-step-key="<?php echo $key; ?>">
                        <div class="eatzy-step-marker">
                            <?php if ($idx < $currentStepIdx): ?><i class="fi fi-br-check"></i><?php else: ?><?php echo ($idx + 1); ?><?php endif; ?>
                        </div>
                        <div class="eatzy-step-text">
                            <h4><?php echo $sData['title']; ?></h4>
                            <p><?php echo $sData['desc']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Customer Live Order Simulation / Testing Controls -->
        <div class="eatzy-sim-controls" style="background:rgba(255,255,255,0.03); border:1px dashed rgba(255,255,255,0.18); border-radius:14px; padding:16px; margin-bottom:24px; text-align:center;">
            <div style="font-size:12px; font-weight:800; color:#fff; margin-bottom:6px;">
                <i class="fi fi-br-bolt" style="color:var(--eatzy-primary); margin-right:4px;"></i> Customer Kitchen Simulation Controller
            </div>
            <p style="font-size:11px; color:#888ea0; margin:0 0 12px 0;">Advance this order's status to test real-time milestone transitions and live carrier SMS alerts:</p>
            <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                <button type="button" class="eatzy-sim-btn" onclick="EatzyApp.advanceOrderStatus('Received')">1. Received</button>
                <button type="button" class="eatzy-sim-btn" onclick="EatzyApp.advanceOrderStatus('Kitchen Preparing')">2. Kitchen Prep</button>
                <button type="button" class="eatzy-sim-btn" onclick="EatzyApp.advanceOrderStatus('Out for Delivery')">3. Out for Delivery</button>
                <button type="button" class="eatzy-sim-btn" onclick="EatzyApp.advanceOrderStatus('Delivered')">4. Delivered ✓</button>
            </div>
        </div>

        <!-- Order Summary & Items Breakdown -->
        <div class="eatzy-tracker-breakdown">
            <h3 style="font-size:16px; font-weight:800; margin:0 0 16px 0; color:#fff;">Items in this Ticket</h3>
            <div class="eatzy-tracker-items">
                <?php if (!empty($order->items)): ?>
                    <?php foreach ($order->items as $it): ?>
                        <div class="eatzy-tracker-item-row">
                            <div class="eatzy-tracker-item-info">
                                <span class="eatzy-item-qty"><?php echo (int)$it->quantity; ?>x</span>
                                <span class="eatzy-item-title"><?php echo htmlspecialchars($it->item_name); ?></span>
                            </div>
                            <span class="eatzy-item-price">&#8369;<?php echo number_format((float)$it->total_price, 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="eatzy-tracker-item-row">
                        <div class="eatzy-tracker-item-info">
                            <span class="eatzy-item-qty">1x</span>
                            <span class="eatzy-item-title">Fresh Bistro Item</span>
                        </div>
                        <span class="eatzy-item-price">&#8369;<?php echo number_format((float)$order->total, 2); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="eatzy-tracker-totals">
                <div class="eatzy-total-row">
                    <span>Subtotal</span>
                    <span>&#8369;<?php echo number_format((float)$order->subtotal, 2); ?></span>
                </div>
                <?php if ((float)$order->discount > 0): ?>
                    <div class="eatzy-total-row" style="color:var(--eatzy-mint); font-weight:700;">
                        <span>Promo Discount Applied</span>
                        <span>-&#8369;<?php echo number_format((float)$order->discount, 2); ?></span>
                    </div>
                <?php endif; ?>
                <div class="eatzy-total-row">
                    <span>Direct Delivery Fee</span>
                    <span><?php echo (float)$order->delivery_fee > 0 ? '&#8369;' . number_format((float)$order->delivery_fee, 2) : 'FREE'; ?></span>
                </div>
                <div class="eatzy-total-row eatzy-final-total">
                    <span>Total Amount Paid (<?php echo strtoupper($order->payment_method ?: 'GCash'); ?>)</span>
                    <span style="color:var(--eatzy-primary);">&#8369;<?php echo number_format((float)$order->total, 2); ?></span>
                </div>
                <div class="eatzy-txn-ref">
                    <span>Payment Reference:</span>
                    <code><?php echo htmlspecialchars($order->transaction_ref ?: 'GCASH-TXN'); ?></code>
                </div>
            </div>
        </div>

        <!-- Brand Loyalty Points Prompt (Section 02 Objective) -->
        <div class="eatzy-loyalty-prompt" id="eatzy-loyalty-prompt">
            <div class="eatzy-loyalty-left">
                <div class="eatzy-loyalty-icon"><i class="fi fi-br-star" style="font-size:24px; color:#ffb703;"></i></div>
                <div class="eatzy-loyalty-info">
                    <h4>Earned <?php echo max(10, round((float)$order->total * 0.05)); ?> EATZY Dining Points!</h4>
                    <p>Linked to mobile number <strong><?php echo htmlspecialchars($order->customer_phone); ?></strong>. Redeem on your next direct order.</p>
                </div>
            </div>
            <button type="button" class="eatzy-btn eatzy-btn-mint" onclick="EatzyApp.claimPoints(this)">
                <span>Save to Profile</span> <i class="fi fi-br-arrow-right" style="margin-left:6px; font-size:12px;"></i>
            </button>
        </div>
    </div>
</div>

