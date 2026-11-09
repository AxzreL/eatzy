<?php
namespace Eatzy\Component\Eatzy\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class BookModel extends BaseDatabaseModel
{
    public function getAvailableSlots()
    {
        return [
            'Lunch' => ['11:30 AM', '12:15 PM', '1:00 PM', '1:45 PM'],
            'Dinner' => ['5:30 PM', '6:15 PM', '7:00 PM', '7:45 PM', '8:30 PM', '9:15 PM']
        ];
    }

    public function getSeatingZones()
    {
        return [
            'Indoor Main Dining' => 'Air-conditioned dining lounge with cozy banquette seating.',
            'Al Fresco Garden Patio' => 'Open-air courtyard patio surrounded by lush ambient lighting.'
        ];
    }
}

