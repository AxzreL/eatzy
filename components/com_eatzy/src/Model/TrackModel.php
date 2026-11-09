<?php
namespace Eatzy\Component\Eatzy\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class TrackModel extends BaseDatabaseModel
{
    public function getOrder($orderNumber)
    {
        $orderNumber = trim($orderNumber ?? '');

        try {
            $db = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__eatzy_orders'));

            if (!empty($orderNumber)) {
                $query->where($db->quoteName('order_number') . ' = ' . $db->quote($orderNumber) . ' OR ' . $db->quoteName('customer_phone') . ' = ' . $db->quote($orderNumber));
            }

            $query->order($db->quoteName('id') . ' DESC');
            $db->setQuery($query, 0, 1);
            $order = $db->loadObject();

            if ($order) {
                $itemQuery = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__eatzy_order_items'))
                    ->where($db->quoteName('order_id') . ' = ' . (int)$order->id);
                $db->setQuery($itemQuery);
                $order->items = $db->loadObjectList();
                return $order;
            }
        } catch (\Throwable $e) {}

        // Fallback PDO in case Joomla DB driver fails
        try {
            $pdo = new \PDO("mysql:host=localhost;dbname=joomla_db;charset=utf8mb4", "root", "", [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ]);

            if (!empty($orderNumber)) {
                $stmt = $pdo->prepare("SELECT * FROM kh66p_eatzy_orders WHERE order_number = ? OR customer_phone = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$orderNumber, $orderNumber]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM kh66p_eatzy_orders ORDER BY id DESC LIMIT 1");
                $stmt->execute();
            }

            $order = $stmt->fetch();
            if ($order) {
                $itemStmt = $pdo->prepare("SELECT * FROM kh66p_eatzy_order_items WHERE order_id = ?");
                $itemStmt->execute([$order->id]);
                $order->items = $itemStmt->fetchAll();
                return $order;
            }
        } catch (\Throwable $ex) {}

        // Return null if no real order exists (never return fake static data)
        return null;
    }
}

