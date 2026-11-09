# EATZY 🍔 — Michelin Street-Tech Fast-Casual Food Tech

> **CRAVE. CLICK. CHOW.**  
> Eliminating third-party aggregator markups with chef-driven fast-casual dining, direct lightning delivery, and friction-free table reservations.

[![Deploy with Vercel](https://vercel.com/button)](https://vercel.com/new/clone?repository-url=https://github.com/AxzreL/eatzy)

---

## 🌟 Overview

**EATZY** is an ultra-modern cyber-bistro application designed for fast-casual food dining in Metro Manila. It provides diners with a direct ordering portal, instant table reservations, fintech payment clearance, and live kitchen dispatch tracking without predatory aggregator commission fees.

This repository is built as a **Hybrid Architecture**:
1. **Vercel-Ready Web Application**: Ready to host instantly on Vercel with zero configuration, lightning-fast edge CDN speeds, and full client-side simulated clearance.
2. **Native Joomla 5/6 Component & Template**: Includes full production extensions (`com_eatzy.zip` and `tpl_eatzy.zip`) and SQL database dump for enterprise CMS hosting.

---

## 🚀 Key Features

- **🍔 Interactive Food Catalog**: 13 signature dishes across Mains, Starters, Drinks, and Desserts with real high-resolution food photography.
- **⚡ Adaptive Category Filters**: Fully responsive wrapping category chips that never get cut off on mobile or desktop screens.
- **🥡 Express Cart & Dynamic Steppers**: Slide-out cart drawer with real-time delivery tier calculation and one-click promo chips (`EATZYFIRST`, `FREEDEL`, `CRAVE50`).
- **📱 Fintech GCash & Maya Modal**: 256-bit encrypted checkout requiring MPIN clearance with automated carrier SMS alerts.
- **📅 Future Day & Week Table Reservations**: Select seating by future day (`Today`, `Tomorrow`, `Wed`, `Thu`, etc.) or future week (`This Week` / `Next Week`) without confusing month pickers.
- **🔍 "Find My Reservation" Portal**: Look up active table bookings anytime using confirmation codes (e.g. `EATZ-RES-XXXX`) or phone numbers.
- **🛵 Live Milestone Dispatch Tracker**: Real-time 4-stage tracking timeline (`Received` → `Kitchen Prep` → `Out for Delivery` → `Delivered`) with an interactive kitchen simulation controller.
- **📲 Mobile Thumb-Zone Navigation**: Fixed bottom navigation bar and mobile cart badge designed for single-hand mobile operation.

---

## ⚡ 1-Click Hosting on Vercel

1. Log in to your [Vercel Dashboard](https://vercel.com).
2. Click **Add New...** → **Project**.
3. Select this repository: `AxzreL/eatzy`.
4. Keep the default settings (Framework Preset: **Other**, Root Directory: `./`).
5. Click **Deploy**!

Your live EATZY site will be deployed and available in seconds with a free `.vercel.app` domain and automatic SSL!

---

## 🛠️ Full Joomla 5/6 CMS Setup (Self-Hosted / XAMPP)

If you wish to host EATZY on a standard PHP/MySQL web server:

1. Clone this repository into your web server document root (e.g., `C:\xampp\htdocs\EATZY`).
2. Import `eatzy_db_seed.sql` into your MySQL database (`joomla_db`).
3. Copy `configuration.sample.php` to `configuration.php` and configure your database credentials.
4. Both extensions are pre-compiled and ready in the [`extensions/`](extensions/) directory:
   - Component: `extensions/com_eatzy.zip`
   - Template: `extensions/tpl_eatzy.zip`

---

## 📁 Repository Structure

```
EATZY/
├── index.html              # Vercel entrypoint: Food Menu, Cart & Checkout
├── book.html               # Table Reservations with Day & Week Selector
├── track.html              # Live Kitchen Tracker & Milestone Simulation
├── vercel.json             # Vercel clean URL and rewrite configuration
├── components/com_eatzy/   # Frontend Joomla MVC component & assets
├── administrator/          # Joomla admin backend (KDS, Dish Manager)
├── templates/eatzy/        # Cyber-bistro Joomla 6 template
├── media/com_eatzy/        # Compiled CSS & JS assets
├── images/foods/           # Local photography for dishes
├── extensions/             # Pre-built installable Joomla ZIP packages
│   ├── com_eatzy.zip
│   └── tpl_eatzy.zip
├── eatzy_db_seed.sql       # Complete database schema & dishes seed
└── configuration.sample.php # Sample database config
```

---

## 📄 License

MIT License. Designed and engineered for EATZY Philippines.
