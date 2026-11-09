<?php
\defined('_JEXEC') or die;

$reservations = $this->reservations ?? [];
?>

<div class="eatzy-admin-reservations-container" style="padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; background:#16161a; color:#fff; padding:16px 24px; border-radius:12px;">
        <div>
            <h1 style="margin:0; font-size:24px; color:#ff5414;">Table Reservations Manager</h1>
            <p style="margin:4px 0 0 0; color:#a0a0a8; font-size:13px;">Guest Bookings • Capacity Tracking • Seating Zone Allocation</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="index.php?option=com_eatzy&view=kds" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">🍳 Kitchen Display (KDS)</a>
            <a href="index.php?option=com_eatzy&view=menu" class="btn btn-outline-light" style="padding:8px 16px; border-radius:8px; border:1px solid #444; color:#fff; text-decoration:none;">🍔 Menu Catalog</a>
        </div>
    </div>

    <div style="background:#1e1e24; border-radius:12px; border:1px solid #2e2e38; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; color:#eee; font-size:13px;">
            <thead>
                <tr style="background:#121215; text-align:left; color:#a0a0a8; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    <th style="padding:14px 16px;">Booking Code</th>
                    <th style="padding:14px 16px;">Guest Name</th>
                    <th style="padding:14px 16px;">Contact Phone</th>
                    <th style="padding:14px 16px;">Date &amp; Time</th>
                    <th style="padding:14px 16px;">Guests</th>
                    <th style="padding:14px 16px;">Zone</th>
                    <th style="padding:14px 16px;">Status</th>
                    <th style="padding:14px 16px;">Special Notes</th>
                    <th style="padding:14px 16px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="9" style="padding:30px; text-align:center; color:#777;">No reservations recorded yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr style="border-top:1px solid #2e2e38;">
                            <td style="padding:14px 16px; font-weight:bold; color:#ff5414;"><?php echo htmlspecialchars($res->reservation_code); ?></td>
                            <td style="padding:14px 16px; font-weight:600;"><?php echo htmlspecialchars($res->guest_name); ?></td>
                            <td style="padding:14px 16px;"><a href="tel:<?php echo htmlspecialchars($res->guest_phone); ?>" style="color:#00c48c; text-decoration:none;"><?php echo htmlspecialchars($res->guest_phone); ?></a></td>
                            <td style="padding:14px 16px;"><?php echo htmlspecialchars($res->reservation_date); ?> at <strong><?php echo htmlspecialchars($res->time_slot); ?></strong></td>
                            <td style="padding:14px 16px;"><?php echo (int)$res->party_size; ?> Guests</td>
                            <td style="padding:14px 16px;">
                                <span style="background:#2a2a35; padding:3px 8px; border-radius:6px; font-size:11px;">
                                    <?php echo htmlspecialchars($res->seating_zone); ?>
                                </span>
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if ($res->status === 'Confirmed'): ?>
                                    <span style="background:rgba(0,196,140,0.2); color:#00c48c; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;">✓ Confirmed</span>
                                <?php elseif ($res->status === 'Seated'): ?>
                                    <span style="background:rgba(0,125,250,0.2); color:#007dfa; padding:3px 8px; border-radius:6px; font-weight:bold; font-size:11px;">🍽️ Seated</span>
                                <?php else: ?>
                                    <span style="background:#333; color:#aaa; padding:3px 8px; border-radius:6px; font-size:11px;"><?php echo htmlspecialchars($res->status); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:14px 16px; color:#aaa;"><?php echo htmlspecialchars($res->special_notes ?: 'None'); ?></td>
                            <td style="padding:14px 16px;">
                                <?php if ($res->status === 'Confirmed'): ?>
                                    <a href="index.php?option=com_eatzy&task=updateReservationStatus&id=<?php echo $res->id; ?>&status=Seated" 
                                       style="background:#007dfa; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px; text-decoration:none; font-weight:600;">Seat Diner</a>
                                <?php elseif ($res->status === 'Seated'): ?>
                                    <a href="index.php?option=com_eatzy&task=updateReservationStatus&id=<?php echo $res->id; ?>&status=Completed" 
                                       style="background:#00c48c; color:#000; padding:4px 8px; border-radius:4px; font-size:11px; text-decoration:none; font-weight:600;">Complete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

