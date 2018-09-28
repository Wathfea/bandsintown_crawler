<?php

namespace App\Domain\Crawlers\Bandsintown\Factory;

use App\Models\BandsintownEvent;
use Bandsintown\Response\Event;
use Bandsintown\Response\Offer;
use Illuminate\Support\Collection;

/**
 * Class BandsintownEventFactory.
 *
 * @package App\Domain\Crawlers\Bandsintown\Factory
 */
class BandsintownEventFactory
{
    /**
     * @param Event $event
     *
     * @return BandsintownEvent
     */
    public function createFromEventResponse(Event $event): BandsintownEvent
    {
        $offersArray = [];

        $venueArray = [
            'name' => $event->getVenue()->getName(),
            'latitude' => $event->getVenue()->getLatitude(),
            'longitude' => $event->getVenue()->getLongitude(),
            'city' => $event->getVenue()->getCity(),
            'region' => $event->getVenue()->getRegion(),
            'country' => $event->getVenue()->getCountry(),
        ];

        /** @var Offer $offer */
        foreach ($event->getOffers() as $offer) {
            $offersArray = [
                'type' => $offer->getType(),
                'url' => $offer->getUrl(),
                'status' => $offer->getStatus(),
            ];
        }

        $bandsInTownEvent = new BandsintownEvent();
        $bandsInTownEvent->id = $event->getId();
        $bandsInTownEvent->artist_id = $event->getArtistId();
        $bandsInTownEvent->url = $event->getUrl();
        $bandsInTownEvent->on_sale_datetime = $event->getOnSaleDatetime();
        $bandsInTownEvent->datetime = $event->getDatetime();
        $bandsInTownEvent->description = $event->getDescription();
        $bandsInTownEvent->venue = json_encode($venueArray);
        $bandsInTownEvent->offers = json_encode($offersArray);
        $bandsInTownEvent->lineup = json_encode($event->getLineup()[0]);

        return $bandsInTownEvent;
    }

    /**
     * @param Collection $events
     *
     * @return BandsintownEvent[]|Collection
     */
    public function createFromEventsResponse(Collection $events): Collection
    {
        return $events->map(function (Event $event) {
            return $this->createFromEventResponse($event);
        });
    }
}
