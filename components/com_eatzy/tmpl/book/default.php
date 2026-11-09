<?php
\defined('_JEXEC') or die;

$slots = $this->slots ?? [
    'Lunch' => ['11:30 AM', '12:15 PM', '1:00 PM', '1:45 PM'],
    'Dinner' => ['5:30 PM', '6:15 PM', '7:00 PM', '7:45 PM', '8:30 PM', '9:15 PM']
];
$zones = $this->zones ?? [
    'Indoor Main Dining' => 'Air-conditioned dining lounge with cozy banquette seating.',
    'Al Fresco Garden Patio' => 'Open-air courtyard patio surrounded by lush ambient lighting.'
];

// Generate Future Days for This Week (Next 7 Days) and Next Week (Following 7 Days) - Month excluded
$daysThisWeek = [];
$daysNextWeek = [];

for ($i = 0; $i < 7; $i++) {
    $time = strtotime("+$i day");
    $daysThisWeek[] = [
        'date'    => date('Y-m-d', $time),
        'dayNum'  => date('d', $time),
        'dayName' => date('D', $time),
        'dayFull' => date('l', $time),
        'label'   => ($i === 0) ? 'Today (' . date('l', $time) . ')' : (($i === 1) ? 'Tomorrow (' . date('l', $time) . ')' : date('l', $time)),
        'badge'   => ($i === 0) ? 'Today' : (($i === 1) ? 'Tmrw' : date('D', $time))
    ];
}

for ($i = 7; $i < 14; $i++) {
    $time = strtotime("+$i day");
    $daysNextWeek[] = [
        'date'    => date('Y-m-d', $time),
        'dayNum'  => date('d', $time),
        'dayName' => date('D', $time),
        'dayFull' => date('l', $time),
        'label'   => 'Next ' . date('l', $time),
        'badge'   => date('D', $time)
    ];
}
?>

<div class="eatzy-app-container">
    <div class="eatzy-booking-wrapper">
        <header class="eatzy-page-header">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
                <a href="index.php?option=com_eatzy&view=menu" class="eatzy-back-link" style="margin-bottom:0;"><i class="fi fi-br-arrow-left" style="margin-right:6px;"></i> Back to Food Menu</a>
                <button type="button" class="eatzy-pill" style="cursor:pointer; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.18); color:#fff;" onclick="EatzyBooking.toggleLookup()">
                    <i class="fi fi-br-search" style="margin-right:4px;"></i> Find My Reservation
                </button>
            </div>
            <h1 class="eatzy-page-title">Reserve Your Table at EATZY</h1>
            <p class="eatzy-page-sub">Guaranteed seating without waiting in line. Choose your preferred dining time and ambiance.</p>
        </header>

        <!-- Existing Reservation Lookup Modal / Card -->
        <div class="eatzy-booking-card" id="eatzy-res-lookup-card" style="display:none; margin-bottom:20px; border-color:var(--eatzy-primary);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h3 style="margin:0; font-size:16px; color:#fff;"><i class="fi fi-br-search" style="margin-right:6px; color:var(--eatzy-primary);"></i> Look Up Existing Reservation</h3>
                <button type="button" style="background:none; border:none; color:#888ea0; font-size:16px; cursor:pointer;" onclick="EatzyBooking.toggleLookup()">&times;</button>
            </div>
            <p style="font-size:12px; color:#888ea0; margin:0 0 14px 0;">Enter your reservation confirmation code or mobile phone number.</p>
            <form onsubmit="EatzyBooking.lookup(event)" style="display:flex; gap:8px; flex-wrap:wrap;">
                <input type="text" id="res-lookup-input" class="eatzy-input" placeholder="e.g. EATZ-RES-7712 or 09XXXXXXXXX" style="flex:1; min-width:180px;">
                <button type="submit" class="eatzy-btn eatzy-btn-primary" style="padding:10px 18px; white-space:nowrap;">Check Table</button>
            </form>
            <div id="res-lookup-result" style="margin-top:14px; display:none;"></div>
        </div>

        <div class="eatzy-booking-card" id="eatzy-booking-form-wrap">
            <form id="eatzy-booking-form" onsubmit="EatzyBooking.submit(event)">
                <!-- 1. Party Size -->
                <div class="eatzy-book-step">
                    <label class="eatzy-step-label">1. Party Size (Number of Guests)</label>
                    <div class="eatzy-guest-selector">
                        <?php foreach ([1, 2, 4, 6, 8, 10, 12] as $size): ?>
                            <button type="button" class="eatzy-guest-chip <?php echo $size === 2 ? 'active' : ''; ?>" 
                                    data-size="<?php echo $size; ?>" 
                                    onclick="EatzyBooking.setSize(<?php echo $size; ?>, this)">
                                <?php echo $size; ?> <?php echo $size === 1 ? 'Guest' : 'Guests'; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="book-party-size" value="2">
                </div>

                <!-- 2. Dining Date: Future Day & Week Selector (Month Excluded) -->
                <div class="eatzy-book-step">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                        <label class="eatzy-step-label" style="margin-bottom:0;">
                            <i class="fi fi-br-calendar" style="color:var(--eatzy-primary); margin-right:6px;"></i> 2. Choose Dining Day &amp; Week
                        </label>
                        
                        <!-- Week Selector Toggle -->
                        <div class="eatzy-week-toggle">
                            <button type="button" class="eatzy-week-btn active" id="btn-week-this" onclick="EatzyBooking.setWeek('this', this)">
                                <i class="fi fi-br-calendar-days" style="margin-right:4px;"></i> This Week
                            </button>
                            <button type="button" class="eatzy-week-btn" id="btn-week-next" onclick="EatzyBooking.setWeek('next', this)">
                                <i class="fi fi-br-forward" style="margin-right:4px;"></i> Next Week
                            </button>
                        </div>
                    </div>

                    <!-- This Week Days Track -->
                    <div id="eatzy-week-this" class="eatzy-days-track">
                        <?php foreach ($daysThisWeek as $idx => $d): ?>
                            <button type="button" 
                                    class="eatzy-day-card <?php echo $idx === 0 ? 'active' : ''; ?>" 
                                    data-date="<?php echo $d['date']; ?>" 
                                    onclick="EatzyBooking.setDate('<?php echo $d['date']; ?>', this, '<?php echo htmlspecialchars($d['label']); ?>')">
                                <span class="eatzy-day-badge"><?php echo $d['badge']; ?></span>
                                <span class="eatzy-day-number"><?php echo $d['dayNum']; ?></span>
                                <span class="eatzy-day-name"><?php echo $d['dayName']; ?></span>
                                <span class="eatzy-day-status"><span class="eatzy-dot"></span> Open</span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Next Week Days Track -->
                    <div id="eatzy-week-next" class="eatzy-days-track hidden" style="display:none;">
                        <?php foreach ($daysNextWeek as $idx => $d): ?>
                            <button type="button" 
                                    class="eatzy-day-card" 
                                    data-date="<?php echo $d['date']; ?>" 
                                    onclick="EatzyBooking.setDate('<?php echo $d['date']; ?>', this, '<?php echo htmlspecialchars($d['label']); ?>')">
                                <span class="eatzy-day-badge"><?php echo $d['badge']; ?></span>
                                <span class="eatzy-day-number"><?php echo $d['dayNum']; ?></span>
                                <span class="eatzy-day-name"><?php echo $d['dayName']; ?></span>
                                <span class="eatzy-day-status"><span class="eatzy-dot"></span> Open</span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <input type="hidden" id="book-date" value="<?php echo date('Y-m-d'); ?>">

                    <!-- Selected Day Summary Feedback -->
                    <div class="eatzy-selected-day-banner" id="eatzy-selected-date-text">
                        <i class="fi fi-br-check-circle" style="color:var(--eatzy-mint); margin-right:6px; font-size:14px;"></i>
                        <span>Selected Day: <strong>Today (<?php echo date('l'); ?>)</strong> &bull; Priority Table Slot Guaranteed</span>
                    </div>
                </div>

                <!-- 3. Time Slots -->
                <div class="eatzy-book-step">
                    <label class="eatzy-step-label">3. Time Slot</label>
                    
                    <div class="eatzy-slot-group">
                        <h4 class="eatzy-slot-heading"><i class="fi fi-br-sun" style="margin-right:6px; font-size:12px;"></i> Lunch Service (11:30 AM – 2:30 PM)</h4>
                        <div class="eatzy-slot-chips">
                            <?php foreach ($slots['Lunch'] as $slot): ?>
                                <button type="button" class="eatzy-slot-chip" data-slot="<?php echo $slot; ?>" onclick="EatzyBooking.setSlot('<?php echo $slot; ?>', this)">
                                    <?php echo $slot; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="eatzy-slot-group">
                        <h4 class="eatzy-slot-heading"><i class="fi fi-br-moon" style="margin-right:6px; font-size:12px;"></i> Dinner Service (5:30 PM – 10:00 PM)</h4>
                        <div class="eatzy-slot-chips">
                            <?php foreach ($slots['Dinner'] as $slot): ?>
                                <button type="button" class="eatzy-slot-chip <?php echo $slot === '7:00 PM' ? 'active' : ''; ?>" data-slot="<?php echo $slot; ?>" onclick="EatzyBooking.setSlot('<?php echo $slot; ?>', this)">
                                    <?php echo $slot; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <input type="hidden" id="book-time-slot" value="7:00 PM">
                </div>

                <!-- 4. Seating Zone -->
                <div class="eatzy-book-step">
                    <label class="eatzy-step-label">4. Seating Zone Preference</label>
                    <div class="eatzy-zone-cards">
                        <?php $zIdx = 0; foreach ($zones as $zoneName => $zoneDesc): $zIdx++; ?>
                            <div class="eatzy-zone-card <?php echo $zIdx === 1 ? 'active' : ''; ?>" 
                                 onclick="EatzyBooking.setZone('<?php echo htmlspecialchars($zoneName); ?>', this)">
                                <div class="eatzy-zone-icon"><?php echo $zIdx === 1 ? '<i class="fi fi-br-snowflake"></i>' : '<i class="fi fi-br-leaf"></i>'; ?></div>
                                <div class="eatzy-zone-info">
                                    <h4><?php echo htmlspecialchars($zoneName); ?></h4>
                                    <p><?php echo htmlspecialchars($zoneDesc); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="book-zone" value="Indoor Main Dining">
                </div>

                <!-- 5. Guest Information -->
                <div class="eatzy-book-step">
                    <label class="eatzy-step-label">5. Guest Contact Information</label>
                    <div class="eatzy-form-grid">
                        <div class="eatzy-form-group">
                            <label>Full Name *</label>
                            <input type="text" id="book-name" class="eatzy-input" placeholder="Enter your full name" required autocomplete="name">
                        </div>
                        <div class="eatzy-form-group">
                            <label>Mobile Number (for SMS Confirmation) *</label>
                            <input type="tel" id="book-phone" class="eatzy-input" placeholder="09XXXXXXXXX (11 digits)" maxlength="11" required autocomplete="tel">
                        </div>
                    </div>
                    <div class="eatzy-form-group" style="margin-top:12px;">
                        <label>Special Requests (Anniversary, Birthday, High Chair, etc.)</label>
                        <input type="text" id="book-notes" class="eatzy-input" placeholder="Optional notes for our host">
                    </div>
                </div>

                <div class="eatzy-book-footer">
                    <button type="submit" class="eatzy-btn eatzy-btn-primary eatzy-btn-large" id="eatzy-book-submit-btn">
                        <span>Confirm Table Booking</span> <i class="fi fi-br-arrow-right" style="margin-left:6px; font-size:12px;"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Success Confirmation Card -->
        <div class="eatzy-booking-success" id="eatzy-booking-success" style="display:none;">
            <div class="eatzy-success-badge"><i class="fi fi-br-check" style="margin-right:6px;"></i> RESERVATION CONFIRMED</div>
            <h2 class="eatzy-success-code" id="eatzy-res-code">EATZ-RES-XXXX</h2>
            <p class="eatzy-success-msg">A confirmation code has been generated and dispatched via automated SMS tracking.</p>
            
            <div class="eatzy-ticket-details">
                <div class="eatzy-ticket-row">
                    <span>Guest:</span>
                    <strong id="res-confirm-name">-</strong>
                </div>
                <div class="eatzy-ticket-row">
                    <span>Date &amp; Time:</span>
                    <strong id="res-confirm-time">-</strong>
                </div>
                <div class="eatzy-ticket-row">
                    <span>Party:</span>
                    <strong id="res-confirm-party">-</strong>
                </div>
                <div class="eatzy-ticket-row">
                    <span>Zone:</span>
                    <strong id="res-confirm-zone">-</strong>
                </div>
            </div>

            <div class="eatzy-success-actions">
                <a href="index.php?option=com_eatzy&view=menu" class="eatzy-btn eatzy-btn-primary">Browse Food Menu</a>
                <button type="button" class="eatzy-btn eatzy-btn-outline" onclick="location.reload()">Book Another Table</button>
            </div>
        </div>
    </div>
</div>

