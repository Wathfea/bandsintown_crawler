<?php

namespace App\Domain\Crawlers\Bandsintown\Factory;

use App\Models\Event;
use App\Models\TicketCategory;

/**
 * Class TickethingEventCategoryFactory.
 *
 * @package App\Domain\Crawlers\Bandsintown\Factory
 */
class TickethingEventCategoryFactory
{
    /**
     * @param Event $event
     *
     * @return TicketCategory
     */
    public function createFromTickethingEvent(Event $event): TicketCategory
    {
        $ticketCategory = new TicketCategory();
        $ticketCategory->event_id = $event->id;
        $ticketCategory->name = ['en' => 'Default'];
        $ticketCategory->default = true;
        $ticketCategory->visible = false;

        return $ticketCategory;
    }
}
