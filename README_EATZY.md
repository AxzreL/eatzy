# EATZY: Fast-Casual Food Tech Platform for Joomla 6

Welcome to **EATZY** (`eatzy.ph`) — an appetizing, high-converting digital ordering and table reservation platform built natively on **Joomla 6.2.0** in XAMPP.

---

## 🚀 Quick Setup & Installation Guide

### Step 1: Start XAMPP
Ensure **Apache** and **MySQL** are running in your **XAMPP Control Panel**.

### Step 2: Import the Database
1. Open phpMyAdmin at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click **Import** and select the seed file:
   `C:\xampp\htdocs\EATZY\eatzy_db_seed.sql`
3. Click **Go**. This creates the `eatzy_db` database pre-loaded with signature dishes, categories, and physical dining tables.

### Step 3: Complete Joomla Web Setup Wizard
1. Open your browser and navigate to:
   [http://localhost/EATZY/](http://localhost/EATZY/)
2. Follow the on-screen Joomla setup prompts:
   * **Language**: English
   * **Site Name**: `EATZY Philippines`
   * **Super User Account**: Set your desired administrator username, email, and password.
   * **Database Configuration**:
     * Host: `localhost`
     * Database Username: `root`
     * Database Password: *(Leave empty)*
     * Database Name: `eatzy_db`
     * Table Prefix: *(Default e.g. `eatz_` or `jos_`)*
3. Once installation completes, click **Remove installation folder** in the installer.

### Step 4: Component & Template Activation
The extensions are already pre-wired into your Joomla directory structure. If you wish to install them cleanly via Joomla Administrator:
1. Log into your Joomla Admin: [http://localhost/EATZY/administrator](http://localhost/EATZY/administrator)
2. Go to **System &rarr; Extensions &rarr; Install**.
3. You can upload the packages from:
   * `C:\xampp\htdocs\EATZY\extensions\com_eatzy.zip`
   * `C:\xampp\htdocs\EATZY\extensions\tpl_eatzy.zip`
4. Set the **eatzy** template as Default under **System &rarr; Site Templates**.

---

## 🍔 Feature Navigation & Direct Links

| Feature | URL | Description |
| :--- | :--- | :--- |
| **Food Menu & Ordering** | `http://localhost/EATZY/index.php?option=com_eatzy&view=menu` | Categorized dishes, dietary filters, instant add-to-cart, slide-out express cart drawer. |
| **Table Booking Engine** | `http://localhost/EATZY/index.php?option=com_eatzy&view=book` | Real-time slot engine, party sizing (1–12+), Indoor Dining vs. Patio zones, conflict verification. |
| **Live Diner Tracker** | `http://localhost/EATZY/index.php?option=com_eatzy&view=track&id=EATZ-8921` | Visual progress timeline, estimated delivery countdown, and 38 loyalty points claim. |
| **Kitchen Display (KDS)** | `http://localhost/EATZY/administrator/index.php?option=com_eatzy&view=kds` | Live ticket Kanban board for staff to move orders through cooking and dispatch lanes. |
| **Table Manager** | `http://localhost/EATZY/administrator/index.php?option=com_eatzy&view=reservations` | Staff dashboard to view reservations, seat diners, and manage floor capacity. |
| **Menu Manager** | `http://localhost/EATZY/administrator/index.php?option=com_eatzy&view=menu` | Admin catalog manager to toggle dish availability and mark "Today's Specials". |
| **Payment Webhook** | `http://localhost/EATZY/index.php?option=com_eatzy&task=webhook` | `POST` webhook endpoint for instant e-wallet clearance. |

---

## 💳 Checkout & Demo Test Credentials

* **Promo Codes**:
  * `EATZYFIRST` — 15% discount on order subtotal.
  * `FREEDEL` — Waives the ₱65.00 delivery fee.
  * `CRAVE50` — ₱50.00 direct credit.
* **Free Delivery**: Automatically unlocked for all orders with subtotal &ge; ₱1,000.00.
* **GCash / Maya Simulation**: During checkout, clicking "Proceed to Pay" opens an authentic e-wallet payment modal with a 1-click **Authorize &amp; Pay Now** button that fires the live webhook.

---
*Created for EATZY Philippines — 100% Commission-Free Direct Food Ordering.*

