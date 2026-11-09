<?php
\defined('_JEXEC') or die;

$orders = $this->orders ?? [];
$columns = [
    'Received' => ['title' => 'Incoming Webhook Orders', 'badge' => 'badge-info'],
    'Kitchen Preparing' => ['title' => 'Chefs Firing on Line', 'badge' => 'badge-warning'],
    'Out for Delivery' => ['title' => 'With Express Rider', 'badge' => 'badge-primary'],
    'Delivered' => ['title' => 'Completed Orders', 'badge' => 'badge-success']
];
?>

<div class="eatzy-admin-kds-container" style="padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; background:#16161a; color:#fff; padding:16px 24px; border-radius:12px;">
        <div>
            <h1 style="margin:0; font-size:24px; color:#ff5414;">EATZY Kitchen Display System (KDS)</h1>
            <p style="margin:4px 0 0 0; color:#a0a0a8; font-size:13px;">Live Order Tickets • Direct GCash/Maya Webhook Feed</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="index.php?option=com_eatzy&view=reservations" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">📅 Table Reservations</a>
            <a href="index.php?option=com_eatzy&view=menu" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">🍔 Menu Catalog</a>
            <button onclick="location.reload()" style="background:#ff5414; color:#fff; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:bold;">🔄 Refresh Board</button>
        </div>
    </div>

    <!-- 4-Column KDS Board -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <?php foreach ($columns as $statusKey => $col): 
            $filtered = array_filter($orders, function($o) use ($statusKey) {
                return ($o->order_status === $statusKey);
            });
        ?>
            <div style="background:#1e1e24; border-radius:12px; padding:14px; border:1px solid #2e2e38; min-height:500px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #2e2e38;">
                    <h3 style="margin:0; font-size:15px; color:#fff; font-weight:600;"><?php echo htmlspecialchars($col['title']); ?></h3>
                    <span style="background:#2e2e38; color:#00c48c; padding:2px 8px; border-radius:12px; font-size:12px; font-weight:bold;"><?php echo count($filtered); ?></span>
                </div>

                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php if (empty($filtered)): ?>
                        <div style="color:#666; text-align:center; padding:30px 10px; font-size:13px;">No tickets in this lane.</div>
                    <?php else: ?>
                        <?php foreach ($filtered as $ord): ?>
                            <div style="background:#121215; border-radius:8px; padding:14px; border-left:4px solid #ff5414; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <strong style="color:#fff; font-size:16px;"><?php echo htmlspecialchars($ord->order_number); ?></strong>
                                    <span style="background:#007dfa; color:#fff; font-size:10px; font-weight:bold; padding:2px 6px; border-radius:4px; text-transform:uppercase;">
                                        <?php echo htmlspecialchars($ord->payment_method); ?> PAID
                                    </span>
                                </div>
                                <div style="color:#a0a0a8; font-size:12px; margin:4px 0 8px 0;">
                                    <span><?php echo htmlspecialchars($ord->customer_name); ?></span> • 
                                    <a href="tel:<?php echo htmlspecialchars($ord->customer_phone); ?>" style="color:#00c48c; text-decoration:none;"><?php echo htmlspecialchars($ord->customer_phone); ?></a>
                                </div>

                                <!-- Items List -->
                                <div style="background:#1a1a20; padding:8px; border-radius:6px; margin-bottom:10px;">
                                    <?php if (!empty($ord->items)): ?>
                                        <?php foreach ($ord->items as $it): ?>
                                            <div style="display:flex; justify-content:space-between; font-size:12px; color:#ddd; margin-bottom:4px;">
                                                <span><strong><?php echo (int)$it->quantity; ?>x</strong> <?php echo htmlspecialchars($it->item_name); ?></span>
                                                <span style="color:#aaa;">₱<?php echo number_format((float)$it->total_price, 2); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <div style="border-top:1px solid #333; margin-top:6px; padding-top:6px; display:flex; justify-content:space-between; font-size:12px; font-weight:bold; color:#fff;">
                                        <span>Total:</span>
                                        <span style="color:#ff5414;">₱<?php echo number_format((float)$ord->total, 2); ?></span>
                                    </div>
                                </div>

                                <div style="font-size:11px; color:#777; margin-bottom:10px;">
                                    <span>Ref: <?php echo htmlspecialchars($ord->transaction_ref ?: 'GCASH-TXN'); ?></span>
                                </div>

                                <!-- Status Actions -->
                                <div style="display:flex; gap:6px;">
                                    <?php if ($statusKey === 'Received'): ?>
                                        <a href="index.php?option=com_eatzy&task=updateOrderStatus&id=<?php echo $ord->id; ?>&status=Kitchen+Preparing" 
                                           style="flex:1; text-align:center; background:#ff5414; color:#fff; padding:6px; border-radius:4px; font-size:12px; font-weight:bold; text-decoration:none;">
                                            Start Cooking &rarr;
                                        </a>
                                    <?php elseif ($statusKey === 'Kitchen Preparing'): ?>
                                        <a href="index.php?option=com_eatzy&task=updateOrderStatus&id=<?php echo $ord->id; ?>&status=Out+for+Delivery" 
                                           style="flex:1; text-align:center; background:#007dfa; color:#fff; padding:6px; border-radius:4px; font-size:12px; font-weight:bold; text-decoration:none;">
                                            Dispatch to Rider &rarr;
                                        </a>
                                    <?php elseif ($statusKey === 'Out for Delivery'): ?>
                                        <a href="index.php?option=com_eatzy&task=updateOrderStatus&id=<?php echo $ord->id; ?>&status=Delivered" 
                                           style="flex:1; text-align:center; background:#00c48c; color:#000; padding:6px; border-radius:4px; font-size:12px; font-weight:bold; text-decoration:none;">
                                            Mark Delivered ✓
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#00c48c; font-size:11px;">✓ Order Completed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

