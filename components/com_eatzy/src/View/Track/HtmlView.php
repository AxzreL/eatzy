<?php
namespace Eatzy\Component\Eatzy\Site\View\Track;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $order;
    protected $orderNumber;

    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $this->orderNumber = trim($app->getInput()->get('id', ''));
        $model = $this->getModel();
        $this->order = $model ? $model->getOrder($this->orderNumber) : null;

        return parent::display($tpl);
    }
}

