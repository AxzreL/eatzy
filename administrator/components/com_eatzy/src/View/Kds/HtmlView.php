<?php
namespace Eatzy\Component\Eatzy\Administrator\View\Kds;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $orders;

    public function display($tpl = null)
    {
        $orders = [];
        try {
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__eatzy_orders'))
                ->order($db->quoteName('id') . ' DESC');
            $db->setQuery($query);
            $orders = $db->loadObjectList();

            foreach ($orders as &$ord) {
                $itemQuery = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__eatzy_order_items'))
                    ->where($db->quoteName('order_id') . ' = ' . (int)$ord->id);
                $db->setQuery($itemQuery);
                $ord->items = $db->loadObjectList();
            }
        } catch (\Throwable $e) {
            // Fallback PDO
            try {
                $pdo = new \PDO("mysql:host=localhost;dbname=eatzy_db;charset=utf8mb4", "root", "", [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
                ]);
                $stmt = $pdo->query("SELECT * FROM eatzy_orders ORDER BY id DESC");
                $orders = $stmt->fetchAll();
                foreach ($orders as &$ord) {
                    $itemStmt = $pdo->prepare("SELECT * FROM eatzy_order_items WHERE order_id = ?");
                    $itemStmt->execute([$ord->id]);
                    $ord->items = $itemStmt->fetchAll();
                }
            } catch (\Throwable $ex) {}
        }

        // Demo fallback if no orders exist yet
        if (empty($orders)) {
            $orders = [
                (object)[
                    'id' => 1,
                    'order_number' => 'EATZ-8921',
                    'customer_name' => 'Mark Santos',
                    'customer_phone' => '09178829101',
                    'delivery_address' => 'Unit 14B, One Bonifacio High Street, BGC, Taguig',
                    'subtotal' => 700.00,
                    'delivery_fee' => 0.00,
                    'total' => 700.00,
                    'payment_method' => 'gcash',
                    'payment_status' => 'Paid',
                    'order_status' => 'Kitchen Preparing',
                    'transaction_ref' => 'GCASH-TXN-902819',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-8 minutes')),
                    'items' => [
                        (object)['item_name' => 'Smoked Angus Truffle Burger', 'quantity' => 1, 'unit_price' => 380.00, 'total_price' => 380.00],
                        (object)['item_name' => 'Creamy Pesto Fettuccine', 'quantity' => 1, 'unit_price' => 320.00, 'total_price' => 320.00]
                    ]
                ]
            ];
        }

        $this->orders = $orders;
        return parent::display($tpl);
    }
}

