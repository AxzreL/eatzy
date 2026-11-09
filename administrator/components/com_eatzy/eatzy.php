<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

require_once __DIR__ . '/src/Controller/DisplayController.php';
require_once __DIR__ . '/src/View/Kds/HtmlView.php';
require_once __DIR__ . '/src/View/Reservations/HtmlView.php';
require_once __DIR__ . '/src/View/Menu/HtmlView.php';

$app = Factory::getApplication();
$controller = new \Eatzy\Component\Eatzy\Administrator\Controller\DisplayController();
$task = $app->getInput()->get('task', 'display');
$controller->execute($task);
$controller->redirect();

