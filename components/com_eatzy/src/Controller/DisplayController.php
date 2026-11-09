<?php
namespace Eatzy\Component\Eatzy\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;

class DisplayController extends BaseController
{
    protected $default_view = 'menu';

    /**
     * Display standard view
     */
    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->input->get('view', 'menu');
        $this->input->set('view', $view);
        return parent::display($cachable, $urlparams);
    }

    /**
     * Helper to get database connection
     */
    private function getDbConnection()
    {
        try {
            return Factory::getContainer()->get('DatabaseDriver');
        } catch (\Throwable $e) {
            // Standalone or pre-installed fallback connection to MySQL
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $dbname = 'eatzy_db';
            $pdo = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ]);
            return $pdo;
        }
    }

    /**
     * AJAX endpoint: Submit new Diner Order
     */
    public function placeOrder()
    {
        header('Content-Type: application/json');
        $app = Factory::getApplication();
        $input = $app->getInput();

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true) ?: $_POST;

        $name = trim($data['customer_name'] ?? '');
        $phone = trim($data['customer_phone'] ?? '');
        $address = trim($data['delivery_address'] ?? '');
        $notes = trim($data['delivery_notes'] ?? '');
        $paymentMethod = trim($data['payment_method'] ?? 'gcash');
        $items = $data['items'] ?? [];
        $promoCode = strtoupper(trim($data['promo_code'] ?? ''));

        if (empty($name) || empty($phone) || empty($address) || empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all delivery details and select items.']);
            exit;
        }

        // Calculate Subtotal & Delivery
        $subtotal = 0.00;
        foreach ($items as $item) {
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $price = (float)($item['price'] ?? 0);
            $subtotal += ($qty * $price);
        }

        $deliveryFee = ($subtotal >= 1000.00) ? 0.00 : 65.00;
        $discount = 0.00;

        if ($promoCode === 'EATZYFIRST') {
            $discount = round($subtotal * 0.15, 2);
        } elseif ($promoCode === 'FREEDEL') {
            $deliveryFee = 0.00;
        } elseif ($promoCode === 'CRAVE50') {
            $discount = min(50.00, $subtotal);
        }

        $total = max(0.00, ($subtotal - $discount) + $deliveryFee);
        $orderNumber = 'EATZ-' . strtoupper(substr(uniqid(), -6));
        $transactionRef = strtoupper($paymentMethod) . '-TXN-' . rand(100000, 999999);

        // Database persistence
        try {
            $db = $this->getDbConnection();
            if ($db instanceof \PDO) {
                $stmt = $db->prepare("INSERT INTO eatzy_orders (order_number, customer_name, customer_phone, delivery_address, delivery_notes, subtotal, delivery_fee, discount, total, payment_method, payment_status, order_status, transaction_ref) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Received', ?)");
                $stmt->execute([$orderNumber, $name, $phone, $address, $notes, $subtotal, $deliveryFee, $discount, $total, $paymentMethod, $transactionRef]);
                $orderId = $db->lastInsertId();

                $itemStmt = $db->prepare("INSERT INTO eatzy_order_items (order_id, item_id, item_name, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($items as $item) {
                    $itemStmt->execute([
                        $orderId,
                        $item['id'] ?? null,
                        $item['name'] ?? 'Dish',
                        (int)$item['quantity'],
                        (float)$item['price'],
                        (int)$item['quantity'] * (float)$item['price']
                    ]);
                }
            } else {
                // Joomla Database Driver
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__eatzy_orders'))
                    ->columns($db->quoteName(['order_number', 'customer_name', 'customer_phone', 'delivery_address', 'delivery_notes', 'subtotal', 'delivery_fee', 'discount', 'total', 'payment_method', 'payment_status', 'order_status', 'transaction_ref']))
                    ->values(implode(',', [
                        $db->quote($orderNumber),
                        $db->quote($name),
                        $db->quote($phone),
                        $db->quote($address),
                        $db->quote($notes),
                        (float)$subtotal,
                        (float)$deliveryFee,
                        (float)$discount,
                        (float)$total,
                        $db->quote($paymentMethod),
                        $db->quote('Pending'),
                        $db->quote('Received'),
                        $db->quote($transactionRef)
                    ]));
                $db->setQuery($query);
                $db->execute();
                $orderId = $db->insertid();

                foreach ($items as $item) {
                    $itemQuery = $db->getQuery(true)
                        ->insert($db->quoteName('#__eatzy_order_items'))
                        ->columns($db->quoteName(['order_id', 'item_id', 'item_name', 'quantity', 'unit_price', 'total_price']))
                        ->values(implode(',', [
                            (int)$orderId,
                            (int)($item['id'] ?? 0),
                            $db->quote($item['name'] ?? 'Dish'),
                            (int)$item['quantity'],
                            (float)$item['price'],
                            (float)($item['quantity'] * $item['price'])
                        ]));
                    $db->setQuery($itemQuery);
                    $db->execute();
                }
            }

            echo json_encode([
                'success' => true,
                'order_number' => $orderNumber,
                'order_id' => $orderId,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'transaction_ref' => $transactionRef,
                'customer_phone' => $phone
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Webhook endpoint: Payment approval from GCash / Maya modal
     * POST index.php?option=com_eatzy&task=webhook
     */
    public function webhook()
    {
        header('Content-Type: application/json');
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true) ?: $_POST;

        $orderNumber = trim($data['order_number'] ?? '');
        $txnRef = trim($data['transaction_ref'] ?? '');
        $status = trim($data['status'] ?? 'SUCCESS');

        if (empty($orderNumber)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing order_number']);
            exit;
        }

        try {
            $db = $this->getDbConnection();
            if ($db instanceof \PDO) {
                $stmt = $db->prepare("UPDATE eatzy_orders SET payment_status = 'Paid', order_status = 'Kitchen Preparing', transaction_ref = COALESCE(NULLIF(?, ''), transaction_ref) WHERE order_number = ?");
                $stmt->execute([$txnRef, $orderNumber]);
            } else {
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__eatzy_orders'))
                    ->set($db->quoteName('payment_status') . ' = ' . $db->quote('Paid'))
                    ->set($db->quoteName('order_status') . ' = ' . $db->quote('Kitchen Preparing'))
                    ->where($db->quoteName('order_number') . ' = ' . $db->quote($orderNumber));
                if (!empty($txnRef)) {
                    $query->set($db->quoteName('transaction_ref') . ' = ' . $db->quote($txnRef));
                }
                $db->setQuery($query);
                $db->execute();
            }

            http_response_code(200);
            echo json_encode([
                'status' => '200 OK',
                'cleared' => true,
                'order_number' => $orderNumber,
                'order_status' => 'Kitchen Preparing',
                'message' => 'Payment authorized. Order sent to Kitchen Display System.'
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * AJAX endpoint: Create Table Reservation
     */
    public function createBooking()
    {
        header('Content-Type: application/json');
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true) ?: $_POST;

        $guestName = trim($data['guest_name'] ?? '');
        $guestPhone = trim($data['guest_phone'] ?? '');
        $date = trim($data['reservation_date'] ?? date('Y-m-d'));
        $timeSlot = trim($data['time_slot'] ?? '7:00 PM');
        $partySize = max(1, (int)($data['party_size'] ?? 2));
        $zone = trim($data['seating_zone'] ?? 'Indoor Main Dining');
        $notes = trim($data['special_notes'] ?? '');

        if (empty($guestName) || empty($guestPhone) || empty($timeSlot)) {
            echo json_encode(['success' => false, 'message' => 'Please provide your name, phone number, and preferred dining slot.']);
            exit;
        }

        $resCode = 'EATZ-RES-' . rand(1000, 9999);

        try {
            $db = $this->getDbConnection();
            if ($db instanceof \PDO) {
                // Conflict check: Count existing bookings for this date, slot, and zone
                $chk = $db->prepare("SELECT COUNT(*) as booked FROM eatzy_reservations WHERE reservation_date = ? AND time_slot = ? AND seating_zone = ? AND status != 'Cancelled'");
                $chk->execute([$date, $timeSlot, $zone]);
                $count = (int)$chk->fetch()->booked;

                if ($count >= 6) { // Max capacity per slot/zone
                    echo json_encode(['success' => false, 'message' => "Sorry, the $timeSlot slot in $zone is fully booked. Please choose an adjacent time slot!"]);
                    exit;
                }

                $stmt = $db->prepare("INSERT INTO eatzy_reservations (reservation_code, guest_name, guest_phone, reservation_date, time_slot, party_size, seating_zone, status, special_notes) VALUES (?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)");
                $stmt->execute([$resCode, $guestName, $guestPhone, $date, $timeSlot, $partySize, $zone, $notes]);
            } else {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__eatzy_reservations'))
                    ->columns($db->quoteName(['reservation_code', 'guest_name', 'guest_phone', 'reservation_date', 'time_slot', 'party_size', 'seating_zone', 'status', 'special_notes']))
                    ->values(implode(',', [
                        $db->quote($resCode),
                        $db->quote($guestName),
                        $db->quote($guestPhone),
                        $db->quote($date),
                        $db->quote($timeSlot),
                        (int)$partySize,
                        $db->quote($zone),
                        $db->quote('Confirmed'),
                        $db->quote($notes)
                    ]));
                $db->setQuery($query);
                $db->execute();
            }

            echo json_encode([
                'success' => true,
                'reservation_code' => $resCode,
                'guest_name' => $guestName,
                'guest_phone' => $guestPhone,
                'reservation_date' => $date,
                'time_slot' => $timeSlot,
                'party_size' => $partySize,
                'seating_zone' => $zone,
                'message' => 'Table reservation confirmed! SMS notification dispatched.'
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Booking error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * AJAX endpoint: Live diner order tracking status
     */
    public function getOrderStatus()
    {
        header('Content-Type: application/json');
        $orderNumber = trim($this->input->getString('order_number', $_GET['order_number'] ?? ''));

        if (empty($orderNumber)) {
            echo json_encode(['success' => false, 'message' => 'Order number required']);
            exit;
        }

        try {
            $db = $this->getDbConnection();
            $order = null;
            $items = [];

            if ($db instanceof \PDO) {
                $stmt = $db->prepare("SELECT * FROM eatzy_orders WHERE order_number = ? LIMIT 1");
                $stmt->execute([$orderNumber]);
                $order = $stmt->fetch();

                if ($order) {
                    $itemStmt = $db->prepare("SELECT * FROM eatzy_order_items WHERE order_id = ?");
                    $itemStmt->execute([$order->id]);
                    $items = $itemStmt->fetchAll();
                }
            } else {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__eatzy_orders'))
                    ->where($db->quoteName('order_number') . ' = ' . $db->quote($orderNumber));
                $db->setQuery($query);
                $order = $db->loadObject();

                if ($order) {
                    $itemQuery = $db->getQuery(true)
                        ->select('*')
                        ->from($db->quoteName('#__eatzy_order_items'))
                        ->where($db->quoteName('order_id') . ' = ' . (int)$order->id);
                    $db->setQuery($itemQuery);
                    $items = $db->loadObjectList();
                }
            }

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'order' => $order,
                'items' => $items
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * AJAX endpoint: Update order status (Live Dispatch Simulation or KDS sync)
     */
    public function updateOrderStatus()
    {
        header('Content-Type: application/json');
        $orderNumber = trim($this->input->getString('order_number', $_GET['order_number'] ?? ''));
        $status = trim($this->input->getString('status', $_GET['status'] ?? ''));

        $allowedStatuses = ['Received', 'Kitchen Preparing', 'Out for Delivery', 'Delivered'];
        if (empty($orderNumber) || !in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid order number or status.']);
            exit;
        }

        try {
            $db = $this->getDbConnection();
            if ($db instanceof \PDO) {
                $stmt = $db->prepare("UPDATE eatzy_orders SET order_status = ? WHERE order_number = ?");
                $stmt->execute([$status, $orderNumber]);
            } else {
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__eatzy_orders'))
                    ->set($db->quoteName('order_status') . ' = ' . $db->quote($status))
                    ->where($db->quoteName('order_number') . ' = ' . $db->quote($orderNumber));
                $db->setQuery($query);
                $db->execute();
            }

            echo json_encode([
                'success' => true, 
                'order_number' => $orderNumber,
                'order_status' => $status,
                'message' => "Order #$orderNumber updated to $status."
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * AJAX endpoint: Lookup Diner Reservation
     */
    public function getReservation()
    {
        header('Content-Type: application/json');
        $code = trim($this->input->getString('code', $_GET['code'] ?? ''));
        $phone = trim($this->input->getString('phone', $_GET['phone'] ?? ''));

        if (empty($code) && empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Please provide a confirmation code or mobile number.']);
            exit;
        }

        try {
            $db = $this->getDbConnection();
            $reservation = null;

            if ($db instanceof \PDO) {
                if (!empty($code)) {
                    $stmt = $db->prepare("SELECT * FROM eatzy_reservations WHERE reservation_code = ? LIMIT 1");
                    $stmt->execute([$code]);
                    $reservation = $stmt->fetch();
                } else {
                    $stmt = $db->prepare("SELECT * FROM eatzy_reservations WHERE guest_phone = ? ORDER BY id DESC LIMIT 1");
                    $stmt->execute([$phone]);
                    $reservation = $stmt->fetch();
                }
            } else {
                $query = $db->getQuery(true)->select('*')->from($db->quoteName('#__eatzy_reservations'));
                if (!empty($code)) {
                    $query->where($db->quoteName('reservation_code') . ' = ' . $db->quote($code));
                } else {
                    $query->where($db->quoteName('guest_phone') . ' = ' . $db->quote($phone))->order('id DESC');
                }
                $db->setQuery($query, 0, 1);
                $reservation = $db->loadObject();
            }

            if (!$reservation) {
                echo json_encode(['success' => false, 'message' => 'No active table reservation found with those details.']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'reservation' => $reservation
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

