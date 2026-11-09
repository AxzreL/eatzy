/**
 * EATZY - Table Booking & Tracker Controller
 */

const EatzyBooking = (function() {
    function init() {
        restoreGuestProfile();
    }

    function restoreGuestProfile() {
        try {
            const profile = JSON.parse(localStorage.getItem('eatzy_guest_profile') || '{}');
            const dinerProfile = JSON.parse(localStorage.getItem('eatzy_diner_profile') || '{}');
            const nameEl = document.getElementById('book-name');
            const phoneEl = document.getElementById('book-phone');

            const name = profile.name || dinerProfile.name || '';
            const phone = profile.phone || dinerProfile.phone || '';

            if (nameEl && !nameEl.value && name) nameEl.value = name;
            if (phoneEl && !phoneEl.value && phone) phoneEl.value = phone;

            [nameEl, phoneEl].forEach(el => {
                if (el) {
                    el.addEventListener('input', saveGuestProfile);
                }
            });
        } catch (e) {}
    }

    function saveGuestProfile() {
        try {
            const profile = {
                name: document.getElementById('book-name')?.value.trim() || '',
                phone: document.getElementById('book-phone')?.value.trim() || ''
            };
            localStorage.setItem('eatzy_guest_profile', JSON.stringify(profile));
        } catch (e) {}
    }

    function setSize(size, el) {
        document.getElementById('book-party-size').value = size;
        document.querySelectorAll('.eatzy-guest-chip').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');
    }

    function setSlot(slot, el) {
        document.getElementById('book-time-slot').value = slot;
        document.querySelectorAll('.eatzy-slot-chip').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');
    }

    function setZone(zone, el) {
        document.getElementById('book-zone').value = zone;
        document.querySelectorAll('.eatzy-zone-card').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');
    }

    function setWeek(week, btn) {
        const thisTrack = document.getElementById('eatzy-week-this');
        const nextTrack = document.getElementById('eatzy-week-next');
        const thisBtn = document.getElementById('btn-week-this');
        const nextBtn = document.getElementById('btn-week-next');

        if (week === 'next') {
            if (thisTrack) {
                thisTrack.classList.add('hidden');
                thisTrack.style.display = 'none';
            }
            if (nextTrack) {
                nextTrack.classList.remove('hidden');
                nextTrack.style.display = '';
            }
            if (thisBtn) thisBtn.classList.remove('active');
            if (nextBtn) nextBtn.classList.add('active');

            // Select first day of next week if none active
            const activeInNext = nextTrack?.querySelector('.eatzy-day-card.active');
            if (!activeInNext) {
                const firstCard = nextTrack?.querySelector('.eatzy-day-card');
                if (firstCard) firstCard.click();
            }
        } else {
            if (thisTrack) {
                thisTrack.classList.remove('hidden');
                thisTrack.style.display = '';
            }
            if (nextTrack) {
                nextTrack.classList.add('hidden');
                nextTrack.style.display = 'none';
            }
            if (thisBtn) thisBtn.classList.add('active');
            if (nextBtn) nextBtn.classList.remove('active');

            const activeInThis = thisTrack?.querySelector('.eatzy-day-card.active');
            if (!activeInThis) {
                const firstCard = thisTrack?.querySelector('.eatzy-day-card');
                if (firstCard) firstCard.click();
            }
        }
    }

    function setDate(dateStr, el, label) {
        const input = document.getElementById('book-date');
        if (input) input.value = dateStr;

        document.querySelectorAll('.eatzy-day-card').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');

        const banner = document.getElementById('eatzy-selected-date-text');
        if (banner) {
            banner.innerHTML = `<i class="fi fi-br-check-circle" style="color:var(--eatzy-mint); margin-right:6px; font-size:14px;"></i><span>Selected Day: <strong>${escapeHtml(label)}</strong> &bull; Priority Table Slot Guaranteed</span>`;
        }
    }

    function toggleLookup() {
        const card = document.getElementById('eatzy-res-lookup-card');
        if (!card) return;
        const isHidden = (card.style.display === 'none' || !card.style.display);
        card.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            const input = document.getElementById('res-lookup-input');
            if (input && !input.value) {
                const lastRes = localStorage.getItem('eatzy_last_res');
                if (lastRes) {
                    input.value = lastRes;
                } else {
                    const guest = JSON.parse(localStorage.getItem('eatzy_guest_profile') || '{}');
                    if (guest.phone) input.value = guest.phone;
                }
            }
            if (input) input.focus();
        }
    }

    function lookup(e) {
        if (e) e.preventDefault();
        const input = document.getElementById('res-lookup-input');
        const resultWrap = document.getElementById('res-lookup-result');
        if (!input || !resultWrap) return;

        const val = input.value.trim();
        if (!val) {
            alert('Please enter your reservation confirmation code or mobile phone number.');
            input.focus();
            return;
        }

        resultWrap.style.display = 'block';
        resultWrap.innerHTML = '<div style="color:#888ea0; font-size:13px; padding:10px 0;"><i class="fi fi-br-spinner fi-spin" style="margin-right:6px;"></i> Searching dining ledger...</div>';

            function renderFoundReservation(res) {
                resultWrap.innerHTML = `
                    <div class="eatzy-res-result-card">
                        <div class="eatzy-res-result-header">
                            <div>
                                <span class="eatzy-pill" style="font-size:10px; margin-right:6px;"><i class="fi fi-br-check" style="margin-right:3px;"></i> ${escapeHtml(res.status || 'Confirmed')}</span>
                                <strong style="color:#fff; font-size:15px; font-family:monospace;">${escapeHtml(res.reservation_code)}</strong>
                            </div>
                            <span style="font-size:12px; color:var(--eatzy-primary); font-weight:700;">${escapeHtml(res.party_size)} Guests</span>
                        </div>
                        <div style="font-size:13px; color:#e5e7eb; display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px;">
                            <div><span style="color:#888ea0; font-size:11px; display:block;">Guest Name</span><strong>${escapeHtml(res.guest_name)}</strong></div>
                            <div><span style="color:#888ea0; font-size:11px; display:block;">Contact Phone</span><strong>${escapeHtml(res.guest_phone)}</strong></div>
                            <div><span style="color:#888ea0; font-size:11px; display:block;">Date &amp; Time</span><strong>${escapeHtml(res.reservation_date)} &bull; ${escapeHtml(res.time_slot)}</strong></div>
                            <div><span style="color:#888ea0; font-size:11px; display:block;">Seating Zone</span><strong>${escapeHtml(res.seating_zone)}</strong></div>
                        </div>
                        ${res.special_notes ? `<div style="font-size:11px; color:#888ea0; margin-top:8px; border-top:1px dashed rgba(255,255,255,0.1); padding-top:6px;"><i class="fi fi-br-info" style="margin-right:4px;"></i> Note: ${escapeHtml(res.special_notes)}</div>` : ''}
                    </div>
                `;
            }

            function renderNotFound() {
                resultWrap.innerHTML = `
                    <div style="background:rgba(255, 69, 0, 0.1); border:1px solid rgba(255, 69, 0, 0.3); border-radius:10px; padding:14px; color:#fff; font-size:13px;">
                        <strong style="color:var(--eatzy-primary);"><i class="fi fi-br-cross-circle" style="margin-right:4px;"></i> No Reservation Found</strong>
                        <p style="margin:4px 0 0 0; color:#888ea0; font-size:12px;">We could not find an active reservation for "<strong>${escapeHtml(val)}</strong>". Please double check your code or book a new table below.</p>
                    </div>
                `;
            }

            fetch(`index.php?option=com_eatzy&task=getReservation&code=${encodeURIComponent(val)}&phone=${encodeURIComponent(val)}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (!data.success || !data.reservation) {
                        renderNotFound();
                        return;
                    }
                    renderFoundReservation(data.reservation);
                })
                .catch(err => {
                    // Client-side lookup fallback for static Vercel
                    try {
                        const myRes = JSON.parse(localStorage.getItem('eatzy_my_reservations') || '[]');
                        const cleanVal = val.toLowerCase().replace(/[^a-z0-9]/g, '');
                        const found = myRes.find(r => 
                            (r.reservation_code && r.reservation_code.toLowerCase().replace(/[^a-z0-9]/g, '') === cleanVal) ||
                            (r.guest_phone && r.guest_phone.replace(/[^0-9]/g, '') === val.replace(/[^0-9]/g, ''))
                        );
                        if (found) {
                            renderFoundReservation(found);
                        } else {
                            renderNotFound();
                        }
                    } catch (e) {
                        renderNotFound();
                    }
                });
    }

    function submit(e) {
        e.preventDefault();

        const name = document.getElementById('book-name').value.trim();
        const phone = document.getElementById('book-phone').value.trim();
        const cleanPhone = phone.replace(/[^0-9]/g, '');

        if (!name) {
            alert('Please enter your full name for the table reservation.');
            document.getElementById('book-name').focus();
            return;
        }

        if (cleanPhone.length < 10) {
            alert('Please enter a valid Philippine mobile number (e.g. 09171234567) so we can dispatch your confirmation SMS.');
            document.getElementById('book-phone').focus();
            return;
        }

        saveGuestProfile();

        const btn = document.getElementById('eatzy-book-submit-btn');
        btn.innerText = 'Checking Table Availability...';
        btn.disabled = true;

        const payload = {
            guest_name: name,
            guest_phone: phone,
            reservation_date: document.getElementById('book-date').value,
            time_slot: document.getElementById('book-time-slot').value,
            party_size: document.getElementById('book-party-size').value,
            seating_zone: document.getElementById('book-zone').value,
            special_notes: document.getElementById('book-notes').value.trim()
        };

        function completeTableBooking(data) {
            btn.innerText = 'Confirm Table Booking →';
            btn.disabled = false;

            try {
                const myRes = JSON.parse(localStorage.getItem('eatzy_my_reservations') || '[]');
                myRes.unshift(data);
                localStorage.setItem('eatzy_my_reservations', JSON.stringify(myRes.slice(0, 5)));
                localStorage.setItem('eatzy_last_res', data.reservation_code);
            } catch (err) {}

            // Hide form, show confirmation card
            document.getElementById('eatzy-booking-form-wrap').style.display = 'none';
            const successWrap = document.getElementById('eatzy-booking-success');
            document.getElementById('eatzy-res-code').innerText = data.reservation_code;
            document.getElementById('res-confirm-name').innerText = data.guest_name;
            document.getElementById('res-confirm-time').innerText = `${data.reservation_date} at ${data.time_slot}`;
            document.getElementById('res-confirm-party').innerText = `${data.party_size} Guests`;
            document.getElementById('res-confirm-zone').innerText = data.seating_zone;
            successWrap.style.display = 'block';

            // Trigger simulated SMS confirmation
            const toast = document.getElementById('eatzy-sms-toast');
            if (toast) {
                document.getElementById('eatzy-sms-body').innerText = `[SMART SMS] EATZY: Your reservation ${data.reservation_code} for ${data.party_size} guests on ${data.reservation_date} at ${data.time_slot} is CONFIRMED!`;
                toast.style.display = 'block';
                setTimeout(() => toast.style.display = 'none', 5000);
            }
        }

        fetch('index.php?option=com_eatzy&task=createBooking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (!data.success) {
                btn.innerText = 'Confirm Table Booking →';
                btn.disabled = false;
                alert(data.message || 'Booking conflict or server error. Please pick an alternative time slot.');
                return;
            }
            completeTableBooking(data);
        })
        .catch(err => {
            // Client-side booking fallback for static Vercel
            const resCode = 'EATZ-RES-' + Math.floor(1000 + Math.random() * 9000);
            const clientData = {
                success: true,
                reservation_code: resCode,
                guest_name: name,
                guest_phone: phone,
                reservation_date: document.getElementById('book-date').value,
                time_slot: document.getElementById('book-time-slot').value,
                party_size: document.getElementById('book-party-size').value,
                seating_zone: document.getElementById('book-zone').value,
                special_notes: document.getElementById('book-notes').value.trim(),
                status: 'Confirmed'
            };
            completeTableBooking(clientData);
        });
    }

    function escapeHtml(str) {
        return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    return { init, setSize, setSlot, setZone, setWeek, setDate, submit, toggleLookup, lookup };
})();

document.addEventListener('DOMContentLoaded', EatzyBooking.init);

const EatzyTracker = (function() {
    function claimPoints(btn) {
        if (typeof EatzyApp !== 'undefined' && EatzyApp.claimPoints) {
            EatzyApp.claimPoints(btn);
        } else {
            btn.innerHTML = '<span><i class="fi fi-br-check" style="margin-right:4px;"></i> 38 Points Saved!</span>';
            btn.style.background = 'rgba(255, 255, 255, 0.08)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            btn.style.color = '#fff';
            btn.disabled = true;
            alert('38 EATZY Loyalty Points added to your dining profile! You can redeem these on your next direct order.');
        }
    }

    return { claimPoints };
})();
