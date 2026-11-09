<?php
namespace Eatzy\Component\Eatzy\Site\View\Menu;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $categories;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->items = $model ? $model->getMenuItems() : [];

        $cats = [];
        foreach ($this->items as $item) {
            $cat = $item->category ?: 'Mains';
            if (!in_array($cat, $cats)) {
                $cats[] = $cat;
            }
        }
        $this->categories = $cats;

        return parent::display($tpl);
    }
}

