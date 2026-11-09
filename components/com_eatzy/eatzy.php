<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Require Site Controller
require_once __DIR__ . '/src/Controller/DisplayController.php';
require_once __DIR__ . '/src/Model/MenuModel.php';
require_once __DIR__ . '/src/Model/BookModel.php';
require_once __DIR__ . '/src/Model/TrackModel.php';
require_once __DIR__ . '/src/View/Menu/HtmlView.php';
require_once __DIR__ . '/src/View/Book/HtmlView.php';
require_once __DIR__ . '/src/View/Track/HtmlView.php';

$app = Factory::getApplication();
$controller = new \Eatzy\Component\Eatzy\Site\Controller\DisplayController();
$task = $app->getInput()->get('task', 'display');
$controller->execute($task);
$controller->redirect();

