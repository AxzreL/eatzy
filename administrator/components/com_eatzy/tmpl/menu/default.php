<?php
\defined('_JEXEC') or die;

$dishes = $this->dishes ?? [];
?>

<div class="eatzy-admin-menu-container" style="padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; background:#16161a; color:#fff; padding:16px 24px; border-radius:12px;">
        <div>
            <h1 style="margin:0; font-size:24px; color:#ff5414;">EATZY Menu Catalog Manager</h1>
            <p style="margin:4px 0 0 0; color:#a0a0a8; font-size:13px;">Manage Fast-Casual Dishes, Pricing, Dietary Badges, and Availability</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="index.php?option=com_eatzy&view=kds" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">🍳 Kitchen Display (KDS)</a>
            <a href="index.php?option=com_eatzy&view=reservations" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">📅 Table Reservations</a>
        </div>
    </div>

    <div style="background:#1e1e24; border-radius:12px; border:1px solid #2e2e38; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; color:#eee; font-size:13px;">
            <thead>
                <tr style="background:#121215; text-align:left; color:#a0a0a8; font-size:12px; text-transform:uppercase;">
                    <th style="padding:14px 16px;">Dish Title</th>
                    <th style="padding:14px 16px;">Category</th>
                    <th style="padding:14px 16px;">Price (PHP)</th>
                    <th style="padding:14px 16px;">Dietary / Tags</th>
                    <th style="padding:14px 16px;">Spice</th>
                    <th style="padding:14px 16px;">Chef Special</th>
                    <th style="padding:14px 16px;">Availability</th>
                    <th style="padding:14px 16px;">Quick Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dishes as $d): ?>
                    <tr style="border-top:1px solid #2e2e38;">
                        <td style="padding:14px 16px; font-weight:600; color:#fff;"><?php echo htmlspecialchars($d->name); ?></td>
                        <td style="padding:14px 16px;">
                            <span style="background:#2a2a35; padding:3px 8px; border-radius:6px; font-size:11px;">
                                <?php echo htmlspecialchars($d->category); ?>
                            </span>
                        </td>
                        <td style="padding:14px 16px; font-weight:bold; color:#ff5414;">₱<?php echo number_format((float)$d->price, 2); ?></td>
                        <td style="padding:14px 16px; color:#00c48c; font-size:11px;"><?php echo htmlspecialchars($d->dietary_tags ?: 'Standard'); ?></td>
                        <td style="padding:14px 16px;"><?php echo str_repeat('🌶️', (int)$d->spice_level) ?: '-'; ?></td>
                        <td style="padding:14px 16px;">
                            <?php if ($d->is_special): ?>
                                <span style="background:rgba(255,84,20,0.2); color:#ff5414; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;">🔥 Special</span>
                            <?php else: ?>
                                <span style="color:#777;">Standard</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px;">
                            <?php if ($d->is_available): ?>
                                <span style="background:rgba(0,196,140,0.2); color:#00c48c; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;">✓ Available</span>
                            <?php else: ?>
                                <span style="background:rgba(255,50,50,0.2); color:#ff4444; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;">Sold Out</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:14px 16px;">
                            <a href="index.php?option=com_eatzy&task=toggleDish&id=<?php echo $d->id; ?>" 
                               style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:11px; text-decoration:none;">
                                Toggle Status
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

