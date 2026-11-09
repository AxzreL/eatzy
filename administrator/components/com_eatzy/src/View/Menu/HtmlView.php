<?php
namespace Eatzy\Component\Eatzy\Administrator\View\Menu;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $dishes;

    public function display($tpl = null)
    {
        $dishes = [];
        try {
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__eatzy_menu'))
                ->order($db->quoteName('category') . ' ASC, ' . $db->quoteName('id') . ' ASC');
            $db->setQuery($query);
            $dishes = $db->loadObjectList();
        } catch (\Throwable $e) {
            try {
                $pdo = new \PDO("mysql:host=localhost;dbname=eatzy_db;charset=utf8mb4", "root", "", [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
                ]);
                $stmt = $pdo->query("SELECT * FROM eatzy_menu ORDER BY category ASC, id ASC");
                $dishes = $stmt->fetchAll();
            } catch (\Throwable $ex) {}
        }

        if (empty($dishes)) {
            $dishes = [
                (object)['id' => 1, 'name' => 'Smoked Angus Truffle Burger', 'category' => 'Mains', 'price' => 380.00, 'dietary_tags' => 'Chef Special', 'spice_level' => 1, 'is_special' => 1, 'is_available' => 1],
                (object)['id' => 2, 'name' => 'Double Smash Cheeseburger', 'category' => 'Mains', 'price' => 290.00, 'dietary_tags' => '', 'spice_level' => 0, 'is_special' => 0, 'is_available' => 1],
                (object)['id' => 3, 'name' => 'Creamy Pesto Fettuccine', 'category' => 'Mains', 'price' => 320.00, 'dietary_tags' => 'Vegetarian', 'spice_level' => 0, 'is_special' => 0, 'is_available' => 1]
            ];
        }

        $this->dishes = $dishes;
        return parent::display($tpl);
    }
}

