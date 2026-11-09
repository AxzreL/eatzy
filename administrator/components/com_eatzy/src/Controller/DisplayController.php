<?php
namespace Eatzy\Component\Eatzy\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

class DisplayController extends BaseController
{
    protected $default_view = 'kds';

    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->input->get('view', 'kds');
        $this->input->set('view', $view);
        return parent::display($cachable, $urlparams);
    }

    private function getDb()
    {
        try {
            return Factory::getContainer()->get('DatabaseDriver');
        } catch (\Throwable $e) {
            return new \PDO("mysql:host=localhost;dbname=eatzy_db;charset=utf8mb4", "root", "", [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ]);
        }
    }

    /**
     * Advance order status in Kitchen Display System
     */
    public function updateOrderStatus()
    {
        $app = Factory::getApplication();
        $id = (int)$this->input->get('id', 0);
        $status = trim($this->input->get('status', ''));

        if ($id > 0 && !empty($status)) {
            try {
                $db = $this->getDb();
                if ($db instanceof \PDO) {
                    $stmt = $db->prepare("UPDATE eatzy_orders SET order_status = ? WHERE id = ?");
                    $stmt->execute([$status, $id]);
                } else {
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__eatzy_orders'))
                        ->set($db->quoteName('order_status') . ' = ' . $db->quote($status))
                        ->where($db->quoteName('id') . ' = ' . (int)$id);
                    $db->setQuery($query);
                    $db->execute();
                }
            } catch (\Throwable $e) {}
        }

        $app->redirect('index.php?option=com_eatzy&view=kds');
    }

    /**
     * Update table reservation status
     */
    public function updateReservationStatus()
    {
        $app = Factory::getApplication();
        $id = (int)$this->input->get('id', 0);
        $status = trim($this->input->get('status', 'Confirmed'));

        if ($id > 0) {
            try {
                $db = $this->getDb();
                if ($db instanceof \PDO) {
                    $stmt = $db->prepare("UPDATE eatzy_reservations SET status = ? WHERE id = ?");
                    $stmt->execute([$status, $id]);
                } else {
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__eatzy_reservations'))
                        ->set($db->quoteName('status') . ' = ' . $db->quote($status))
                        ->where($db->quoteName('id') . ' = ' . (int)$id);
                    $db->setQuery($query);
                    $db->execute();
                }
            } catch (\Throwable $e) {}
        }

        $app->redirect('index.php?option=com_eatzy&view=reservations');
    }

    /**
     * Toggle menu item availability
     */
    public function toggleDish()
    {
        $app = Factory::getApplication();
        $id = (int)$this->input->get('id', 0);

        if ($id > 0) {
            try {
                $db = $this->getDb();
                if ($db instanceof \PDO) {
                    $stmt = $db->prepare("UPDATE eatzy_menu SET is_available = IF(is_available = 1, 0, 1) WHERE id = ?");
                    $stmt->execute([$id]);
                } else {
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__eatzy_menu'))
                        ->set($db->quoteName('is_available') . ' = 1 - ' . $db->quoteName('is_available'))
                        ->where($db->quoteName('id') . ' = ' . (int)$id);
                    $db->setQuery($query);
                    $db->execute();
                }
            } catch (\Throwable $e) {}
        }

        $app->redirect('index.php?option=com_eatzy&view=menu');
    }
}

