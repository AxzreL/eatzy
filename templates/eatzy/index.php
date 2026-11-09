<?php
/**
 * EATZY Ultra-Modern Cyber-Bistro Template for Joomla 6
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
$app = Factory::getApplication();
$currentView = $app->getInput()->get('view', 'menu');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>EATZY — Crave. Click. Chow. | Michelin Street-Tech Fast-Casual</title>
    <meta name="description" content="Direct-to-diner fast-casual ordering and instant table reservations in Metro Manila. GCash & Maya supported.">
    
    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Flaticon UIcons (Bold Rounded & All Styles) -->
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons@3.3.1/css/all/all.min.css">

    <!-- EATZY CSS -->
    <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/components/com_eatzy/assets/css/eatzy.css?v=2.6.1">

    <style>
        /* Sticky Cyber Navigation */
        .eatzy-site-nav {
            position: sticky;
            top: 0;
            background: rgba(10, 11, 16, 0.88);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 800;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .eatzy-nav-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .eatzy-brand-logo {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.8px;
            color: #fff;
            text-decoration: none;
        }

        .eatzy-brand-logo span {
            color: var(--eatzy-primary);
        }

        .eatzy-brand-badge {
            font-size: 9px;
            background: rgba(255, 255, 255, 0.08);
            color: #d0d4e0;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-left: 6px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .eatzy-nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .eatzy-nav-link {
            color: var(--eatzy-text-secondary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .eatzy-nav-link:hover, .eatzy-nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .eatzy-nav-cart-btn {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 8px 16px;
            border-radius: var(--eatzy-radius-pill);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .eatzy-nav-cart-btn:hover {
            background: var(--eatzy-primary-gradient);
            border-color: var(--eatzy-primary);
            box-shadow: 0 4px 18px rgba(255, 69, 0, 0.4);
            transform: translateY(-1px);
        }

        .eatzy-cart-counter {
            background: var(--eatzy-primary);
            color: #fff;
            font-size: 11px;
            font-weight: 900;
            padding: 2px 7px;
            border-radius: var(--eatzy-radius-pill);
        }

        .eatzy-main-content {
            flex-grow: 1;
        }

        /* Mobile Top Header Bag Button */
        .eatzy-mobile-top-cart-btn {
            display: none;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 7px 12px;
            border-radius: var(--eatzy-radius-pill);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .eatzy-mobile-top-cart-btn:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Mobile Bottom Thumb-Zone Navigation Bar */
        .eatzy-mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(10, 11, 16, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.6);
            padding: 6px 12px calc(6px + env(safe-area-inset-bottom, 0px)) 12px;
            justify-content: space-around;
            align-items: center;
        }

        .eatzy-bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            background: none;
            border: none;
            color: #888ea0;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 10px;
            min-width: 58px;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .eatzy-bottom-nav-item i {
            font-size: 18px;
            line-height: 1;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .eatzy-bottom-nav-item.active {
            color: #ffffff;
        }

        .eatzy-bottom-nav-item.active i {
            color: var(--eatzy-primary);
            transform: translateY(-1px);
        }

        .eatzy-bottom-nav-item:active {
            transform: scale(0.92);
        }

        .eatzy-bottom-cart-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .eatzy-bottom-cart-wrap .eatzy-cart-counter {
            position: absolute;
            top: -6px;
            right: -10px;
            font-size: 9px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
        }

        /* Luxury Footer */
        .eatzy-site-footer {
            background: #090a0e;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            padding: 50px 20px 30px 20px;
            margin-top: 80px;
            font-size: 13px;
            color: #7b8092;
            position: relative;
            z-index: 2;
        }

        .eatzy-footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 36px;
            margin-bottom: 36px;
        }

        .eatzy-footer-col h4 {
            color: #fff;
            margin: 0 0 16px 0;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .eatzy-footer-col p {
            margin: 0 0 8px 0;
            line-height: 1.6;
        }

        .eatzy-footer-bottom {
            max-width: 1240px;
            margin: 0 auto;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            font-size: 12px;
        }

        /* Responsive Breakpoints for Mobile Top Nav and Bottom Navigation */
        @media (max-width: 768px) {
            body {
                padding-bottom: calc(68px + env(safe-area-inset-bottom, 0px));
            }

            .eatzy-nav-links {
                display: none !important;
            }

            .eatzy-mobile-top-cart-btn {
                display: inline-flex !important;
            }

            .eatzy-mobile-bottom-nav {
                display: flex !important;
            }

            .eatzy-site-nav {
                padding: 10px 14px;
            }

            .eatzy-brand-logo {
                font-size: 18px;
            }

            .eatzy-brand-badge {
                font-size: 8px;
                padding: 2px 5px;
            }

            .eatzy-site-footer {
                padding: 36px 16px calc(40px + env(safe-area-inset-bottom, 0px)) 16px;
                margin-top: 40px;
            }

            .eatzy-footer-inner {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .eatzy-footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Sticky Navigation (Section 05 Mockup: [EATZY LOGO] [MENU] [CART]) -->
    <nav class="eatzy-site-nav">
        <div class="eatzy-nav-inner">
            <a href="index.php?option=com_eatzy&view=menu" class="eatzy-brand-logo">
                <i class="fi fi-br-utensils" style="font-size:18px; margin-right:4px; color:var(--eatzy-primary);"></i>
                EATZY<span>.PH</span>
                <span class="eatzy-brand-badge">BISTRO TECH</span>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="eatzy-nav-links">
                <a href="index.php?option=com_eatzy&view=menu" class="eatzy-nav-link <?php echo $currentView === 'menu' ? 'active' : ''; ?>">
                    <i class="fi fi-br-hamburger"></i> <span>Menu</span>
                </a>
                <a href="index.php?option=com_eatzy&view=book" class="eatzy-nav-link <?php echo $currentView === 'book' ? 'active' : ''; ?>">
                    <i class="fi fi-br-calendar"></i> <span>Book Table</span>
                </a>
                <a href="index.php?option=com_eatzy&view=track" class="eatzy-nav-link <?php echo $currentView === 'track' ? 'active' : ''; ?>">
                    <i class="fi fi-br-moped"></i> <span>Track Order</span>
                </a>
                <a href="administrator/index.php?option=com_eatzy&view=kds" class="eatzy-nav-link" target="_blank">
                    <i class="fi fi-br-hat-chef"></i> <span>Kitchen Display</span>
                </a>
                <button class="eatzy-nav-cart-btn" onclick="EatzyApp.toggleCart(true)" aria-label="Open Cart">
                    <i class="fi fi-br-shopping-bag"></i>
                    <span id="eatzy-nav-cart-badge" class="eatzy-cart-counter">0</span>
                </button>
            </div>

            <!-- Mobile Top Header Quick Bag -->
            <button class="eatzy-mobile-top-cart-btn" onclick="EatzyApp.toggleCart(true)" aria-label="Open Cart">
                <i class="fi fi-br-shopping-bag"></i>
                <span id="eatzy-mobile-top-cart-badge" class="eatzy-cart-counter">0</span>
            </button>
        </div>
    </nav>

    <!-- Main Component Render -->
    <main class="eatzy-main-content">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
    </main>

    <!-- Global Footer -->
    <footer class="eatzy-site-footer">
        <div class="eatzy-footer-inner">
            <div class="eatzy-footer-col">
                <h4 style="color:var(--eatzy-primary);">EATZY PHILIPPINES</h4>
                <p>An ultra-modern fast-casual food tech platform engineered for direct delivery ordering, instant guest checkout, and frictionless table bookings.</p>
                <p style="color:#d0d4e0; font-weight:700;"><i class="fi fi-br-badge-check" style="margin-right:6px; color:var(--eatzy-mint);"></i>100% Zero Aggregator Markups</p>
            </div>
            <div class="eatzy-footer-col">
                <h4>Hours of Service</h4>
                <p><i class="fi fi-br-clock" style="margin-right:6px; font-size:12px;"></i>Lunch Service: 11:30 AM – 2:30 PM</p>
                <p><i class="fi fi-br-clock" style="margin-right:6px; font-size:12px;"></i>Dinner Service: 5:30 PM – 10:00 PM</p>
                <p><i class="fi fi-br-moped" style="margin-right:6px; font-size:12px;"></i>Express Delivery: Daily 11:00 AM – 9:30 PM</p>
            </div>
            <div class="eatzy-footer-col">
                <h4>BGC Flagship Location</h4>
                <p><i class="fi fi-br-marker" style="margin-right:6px; font-size:12px;"></i>High Street South Block, 26th Street,</p>
                <p>Bonifacio Global City, Taguig, Metro Manila</p>
                <p><i class="fi fi-br-phone-call" style="margin-right:6px; font-size:12px;"></i>Direct Hotlines: (02) 8829-EATZ &bull; 0917-882-9101</p>
            </div>
            <div class="eatzy-footer-col">
                <h4>Financial Security &amp; Webhooks</h4>
                <p><i class="fi fi-br-shield-check" style="margin-right:6px; font-size:12px;"></i>256-bit TLS End-to-End Encryption</p>
                <p><i class="fi fi-br-check" style="margin-right:6px; font-size:12px;"></i>GCash Direct Webhook Clearance</p>
                <p><i class="fi fi-br-check" style="margin-right:6px; font-size:12px;"></i>Maya e-Wallet &amp; Card Processing</p>
                <p>Domain: <code style="color:var(--eatzy-primary);">eatzy.ph</code></p>
            </div>
        </div>
        <div class="eatzy-footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> EATZY Philippines Inc. Built natively on Joomla 6.</span>
            <span>Michelin Street-Tech UX &bull; WCAG 2.1 AA Compliant &bull; Ultra-Fast PWA</span>
        </div>
    </footer>

    <!-- Mobile Bottom Thumb-Zone Navigation Bar -->
    <nav class="eatzy-mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="index.php?option=com_eatzy&view=menu" class="eatzy-bottom-nav-item <?php echo $currentView === 'menu' ? 'active' : ''; ?>">
            <i class="fi fi-br-hamburger"></i>
            <span>Menu</span>
        </a>
        <a href="index.php?option=com_eatzy&view=book" class="eatzy-bottom-nav-item <?php echo $currentView === 'book' ? 'active' : ''; ?>">
            <i class="fi fi-br-calendar"></i>
            <span>Book</span>
        </a>
        <a href="index.php?option=com_eatzy&view=track" class="eatzy-bottom-nav-item <?php echo $currentView === 'track' ? 'active' : ''; ?>">
            <i class="fi fi-br-moped"></i>
            <span>Track</span>
        </a>
        <button type="button" class="eatzy-bottom-nav-item" onclick="EatzyApp.toggleCart(true)">
            <div class="eatzy-bottom-cart-wrap">
                <i class="fi fi-br-shopping-bag"></i>
                <span id="eatzy-bottom-cart-badge" class="eatzy-cart-counter">0</span>
            </div>
            <span>Bag</span>
        </button>
    </nav>

    <!-- Dynamic Island Carrier SMS Notification (Global) -->
    <div class="eatzy-sms-toast" id="eatzy-sms-toast" style="display:none;">
        <div class="eatzy-sms-header">
            <span>GLOBE TELECOM &bull; INSTANT DISPATCH</span>
            <span>Just Now</span>
        </div>
        <p class="eatzy-sms-body" id="eatzy-sms-body">
            EATZY: Order confirmed and kitchen notified.
        </p>
    </div>

    <!-- Client-side Scripts -->
    <script src="<?php echo $this->baseurl; ?>/components/com_eatzy/assets/js/eatzy.js?v=2.6.0"></script>
    <script src="<?php echo $this->baseurl; ?>/components/com_eatzy/assets/js/booking.js?v=2.6.0"></script>
</body>
</html>
