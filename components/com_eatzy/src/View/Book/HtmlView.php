<?php
namespace Eatzy\Component\Eatzy\Site\View\Book;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $slots;
    protected $zones;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->slots = $model ? $model->getAvailableSlots() : [];
        $this->zones = $model ? $model->getSeatingZones() : [];

        return parent::display($tpl);
    }
}

